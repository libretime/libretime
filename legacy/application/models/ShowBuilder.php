<?php

class Application_Model_ShowBuilder
{
    private $timezone;

    // in UTC timezone
    private $startDT;
    // in UTC timezone
    private $endDT;

    private $user;
    private $opts;

    private $pos;
    private $contentDT;
    private $epoch_now;
    private $currentShow;
    private $currentShowId;

    private $showInstances = [];

    private $defaultRowArray = [
        'header' => false,
        'footer' => false,
        'empty' => false,
        'allowed' => false,
        'linked_allowed' => true,
        'id' => 0,
        'instance' => '',
        'starts' => '',
        'ends' => '',
        'runtime' => '',
        'title' => '',
        'creator' => '',
        'album' => '',
        'timestamp' => null,
        'cuein' => '',
        'cueout' => '',
        'fadein' => '',
        'fadeout' => '',
        'image' => false,
        'mime' => null,
        'color' => '', // in hex without the '#' sign.
        'backgroundColor' => '', // in hex without the '#' sign.
    ];

    /*
     * @param DateTime $p_startsDT
     * @param DateTime $p_endsDT
     */
    public function __construct($p_startDT, $p_endDT, $p_opts)
    {
        $this->startDT = $p_startDT;
        $this->endDT = $p_endDT;
        $this->timezone = Application_Model_Preference::GetUserTimezone();
        $this->user = Application_Model_User::getCurrentUser();
        $this->opts = $p_opts;
        $this->epoch_now = floatval(microtime(true));
        $this->currentShow = false;
    }

    private function getUsersShows()
    {
        $shows = [];

        $host_shows = CcShowHostsQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->filterByDbHost($this->user->getId())
            ->find();

        foreach ($host_shows as $host_show) {
            $shows[] = $host_show->getDbShow();
        }

        return $shows;
    }

    // check to see if this row should be editable by the user.
    private function isAllowed($p_item, &$row)
    {
        // cannot schedule in a recorded show.
        if (intval($p_item['si_record']) === 1) {
            return;
        }

        if ($this->currentShow) {
            $this->currentShowId = $p_item['show_id'];
        }

        /* If any linked show instance is currently playing
         * we have to disable editing, or else the times
         * will not make sense for shows scheduled in the future
         */
        if ($p_item['linked'] && $p_item['show_id'] == $this->currentShowId) {
            $row['linked_allowed'] = false;
        }

        if ($this->user->canSchedule($p_item['show_id']) == true) {
            $row['allowed'] = true;
        }
    }

    private function getItemColor($p_item, &$row)
    {
        $defaultColor = 'ffffff';
        $defaultBackground = DEFAULT_SHOW_COLOR;

        $color = $p_item['show_color'];
        if ($color === '') {
            $color = $defaultColor;
        }
        $backgroundColor = $p_item['show_background_color'];
        if ($backgroundColor === '') {
            $backgroundColor = $defaultBackground;
        }

        $row['color'] = $color;
        $row['backgroundColor'] = $backgroundColor;
    }

    // information about whether a track is inside|boundary|outside a show.
    private function getItemStatus($p_item, &$row)
    {
        $row['status'] = intval($p_item['playout_status']);
    }

    private function getRowTimestamp($p_item, &$row)
    {
        if (is_null($p_item['si_last_scheduled'])) {
            $ts = 0;
        } else {
            $dt = new DateTime($p_item['si_last_scheduled'], new DateTimeZone('UTC'));
            $ts = intval($dt->format('U'));
        }
        $row['timestamp'] = $ts;
    }

    /*
     * marks a row's status.
     * 0 = past
     * 1 = current
     * 2 = future
     * TODO : change all of the above to real constants -- RG
     */
    private function getScheduledStatus($p_epochItemStart, $p_epochItemEnd, &$row)
    {
        if (
            $row['footer'] === true && $this->epoch_now > $p_epochItemStart
            && $this->epoch_now > $p_epochItemEnd
        ) {
            $row['scheduled'] = 0;
        } elseif ($row['footer'] === true && $this->epoch_now < $p_epochItemEnd) {
            $row['scheduled'] = 2;
        } elseif ($row['header'] === true && $this->epoch_now >= $p_epochItemStart) {
            $row['scheduled'] = 0;
        } elseif ($row['header'] === true && $this->epoch_now < $p_epochItemEnd) {
            $row['scheduled'] = 2;
        }

        // item is in the past.
        elseif ($this->epoch_now > $p_epochItemEnd) {
            $row['scheduled'] = 0;
        }

        // item is the currently scheduled item.
        elseif ($this->epoch_now >= $p_epochItemStart && $this->epoch_now < $p_epochItemEnd) {
            $row['scheduled'] = 1;
            // how many seconds the view should wait to redraw itself.
            $row['refresh'] = $p_epochItemEnd - $this->epoch_now;
        }

        // item is in the future.
        elseif ($this->epoch_now < $p_epochItemStart) {
            $row['scheduled'] = 2;
        } else {
            Logging::warn('No-op? is this what should happen...printing
                debug just in case');
            $d = [
                '$p_epochItemStart' => $p_epochItemStart,
                '$p_epochItemEnd' => $p_epochItemEnd,
                '$row' => $row,
            ];
            Logging::warn($d);
        }
    }

    private function makeHeaderRow($p_item)
    {
        $row = $this->defaultRowArray;
        // $this->isAllowed($p_item, $row);
        $this->getRowTimestamp($p_item, $row);
        $this->getItemColor($p_item, $row);

        $showStartDT = new DateTime($p_item['si_starts'], new DateTimeZone('UTC'));
        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $startsEpoch = floatval($showStartDT->format('U.u'));
        $showEndDT = new DateTime($p_item['si_ends'], new DateTimeZone('UTC'));
        $showEndDT->setTimezone(new DateTimeZone($this->timezone));
        $endsEpoch = floatval($showEndDT->format('U.u'));

        // is a rebroadcast show
        if (intval($p_item['si_rebroadcast']) === 1) {
            $row['rebroadcast'] = true;

            $parentInstance = CcShowInstancesQuery::create()->findPk($p_item['parent_show']);
            $name = $parentInstance->getCcShow()->getDbName();
            $dt = $parentInstance->getDbStarts(null);
            $dt->setTimezone(new DateTimeZone($this->timezone));
            $time = $dt->format('Y-m-d H:i');

            $row['rebroadcast_title'] = sprintf(_('Rebroadcast of %s from %s'), $name, $time);
        } elseif (intval($p_item['si_record']) === 1) {
            $row['record'] = true;
        }

        if ($startsEpoch < $this->epoch_now && $endsEpoch > $this->epoch_now) {
            $row['currentShow'] = true;
            $this->currentShow = true;
        } else {
            $this->currentShow = false;
        }

        $this->isAllowed($p_item, $row);

        $row['header'] = true;
        $row['starts'] = $showStartDT->format('Y-m-d H:i');
        $row['startDate'] = $showStartDT->format('Y-m-d');
        $row['startTime'] = $showStartDT->format('H:i');
        $row['refresh'] = floatval($showStartDT->format('U.u')) - $this->epoch_now;
        $row['ends'] = $showEndDT->format('Y-m-d H:i');
        $row['endDate'] = $showEndDT->format('Y-m-d');
        $row['endTime'] = $showEndDT->format('H:i');
        $row['duration'] = floatval($showEndDT->format('U.u')) - floatval($showStartDT->format('U.u'));
        if ($p_item['show_name']) {
           $row['title'] = htmlspecialchars($p_item['show_name']);
        }
        $row['instance'] = intval($p_item['si_id']);
        $row['image'] = '';

        $this->getScheduledStatus($startsEpoch, $endsEpoch, $row);

        $this->contentDT = $showStartDT;

        return $row;
    }

    private function makeScheduledItemRow($p_item)
    {
        $row = $this->defaultRowArray;

        if (isset($p_item['sched_starts'])) {
            $schedStartDT = new DateTime(
                $p_item['sched_starts'],
                new DateTimeZone('UTC')
            );
            $schedStartDT->setTimezone(new DateTimeZone($this->timezone));
            $schedEndDT = new DateTime(
                $p_item['sched_ends'],
                new DateTimeZone('UTC')
            );
            $schedEndDT->setTimezone(new DateTimeZone($this->timezone));
            $showEndDT = new DateTime($p_item['si_ends'], new DateTimeZone('UTC'));

            $this->getItemStatus($p_item, $row);

            $startsEpoch = floatval($schedStartDT->format('U.u'));
            $endsEpoch = floatval($schedEndDT->format('U.u'));
            $showEndEpoch = floatval($showEndDT->format('U.u'));

            // don't want an overbooked item to stay marked as current.
            $this->getScheduledStatus($startsEpoch, min($endsEpoch, $showEndEpoch), $row);

            $row['id'] = intval($p_item['sched_id']);
            $row['image'] = $p_item['file_exists'];
            $row['instance'] = intval($p_item['si_id']);
            $row['starts'] = $schedStartDT->format('H:i:s');
            $row['ends'] = $schedEndDT->format('H:i:s');

            $cue_out = Application_Common_DateHelper::playlistTimeToSeconds($p_item['cue_out']);
            $cue_in = Application_Common_DateHelper::playlistTimeToSeconds($p_item['cue_in']);

            $run_time = $cue_out - $cue_in;

            $formatter = new LengthFormatter(Application_Common_DateHelper::secondsToPlaylistTime($run_time));
            $row['runtime'] = $formatter->format();

            if ($p_item['file_track_title']) {
               $row['title'] = htmlspecialchars($p_item['file_track_title']);
            }
            if ($p_item['file_artist_name']) {
               $row['creator'] = htmlspecialchars($p_item['file_artist_name']);
            }
            if ($p_item['file_album_title']) {
               $row['album'] = htmlspecialchars($p_item['file_album_title']);
            }

            $row['cuein'] = $p_item['cue_in'];
            $row['cueout'] = $p_item['cue_out'];
            $row['fadein'] = round(substr($p_item['fade_in'], 6), 6);
            $row['fadeout'] = round(substr($p_item['fade_out'], 6), 6);
            $row['mime'] = $p_item['file_mime'];

            $row['pos'] = $this->pos++;

            $this->contentDT = $schedEndDT;
        }
        // show is empty or is a special kind of show (recording etc)
        elseif (intval($p_item['si_record']) === 1) {
            $row['record'] = true;
            $row['instance'] = intval($p_item['si_id']);

            $showStartDT = new DateTime($p_item['si_starts'], new DateTimeZone('UTC'));
            $showEndDT = new DateTime($p_item['si_ends'], new DateTimeZone('UTC'));

            $startsEpoch = floatval($showStartDT->format('U.u'));
            $endsEpoch = floatval($showEndDT->format('U.u'));

            $this->getScheduledStatus($startsEpoch, $endsEpoch, $row);
        } else {
            $row['empty'] = true;
            $row['id'] = 0;
            $row['instance'] = intval($p_item['si_id']);
        }

        if (intval($p_item['si_rebroadcast']) === 1) {
            $row['rebroadcast'] = true;
        }

        if ($this->currentShow === true) {
            $row['currentShow'] = true;
        }

        $this->getItemColor($p_item, $row);
        $this->getRowTimestamp($p_item, $row);
        $this->isAllowed($p_item, $row);

        return $row;
    }

    private function makeFooterRow($p_item)
    {
        $row = $this->defaultRowArray;
        $row['footer'] = true;
        $row['instance'] = intval($p_item['si_id']);
        $this->getRowTimestamp($p_item, $row);

        $showEndDT = new DateTime($p_item['si_ends'], new DateTimeZone('UTC'));
        $contentDT = $this->contentDT;

        $runtime = bcsub($contentDT->format('U.u'), $showEndDT->format('U.u'), 6);
        $row['runtime'] = $runtime;

        $timeFilled = new TimeFilledFormatter($runtime);
        $row['fRuntime'] = $timeFilled->format();

        $showStartDT = new DateTime($p_item['si_starts'], new DateTimeZone('UTC'));
        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $startsEpoch = floatval($showStartDT->format('U.u'));
        $showEndDT = new DateTime($p_item['si_ends'], new DateTimeZone('UTC'));
        $showEndDT->setTimezone(new DateTimeZone($this->timezone));
        $endsEpoch = floatval($showEndDT->format('U.u'));

        $row['refresh'] = floatval($showEndDT->format('U.u')) - $this->epoch_now;

        if ($this->currentShow === true) {
            $row['currentShow'] = true;
        }

        $this->getScheduledStatus($startsEpoch, $endsEpoch, $row);
        $this->isAllowed($p_item, $row);

        if (intval($p_item['si_record']) === 1) {
            $row['record'] = true;
        }

        return $row;
    }

    /*
     * @param int $timestamp Unix timestamp in seconds.
     *
     * @return boolean whether the schedule in the show builder's range has
     * been updated.
     *
     */
    public function hasBeenUpdatedSince($timestamp, $instances)
    {
        $outdated = false;
        $shows = Application_Model_Show::getShows($this->startDT, $this->endDT);

        $include = [];
        if ($this->opts['showFilter'] !== 0) {
            $include[] = $this->opts['showFilter'];
        } elseif ($this->opts['myShows'] === 1) {
            $include = $this->getUsersShows();
        }

        $currentInstances = [];

        foreach ($shows as $show) {
            if (empty($include) || in_array($show['show_id'], $include)) {
                $currentInstances[] = $show['instance_id'];

                if (isset($show['last_scheduled'])) {
                    $dt = new DateTime(
                        $show['last_scheduled'],
                        new DateTimeZone('UTC')
                    );
                } else {
                    $dt = new DateTime(
                        $show['created'],
                        new DateTimeZone('UTC')
                    );
                }

                // check if any of the shows have a more recent timestamp.
                $showTimeStamp = intval($dt->format('U'));
                if ($timestamp < $showTimeStamp) {
                    $outdated = true;

                    break;
                }
            }
        }

        // see if the displayed show instances have changed. (deleted,
        // empty schedule etc)
        if (
            $outdated === false && count($instances)
            !== count($currentInstances)
        ) {
            Logging::debug('show instances have changed.');
            $outdated = true;
        }

        return $outdated;
    }

    public function getItems()
    {
        $current_id = -1;
        $display_items = [];

        $shows = [];
        $showInstance = [];
        if ($this->opts['myShows'] === 1) {
            $shows = $this->getUsersShows();
        } elseif ($this->opts['showFilter'] !== 0) {
            $shows[] = $this->opts['showFilter'];
        } elseif ($this->opts['showInstanceFilter'] !== 0) {
            $showInstance[] = $this->opts['showInstanceFilter'];
        }

        $scheduled_items = Application_Model_Schedule::GetScheduleDetailItems(
            $this->startDT,
            $this->endDT,
            $shows,
            $showInstance
        );

        for ($i = 0, $rows = count($scheduled_items); $i < $rows; ++$i) {
            $item = $scheduled_items[$i];

            // don't send back data for filler rows.
            if (
                isset($item['playout_status'])
                && $item['playout_status'] < 0
            ) {
                continue;
            }

            // make a header row.
            if ($current_id !== $item['si_id']) {
                // make a footer row.
                if ($current_id !== -1) {
                    // pass in the previous row as it's the last row for
                    // the previous show.
                    $display_items[] = $this->makeFooterRow(
                        $scheduled_items[$i - 1]
                    );
                }

                $display_items[] = $this->makeHeaderRow($item);

                $current_id = $item['si_id'];

                $this->pos = 1;
            }

            // make a normal data row.
            $row = $this->makeScheduledItemRow($item);
            // don't display the empty rows.
            if (isset($row)) {
                $display_items[] = $row;
            }

            if (
                $current_id !== -1
                && !in_array($current_id, $this->showInstances)
            ) {
                $this->showInstances[] = $current_id;
            }
        }

        // make the last footer if there were any scheduled items.
        if (count($scheduled_items) > 0) {
            $display_items[] = $this->makeFooterRow($scheduled_items[count($scheduled_items) - 1]);
        }

        return [
            'schedule' => $display_items,
            'showInstances' => $this->showInstances,
        ];
    }
}
