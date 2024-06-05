<?php

final class Application_Model_Scheduler
{
    private $con;
    private $fileInfo = [
        'id' => '',
        'cliplength' => '',
        'cuein' => '00:00:00',
        'cueout' => '00:00:00',
        'fadein' => '00:00:00',
        'fadeout' => '00:00:00',
        'sched_id' => null,
        'type' => 0, // default type of '0' to represent files. type '1' represents a webstream
    ];

    private $epochNow;
    private $nowDT;
    private $user;

    private $crossfadeDuration;
    private $applyCrossfades = true;

    private $checkUserPermissions = true;

    public function __construct($checkUserPermissions = true)
    {
        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        // subtracting one because sometimes when we cancel a track, we set its end time
        // to epochNow and then send the new schedule to pypo. Sometimes the currently cancelled
        // track can still be included in the new schedule because it may have a few ms left to play.
        // subtracting 1 second from epochNow resolves this issue.
        $this->epochNow = microtime(true) - 1;
        $this->nowDT = DateTime::createFromFormat('U.u', $this->epochNow, new DateTimeZone('UTC'));

        if ($this->nowDT === false) {
            // DateTime::createFromFormat does not support millisecond string formatting in PHP 5.3.2 (Ubuntu 10.04).
            // In PHP 5.3.3 (Ubuntu 10.10), this has been fixed.
            $this->nowDT = DateTime::createFromFormat('U', time(), new DateTimeZone('UTC'));
        }

        $this->setCheckUserPermissions($checkUserPermissions);

        if ($this->checkUserPermissions) {
            $this->user = Application_Model_User::getCurrentUser();
        }

        $this->crossfadeDuration = Application_Model_Preference::GetDefaultCrossfadeDuration();
    }

    public function setCheckUserPermissions($value)
    {
        $this->checkUserPermissions = $value;
    }

    private function validateItemMove($itemsToMove, $destination)
    {
        $destinationInstanceId = $destination['instance'];
        $destinationCcShowInstance = CcShowInstancesQuery::create()
            ->findPk($destinationInstanceId);
        $isDestinationLinked = $destinationCcShowInstance->getCcShow()->isLinked();

        foreach ($itemsToMove as $itemToMove) {
            $sourceInstanceId = $itemToMove['instance'];
            $ccShowInstance = CcShowInstancesQuery::create()
                ->findPk($sourceInstanceId);

            // does the item being moved belong to a linked show
            $isSourceLinked = $ccShowInstance->getCcShow()->isLinked();

            if ($isDestinationLinked && !$isSourceLinked) {
                throw new Exception('Cannot move items into linked shows');
            }
            if (!$isDestinationLinked && $isSourceLinked) {
                throw new Exception('Cannot move items out of linked shows');
            }
            if ($isSourceLinked && $sourceInstanceId != $destinationInstanceId) {
                throw new Exception(_('Cannot move items out of linked shows'));
            }
        }
    }

    /*
     * make sure any incoming requests for scheduling are legit.
    *
    * @param array $items, an array containing pks of cc_schedule items.
    */
    private function validateRequest($items, $addRemoveAction = false, $cancelShow = false)
    {
        // $items is where tracks get inserted (they are schedule locations)

        $nowEpoch = floatval($this->nowDT->format('U.u'));

        $schedInfo = [];
        $instanceInfo = [];

        for ($i = 0; $i < count($items); ++$i) {
            $id = $items[$i]['id'];

            // could be added to the beginning of a show, which sends id = 0;
            if ($id > 0) {
                // schedule_id of where we are inserting after?
                $schedInfo[$id] = $items[$i]['instance'];
            }

            // format is instance_id => timestamp
            $instanceInfo[$items[$i]['instance']] = $items[$i]['timestamp'];
        }

        if (count($instanceInfo) === 0) {
            throw new Exception('Invalid Request.');
        }

        $schedIds = [];
        if (count($schedInfo) > 0) {
            $schedIds = array_keys($schedInfo);
        }
        $schedItems = CcScheduleQuery::create()->findPKs($schedIds, $this->con);
        $instanceIds = array_keys($instanceInfo);
        $showInstances = CcShowInstancesQuery::create()->findPKs($instanceIds, $this->con);

        // an item has been deleted
        if (count($schedIds) !== count($schedItems)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date! (sched mismatch)"));
        }

        // a show has been deleted
        if (count($instanceIds) !== count($showInstances)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date! (instance mismatch)"));
        }

        foreach ($schedItems as $schedItem) {
            $id = $schedItem->getDbId();
            $instance = $schedItem->getCcShowInstances($this->con);

            if (intval($schedInfo[$id]) !== $instance->getDbId()) {
                throw new OutDatedScheduleException(_("The schedule you're viewing is out of date!"));
            }
        }

        foreach ($showInstances as $instance) {
            $id = $instance->getDbId();
            $show = $instance->getCcShow($this->con);

            if ($this->checkUserPermissions && $this->user->canSchedule($show->getDbId()) === false) {
                throw new Exception(sprintf(_('You are not allowed to schedule show %s.'), $show->getDbName()));
            }

            if ($instance->getDbRecord()) {
                throw new Exception(_('You cannot add files to recording shows.'));
            }

            $showEndEpoch = floatval($instance->getDbEnds('U.u'));

            if ($showEndEpoch < $nowEpoch) {
                throw new OutDatedScheduleException(sprintf(_('The show %s is over and cannot be scheduled.'), $show->getDbName()));
            }

            $ts = intval($instanceInfo[$id]);
            $lastSchedTs = intval($instance->getDbLastScheduled('U')) ?: 0;
            if ($ts < $lastSchedTs) {
                Logging::info("ts {$ts} last sched {$lastSchedTs}");

                throw new OutDatedScheduleException(sprintf(_('The show %s has been previously updated!'), $show->getDbName()));
            }

            /*
             * Does the afterItem belong to a show that is linked AND
             * currently playing?
             * If yes, throw an exception
             * unless it is a cancel show action then we don't check because otherwise
             * ongoing linked shows can't be cancelled
             */
            if ($addRemoveAction && !$cancelShow) {
                $ccShow = $instance->getCcShow();
                if ($ccShow->isLinked()) {
                    // get all the linked shows instances and check if
                    // any of them are currently playing
                    $ccShowInstances = $ccShow->getCcShowInstancess();
                    $timeNowUTC = gmdate(DEFAULT_TIMESTAMP_FORMAT);
                    foreach ($ccShowInstances as $ccShowInstance) {
                        if (
                            $ccShowInstance->getDbStarts() <= $timeNowUTC
                            && $ccShowInstance->getDbEnds() > $timeNowUTC
                        ) {
                            throw new Exception(_('Content in linked shows cannot be changed while on air!'));
                        }
                    }
                }
            }
        }
    }

    private function validateMediaItems($mediaItems)
    {
        foreach ($mediaItems as $mediaItem) {
            $id = $mediaItem['id'];
            if ($mediaItem['type'] === 'playlist') {
                $playlist = new Application_Model_Playlist($id, $this->con);
                if ($playlist->containsMissingFiles()) {
                    throw new Exception(_('Cannot schedule a playlist that contains missing files.'));
                }
            }
        }

        return true;
    }

    /*
     * @param $id
     * @param $type
     * @param $show
     *
     * @return $files
     */
    private function retrieveMediaFiles($id, $type, $show)
    {
        // if there is a show we need to set a show limit to pass to smart blocks in case they use time remaining
        $showInstance = new Application_Model_ShowInstance($show);
        $showLimit = $showInstance->getSecondsRemaining();
        $originalShowLimit = $showLimit;

        $files = [];
        if ($type === 'audioclip') {
            $file = CcFilesQuery::create()->findPK($id, $this->con);

            if (is_null($file) || !$file->visible()) {
                throw new Exception(_('A selected File does not exist!'));
            }
            $data = $this->fileInfo;
            $data['id'] = $id;

            $cuein = Application_Common_DateHelper::playlistTimeToSeconds($file->getDbCuein());
            $cueout = Application_Common_DateHelper::playlistTimeToSeconds($file->getDbCueout());
            $row_length = Application_Common_DateHelper::secondsToPlaylistTime($cueout - $cuein);

            $data['cliplength'] = $row_length;

            $data['cuein'] = $file->getDbCuein();
            $data['cueout'] = $file->getDbCueout();

            // fade is in format SS.uuuuuu
            $data['fadein'] = Application_Model_Preference::GetDefaultFadeIn();
            $data['fadeout'] = Application_Model_Preference::GetDefaultFadeOut();

            $files[] = $data;
        } elseif ($type === 'playlist') {
            $pl = new Application_Model_Playlist($id);
            $contents = $pl->getContents();
            // because the time remaining is not updated until after the schedule inserts we need to track it for
            // the entire add vs. querying on the smartblock level
            foreach ($contents as $plItem) {
                if ($plItem['type'] == 0) {
                    $data['id'] = $plItem['item_id'];
                    $data['cliplength'] = $plItem['length'];
                    $data['cuein'] = $plItem['cuein'];
                    $data['cueout'] = $plItem['cueout'];
                    $data['fadein'] = $plItem['fadein'];
                    $data['fadeout'] = $plItem['fadeout'];
                    $data['type'] = 0;
                    $files[] = $data;
                } elseif ($plItem['type'] == 1) {
                    $data['id'] = $plItem['item_id'];
                    $data['cliplength'] = $plItem['length'];
                    $data['cuein'] = $plItem['cuein'];
                    $data['cueout'] = $plItem['cueout'];
                    $data['fadein'] = '00.500000'; // $plItem['fadein'];
                    $data['fadeout'] = '00.500000'; // $plItem['fadeout'];
                    $data['type'] = 1;
                    $files[] = $data;
                } elseif ($plItem['type'] == 2) {
                    // if it's a block
                    $bl = new Application_Model_Block($plItem['item_id']);
                    if ($bl->isStatic()) {
                        foreach ($bl->getContents() as $track) {
                            $data['id'] = $track['item_id'];
                            $data['cliplength'] = $track['length'];
                            $data['cuein'] = $track['cuein'];
                            $data['cueout'] = $track['cueout'];
                            $data['fadein'] = $track['fadein'];
                            $data['fadeout'] = $track['fadeout'];
                            $data['type'] = 0;
                            $files[] = $data;
                        }
                    } else {
                        $defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
                        $defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();
                        $dynamicFiles = $bl->getListOfFilesUnderLimit($showLimit);
                        foreach ($dynamicFiles as $f) {
                            $fileId = $f['id'];
                            $file = CcFilesQuery::create()->findPk($fileId);
                            if (isset($file) && $file->visible()) {
                                $data['id'] = $file->getDbId();
                                $data['cuein'] = $file->getDbCuein();
                                $data['cueout'] = $file->getDbCueout();

                                $cuein = Application_Common_DateHelper::calculateLengthInSeconds($data['cuein']);
                                $cueout = Application_Common_DateHelper::calculateLengthInSeconds($data['cueout']);
                                $data['cliplength'] = Application_Common_DateHelper::secondsToPlaylistTime($cueout - $cuein);

                                // fade is in format SS.uuuuuu
                                $data['fadein'] = $defaultFadeIn;
                                $data['fadeout'] = $defaultFadeOut;

                                $data['type'] = 0;
                                $files[] = $data;
                            }
                        }
                    }
                }
                // if this is a playlist it might contain multiple time remaining smart blocks
                // since the schedule isn't updated until after this insert we need to keep tally
                $showLimit = $originalShowLimit - $this->timeLengthOfFiles($files);
            }
        } elseif ($type == 'stream') {
            // need to return
            $stream = CcWebstreamQuery::create()->findPK($id, $this->con);

            if (is_null($stream) /* || !$file->visible() */) {
                throw new Exception(_('A selected File does not exist!'));
            }
            $data = $this->fileInfo;
            $data['id'] = $id;
            $data['cliplength'] = $stream->getDbLength();
            $data['cueout'] = $stream->getDbLength();
            $data['type'] = 1;

            // fade is in format SS.uuuuuu
            $data['fadein'] = Application_Model_Preference::GetDefaultFadeIn();
            $data['fadeout'] = Application_Model_Preference::GetDefaultFadeOut();

            $files[] = $data;
        } elseif ($type == 'block') {
            $bl = new Application_Model_Block($id);
            if ($bl->isStatic()) {
                foreach ($bl->getContents() as $track) {
                    $data['id'] = $track['item_id'];
                    $data['cliplength'] = $track['length'];
                    $data['cuein'] = $track['cuein'];
                    $data['cueout'] = $track['cueout'];
                    $data['fadein'] = $track['fadein'];
                    $data['fadeout'] = $track['fadeout'];
                    $data['type'] = 0;
                    $files[] = $data;
                }
            } else {
                $defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
                $defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();
                $dynamicFiles = $bl->getListOfFilesUnderLimit($showLimit);
                foreach ($dynamicFiles as $f) {
                    $fileId = $f['id'];
                    $file = CcFilesQuery::create()->findPk($fileId);
                    if (isset($file) && $file->visible()) {
                        $data['id'] = $file->getDbId();
                        $data['cuein'] = $file->getDbCuein();
                        $data['cueout'] = $file->getDbCueout();

                        $cuein = Application_Common_DateHelper::calculateLengthInSeconds($data['cuein']);
                        $cueout = Application_Common_DateHelper::calculateLengthInSeconds($data['cueout']);
                        $data['cliplength'] = Application_Common_DateHelper::secondsToPlaylistTime($cueout - $cuein);

                        // fade is in format SS.uuuuuu
                        $data['fadein'] = $defaultFadeIn;
                        $data['fadeout'] = $defaultFadeOut;

                        $data['type'] = 0;
                        $files[] = $data;
                    }
                }
            }
        }

        return $files;
    }

    /*
     * @param DateTime startDT in UTC
    *  @param string duration
    *      in format H:i:s.u (could be more that 24 hours)
    *
    * @return DateTime endDT in UTC
    */
    private function findTimeDifference($p_startDT, $p_seconds)
    {
        $startEpoch = $p_startDT->format('U.u');

        // add two float numbers to 6 subsecond precision
        // DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $newEpoch = bcsub($startEpoch, (string) $p_seconds, 6);

        $dt = DateTime::createFromFormat('U.u', $newEpoch, new DateTimeZone('UTC'));

        if ($dt === false) {
            // PHP 5.3.2 problem
            $dt = DateTime::createFromFormat('U', intval($newEpoch), new DateTimeZone('UTC'));
        }

        return $dt;
    }

    private function findTimeDifference2($p_startDT, $p_endDT)
    {
        $startEpoch = $p_startDT->format('U.u');
        $endEpoch = $p_endDT->format('U.u');

        // add two float numbers to 6 subsecond precision
        // DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $newEpoch = bcsub($endEpoch, (string) $startEpoch, 6);

        $dt = DateTime::createFromFormat('U.u', $newEpoch, new DateTimeZone('UTC'));

        if ($dt === false) {
            // PHP 5.3.2 problem
            $dt = DateTime::createFromFormat('U', intval($newEpoch), new DateTimeZone('UTC'));
        }

        return $dt;
    }

    /*
     * @param DateTime startDT in UTC
     * @param string duration
     *      in format H:i:s.u (could be more that 24 hours)
     *
     * @return DateTime endDT in UTC
     */
    private function findEndTime($p_startDT, $p_duration)
    {
        $startEpoch = $p_startDT->format('U.u');
        $durationSeconds = Application_Common_DateHelper::playlistTimeToSeconds($p_duration);

        // add two float numbers to 6 subsecond precision
        // DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $endEpoch = bcadd($startEpoch, (string) $durationSeconds, 6);

        $dt = DateTime::createFromFormat('U.u', $endEpoch, new DateTimeZone('UTC'));

        if ($dt === false) {
            // PHP 5.3.2 problem
            $dt = DateTime::createFromFormat('U', intval($endEpoch), new DateTimeZone('UTC'));
        }

        return $dt;
    }

    private function findNextStartTime($DT, $instanceId)
    {
        // TODO: there is at least one case where this function creates a filler block with
        //       an incorrect length; should keep an eye on it
        $sEpoch = $DT->format('U.u');
        $nEpoch = $this->epochNow;

        // check for if the show has started.
        if (bccomp($nEpoch, $sEpoch, 6) === 1) {
            $this->applyCrossfades = false;
            // need some kind of placeholder for cc_schedule.
            // playout_status will be -1.
            $nextDT = $this->nowDT;

            $length = bcsub($nEpoch, $sEpoch, 6);
            $cliplength = Application_Common_DateHelper::secondsToPlaylistTime($length);

            // fillers are for only storing a chunk of time space that has already passed.
            $filler = new CcSchedule();
            $filler->setDbStarts($DT)
                ->setDbEnds($this->nowDT)
                ->setDbClipLength($cliplength)
                ->setDbCueIn('00:00:00')
                ->setDbCueOut('00:00:00')
                ->setDbPlayoutStatus(-1)
                ->setDbInstanceId($instanceId)
                ->save($this->con);
        } else {
            $nextDT = $DT;
        }

        return $nextDT;
    }

    /*
     * @param int $showInstance
     *   This function recalculates the start/end times of items in a gapless show to
     *   account for crossfade durations.
     */
    private function calculateCrossfades($instanceId)
    {
        Logging::info('adjusting start, end times of scheduled items to account for crossfades show instance #' . $instanceId);
        $instance = CcShowInstancesQuery::create()->findPk($instanceId);

        if (is_null($instance)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date!"));
        }

        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($instanceId)
            ->orderByDbStarts()
            ->find($this->con);

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $itemStartDT = $instance->getDbStarts(null);
        foreach ($schedule as $item) {
            $itemEndDT = $this->findEndTime($itemStartDT, $item->getDbClipLength());
            // If the track has already ended, don't change it.
            if ($itemEndDT < $now) {
                $itemStartDT = $itemEndDT;

                continue;
            }
            $item->setDbStarts($itemStartDT)
                ->setDbEnds($itemEndDT)
                ->save($this->con);
            $itemStartDT = $this->findTimeDifference($itemEndDT, $this->crossfadeDuration);
        }
    }

    /*
     * @param int $showInstance
     * @param array $exclude
     *   ids of sched items to remove from the calculation.
     *   This function squeezes all items of a show together so that
     *   there are no gaps between them.
     */
    public function removeGaps($showInstance, $exclude = null)
    {
        Logging::info('removing gaps from show instance #' . $showInstance);

        $instance = CcShowInstancesQuery::create()->findPk($showInstance, $this->con);
        if (is_null($instance)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date!"));
        }

        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($showInstance)
            ->filterByDbId($exclude, Criteria::NOT_IN)
            ->orderByDbStarts()
            ->find($this->con);

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $itemStartDT = $instance->getDbStarts(null);
        foreach ($schedule as $item) {
            $itemEndDT = $this->findEndTime($itemStartDT, $item->getDbClipLength());
            // If the track has already ended, don't change it.
            if ($itemEndDT < $now) {
                $itemStartDT = $itemEndDT;

                continue;
            }

            $item->setDbStarts($itemStartDT)
                ->setDbEnds($itemEndDT);

            $itemStartDT = $itemEndDT;
        }

        $schedule->save($this->con);
    }

    /** Temporary hack to copy the track cue in, out, and length from the cc_files table to fix
     *  incorrect track lengths (RKTN-260).
     *
     * @param mixed      $showInstance
     * @param null|mixed $exclude
     */
    public function removeGaps2($showInstance, $exclude = null)
    {
        $instance = CcShowInstancesQuery::create()->findPK($showInstance, $this->con);
        if (is_null($instance)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date!"));
        }

        $itemStartDT = $instance->getDbStarts(null);

        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($showInstance)
            ->filterByDbId($exclude, Criteria::NOT_IN)
            ->orderByDbStarts()
            ->find($this->con);

        foreach ($schedule as $item) {
            // START OF TIME RECALC HACK

            // TODO: Copy the cue in, cue out, and track length from the cc_files table
            $file = $item->getCcFiles($this->con);
            if (!$file) {
                continue;
            }
            $item->setDbCueIn($file->getDbCueIn());
            $item->setDbCueOut($file->getDbCueOut());

            $cueOut = new DateTime($file->getDbCueOut());
            $cueIn = new DateTime($file->getDbCueIn());
            $clipLength = $this->findTimeDifference2($cueIn, $cueOut);

            // The clip length is supposed to be cue out - cue in:
            // FIXME: How do we correctly do time arithmetic in PHP without losing the millseconds?
            $item->setDbClipLength($clipLength->format(DEFAULT_INTERVAL_FORMAT));
            $item->save($this->con);
            // Ensure we don't get cached results
            CcSchedulePeer::clearInstancePool();
            // END OF TIME RECALC HACK

            $itemEndDT = $this->findEndTime($itemStartDT, $item->getDbClipLength());
            $item->setDbStarts($itemStartDT)
                ->setDbEnds($itemEndDT)
                ->save($this->con);
            $itemStartDT = $this->findTimeDifference($itemEndDT, $this->crossfadeDuration);
        }

        $instance->updateDbTimeFilled($this->con); // FIXME: TIME RECALC HACK (Albert)

        $schedule->save($this->con);
    }

    /**
     * Enter description here ...
     *
     * @param       $scheduleItems cc_schedule items, where the items get inserted after
     * @param       $filesToInsert array of schedule item info, what gets inserted into cc_schedule
     * @param mixed $mediaItems
     * @param mixed $moveAction
     * @param mixed $adjustSched
     */
    private function insertAfter($scheduleItems, $mediaItems, $filesToInsert = null, $adjustSched = true, $moveAction = false)
    {
        try {
            // temporary fix for CC-5665
            set_time_limit(180);

            $affectedShowInstances = [];

            // dont want to recalculate times for moved items
            // only moved items have a sched_id
            $excludeIds = [];

            $startProfile = microtime(true);

            $temp = [];
            $instance = null;

            /* Items in shows are ordered by position number. We need to know
             * the position when adding/moving items in linked shows so they are
             * added or moved in the correct position
             */
            $pos = 0;

            $linked = false;

            foreach ($scheduleItems as $schedule) {
                // reset
                $this->applyCrossfades = true;

                $id = intval($schedule['id']);

                /* Find out if the show where the cursor position (where an item will
                 * be inserted) is located is linked or not. If the show is linked,
                 * we need to make sure there isn't another cursor selection in one of it's
                 * linked shows. If there is that will cause a duplication, in the least,
                 * of inserted items
                 */
                if ($id != 0) {
                    $schedule_sql = 'SELECT * FROM cc_schedule WHERE id = ' . $id;
                    $ccSchedule = Application_Common_Database::prepareAndExecute(
                        $schedule_sql,
                        [],
                        Application_Common_Database::SINGLE
                    );

                    $show_sql = 'SELECT * FROM cc_show WHERE id IN (' .
                        'SELECT show_id FROM cc_show_instances WHERE id = ' . $ccSchedule['instance_id'] . ')';
                    $ccShow = Application_Common_Database::prepareAndExecute(
                        $show_sql,
                        [],
                        Application_Common_Database::SINGLE
                    );

                    $linked = $ccShow['linked'];
                    if ($linked) {
                        $unique = $ccShow['id'] . $ccSchedule['position'];
                        if (!in_array($unique, $temp)) {
                            $temp[] = $unique;
                        } else {
                            continue;
                        }
                    }
                } else {
                    $show_sql = 'SELECT * FROM cc_show WHERE id IN (' .
                        'SELECT show_id FROM cc_show_instances WHERE id = ' . $schedule['instance'] . ')';
                    $ccShow = Application_Common_Database::prepareAndExecute(
                        $show_sql,
                        [],
                        Application_Common_Database::SINGLE
                    );

                    $linked = $ccShow['linked'];
                    if ($linked) {
                        $unique = $ccShow['id'] . 'a';
                        if (!in_array($unique, $temp)) {
                            $temp[] = $unique;
                        } else {
                            continue;
                        }
                    }
                }

                /* If the show where the cursor position is located is linked
                 * we need to insert the items for each linked instance belonging
                 * to that show
                 */
                if ($linked) {
                    $instances = CcShowInstancesQuery::create()
                        ->filterByDbShowId($ccShow['id'])
                        ->filterByDbStarts(gmdate(DEFAULT_TIMESTAMP_FORMAT), Criteria::GREATER_THAN)
                        ->find();
                } else {
                    $instances = CcShowInstancesQuery::create()
                        ->filterByDbId($schedule['instance'])
                        ->find();
                }

                $excludePositions = [];
                foreach ($instances as &$instance) {
                    // reset
                    $this->applyCrossfades = true;

                    // $instanceId = $instance["id"];
                    $instanceId = $instance->getDbId();
                    if ($id !== 0) {
                        /* We use the selected cursor's position to find the same
                         * positions in every other linked instance
                         */
                        $pos = $ccSchedule['position'];

                        $linkedItem_sql = 'SELECT ends FROM cc_schedule ' .
                            "WHERE instance_id = {$instanceId} " .
                            "AND position = {$pos} " .
                            'AND playout_status != -1';
                        $linkedItemEnds = Application_Common_Database::prepareAndExecute(
                            $linkedItem_sql,
                            [],
                            Application_Common_Database::COLUMN
                        );

                        if (!$linkedItemEnds) {
                            // With dynamic smart blocks there may be different number of items in
                            // each show. In case the position does not exist we need to select
                            // the end time of the last position
                            $maxPos_sql = 'SELECT max(position) from cc_schedule ' .
                                "WHERE instance_id = {$instanceId}";
                            $pos = Application_Common_Database::prepareAndExecute(
                                $maxPos_sql,
                                [],
                                Application_Common_Database::COLUMN
                            );

                            // show instance has no scheduled tracks
                            if (empty($pos)) {
                                $pos = 0;
                                $nextStartDT = new DateTime($instance->getDbStarts(), new DateTimeZone('UTC'));
                            } else {
                                $linkedItem_sql = 'SELECT ends FROM cc_schedule ' .
                                    "WHERE instance_id = {$instanceId} " .
                                    "AND position = {$pos} " .
                                    'AND playout_status != -1';
                                $linkedItemEnds = Application_Common_Database::prepareAndExecute(
                                    $linkedItem_sql,
                                    [],
                                    Application_Common_Database::COLUMN
                                );

                                $nextStartDT = $this->findNextStartTime(
                                    new DateTime($linkedItemEnds, new DateTimeZone('UTC')),
                                    $instanceId
                                );
                            }
                        } else {
                            $nextStartDT = $this->findNextStartTime(
                                new DateTime($linkedItemEnds, new DateTimeZone('UTC')),
                                $instanceId
                            );

                            ++$pos;
                        }
                    }
                    // selected empty row to add after
                    else {
                        $showStartDT = new DateTime($instance->getDbStarts(), new DateTimeZone('UTC'));
                        $nextStartDT = $this->findNextStartTime($showStartDT, $instanceId);

                        // first item in show so start position counter at 0
                        $pos = 0;

                        /* Show is empty so we don't need to calculate crossfades
                         * for the first inserted item
                         */
                        $this->applyCrossfades = false;
                    }

                    if (!in_array($instanceId, $affectedShowInstances)) {
                        $affectedShowInstances[] = $instanceId;
                    }

                    /*
                     * $adjustSched is true if there are schedule items
                     * following the item just inserted, per show instance
                     */
                    if ($adjustSched === true) {
                        $pstart = microtime(true);

                        if ($this->applyCrossfades) {
                            $initalStartDT = clone $this->findTimeDifference(
                                $nextStartDT,
                                $this->crossfadeDuration
                            );
                        } else {
                            $initalStartDT = clone $nextStartDT;
                        }

                        $pend = microtime(true);
                        Logging::debug('finding all following items.');
                        Logging::debug(floatval($pend) - floatval($pstart));
                    }

                    // passing $schedule["instance"] so that the instance being scheduled
                    // can be used to determine the remaining time
                    // in the case of a fill remaining time smart block
                    if (is_null($filesToInsert)) {
                        $filesToInsert = [];
                        foreach ($mediaItems as $media) {
                            $filesToInsert = array_merge(
                                $filesToInsert,
                                $this->retrieveMediaFiles($media['id'], $media['type'], $schedule['instance'])
                            );
                        }
                    }

                    $doInsert = false;
                    $doUpdate = false;
                    $values = [];

                    // array that stores the cc_file ids so we can update the is_scheduled flag
                    $fileIds = [];

                    foreach ($filesToInsert as &$file) {
                        // item existed previously and is being moved.
                        // need to keep same id for resources if we want REST.
                        if (isset($file['sched_id'])) {
                            $adjustFromDT = clone $nextStartDT;
                            $doUpdate = true;

                            $movedItem_sql = 'SELECT * FROM cc_schedule ' .
                                'WHERE id = ' . $file['sched_id'];
                            $sched = Application_Common_Database::prepareAndExecute(
                                $movedItem_sql,
                                [],
                                Application_Common_Database::SINGLE
                            );

                            /* We need to keep a record of the original positon a track
                             * is being moved from so we can use it to retrieve the correct
                             * items in linked instances
                             */
                            if (!isset($originalPosition)) {
                                $originalPosition = $sched['position'];
                            }

                            /* If we are moving an item in a linked show we need to get
                             * the relative item to move in each instance. We know what the
                             * relative item is by its position
                             */
                            if ($linked) {
                                $movedItem_sql = 'SELECT * FROM cc_schedule ' .
                                    "WHERE position = {$originalPosition} " .
                                    "AND instance_id = {$instanceId}";

                                $sched = Application_Common_Database::prepareAndExecute(
                                    $movedItem_sql,
                                    [],
                                    Application_Common_Database::SINGLE
                                );
                            }
                            /* If we don't find a schedule item it means the linked
                             * shows have a different amount of items (dyanmic block)
                             * and we should skip the item move for this show instance
                             */
                            if (!$sched) {
                                continue;
                            }
                            $excludeIds[] = intval($sched['id']);

                            $file['cliplength'] = $sched['clip_length'];
                            $file['cuein'] = $sched['cue_in'];
                            $file['cueout'] = $sched['cue_out'];
                            $file['fadein'] = $sched['fade_in'];
                            $file['fadeout'] = $sched['fade_out'];
                        } else {
                            $doInsert = true;
                        }

                        // default fades are in seconds
                        // we need to convert to '00:00:00' format
                        // added a check to only run the conversion if they are in seconds format
                        // otherwise php > 7.1 throws errors
                        if (is_numeric($file['fadein'])) {
                            $file['fadein'] = Application_Common_DateHelper::secondsToPlaylistTime($file['fadein']);
                        }
                        if (is_numeric($file['fadeout'])) {
                            $file['fadeout'] = Application_Common_DateHelper::secondsToPlaylistTime($file['fadeout']);
                        }

                        switch ($file['type']) {
                            case 0:
                                $fileId = $file['id'];
                                $streamId = 'null';
                                $fileIds[] = $fileId;

                                break;

                            case 1:
                                $streamId = $file['id'];
                                $fileId = 'null';

                                break;

                            default:
                                break;
                        }

                        if ($this->applyCrossfades) {
                            $nextStartDT = $this->findTimeDifference(
                                $nextStartDT,
                                $this->crossfadeDuration
                            );
                            $endTimeDT = $this->findEndTime($nextStartDT, $file['cliplength']);
                            $endTimeDT = $this->findTimeDifference($endTimeDT, $this->crossfadeDuration);
                            /* Set it to false because the rest of the crossfades
                             * will be applied after we insert each item
                             */
                            $this->applyCrossfades = false;
                        }

                        $endTimeDT = $this->findEndTime($nextStartDT, $file['cliplength']);
                        if ($doInsert) {
                            $values[] = '(' .
                                "'{$nextStartDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "'{$endTimeDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "'{$file['cuein']}', " .
                                "'{$file['cueout']}', " .
                                "'{$file['fadein']}', " .
                                "'{$file['fadeout']}', " .
                                "'{$file['cliplength']}', " .
                                "{$pos}, " .
                                "{$instanceId}, " .
                                "{$fileId}, " .
                                "{$streamId})";
                        } elseif ($doUpdate) {
                            $update_sql = 'UPDATE cc_schedule SET ' .
                                "starts = '{$nextStartDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "ends = '{$endTimeDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "cue_in = '{$file['cuein']}', " .
                                "cue_out = '{$file['cueout']}', " .
                                "fade_in = '{$file['fadein']}', " .
                                "fade_out = '{$file['fadeout']}', " .
                                "clip_length = '{$file['cliplength']}', " .
                                "position = {$pos}, " .
                                "instance_id = {$instanceId} " .
                                "WHERE id = {$sched['id']}";

                            Application_Common_Database::prepareAndExecute(
                                $update_sql,
                                [],
                                Application_Common_Database::EXECUTE
                            );
                        }

                        $nextStartDT = $this->findTimeDifference($endTimeDT, $this->crossfadeDuration);
                        ++$pos;
                    } // all files have been inserted/moved
                    if ($doInsert) {
                        $insert_sql = 'INSERT INTO cc_schedule ' .
                            '(starts, ends, cue_in, cue_out, fade_in, fade_out, ' .
                            'clip_length, position, instance_id, file_id, stream_id) VALUES ' .
                            implode(',', $values) . ' RETURNING id';

                        $stmt = $this->con->prepare($insert_sql);
                        if ($stmt->execute()) {
                            foreach ($stmt->fetchAll() as $row) {
                                $excludeIds[] = $row['id'];
                            }
                        }
                    }

                    $selectCriteria = new Criteria();
                    $selectCriteria->add(CcFilesPeer::ID, $fileIds, Criteria::IN);
                    $selectCriteria->addAnd(CcFilesPeer::IS_SCHEDULED, false);
                    $updateCriteria = new Criteria();
                    $updateCriteria->add(CcFilesPeer::IS_SCHEDULED, true);
                    BasePeer::doUpdate($selectCriteria, $updateCriteria, $this->con);

                    /* Reset files to insert so we can get a new set of files. We have
                     * to do this in case we are inserting a dynamic block
                     */
                    if (!$moveAction) {
                        $filesToInsert = null;
                    }

                    if ($adjustSched === true) {
                        $followingItems_sql = 'SELECT * FROM cc_schedule ' .
                            "WHERE starts >= '{$initalStartDT->format(DEFAULT_MICROTIME_FORMAT)}' " .
                            "AND instance_id = {$instanceId} ";
                        if (count($excludeIds) > 0) {
                            $followingItems_sql .= 'AND id NOT IN (' . implode(',', $excludeIds) . ') ';
                        }
                        $followingItems_sql .= 'ORDER BY starts';
                        $followingSchedItems = Application_Common_Database::prepareAndExecute(
                            $followingItems_sql
                        );

                        $pstart = microtime(true);

                        // recalculate the start/end times after the inserted items.
                        foreach ($followingSchedItems as $item) {
                            $endTimeDT = $this->findEndTime($nextStartDT, $item['clip_length']);
                            $endTimeDT = $this->findTimeDifference($endTimeDT, $this->crossfadeDuration);
                            $update_sql = 'UPDATE cc_schedule SET ' .
                                "starts = '{$nextStartDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "ends = '{$endTimeDT->format(DEFAULT_MICROTIME_FORMAT)}', " .
                                "position = {$pos} " .
                                "WHERE id = {$item['id']}";
                            Application_Common_Database::prepareAndExecute(
                                $update_sql,
                                [],
                                Application_Common_Database::EXECUTE
                            );

                            $nextStartDT = $this->findTimeDifference($endTimeDT, $this->crossfadeDuration);
                            ++$pos;
                        }

                        $pend = microtime(true);
                        Logging::debug('adjusting all following items.');
                        Logging::debug(floatval($pend) - floatval($pstart));
                    }
                    if ($moveAction) {
                        $this->calculateCrossfades($instanceId);
                    }
                } // for each instance
            } // for each schedule location

            $endProfile = microtime(true);
            Logging::debug('finished adding scheduled items.');
            Logging::debug(floatval($endProfile) - floatval($startProfile));

            // update the status flag in cc_schedule.
            $instances = CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($affectedShowInstances)
                ->find($this->con);

            $startProfile = microtime(true);

            foreach ($instances as $instance) {
                $instance->updateScheduleStatus($this->con);
            }

            $endProfile = microtime(true);
            Logging::debug('updating show instances status.');
            Logging::debug(floatval($endProfile) - floatval($startProfile));

            $startProfile = microtime(true);

            // update the last scheduled timestamp.
            CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($affectedShowInstances)
                ->update(['DbLastScheduled' => new DateTime('now', new DateTimeZone('UTC'))], $this->con);

            $endProfile = microtime(true);
            Logging::debug('updating last scheduled timestamp.');
            Logging::debug(floatval($endProfile) - floatval($startProfile));
        } catch (Exception $e) {
            Logging::debug($e->getMessage());

            throw $e;
        }
    }

    private function updateMovedItem() {}

    private function getInstances($instanceId)
    {
        $ccShowInstance = CcShowInstancesQuery::create()->findPk($instanceId);
        $ccShow = $ccShowInstance->getCcShow();
        if ($ccShow->isLinked()) {
            return $ccShow->getFutureCcShowInstancess();
        }

        return [$ccShowInstance];
    }

    /*
     * @param array $scheduleItems (schedule_id and instance_id it belongs to)
     * @param array $mediaItems (file|block|playlist|webstream)
     */
    public function scheduleAfter($scheduleItems, $mediaItems, $adjustSched = true)
    {
        $this->con->beginTransaction();

        try {
            // Increase the transaction isolation level to prevent two concurrent requests from potentially resulting
            // in tracks scheduled at the same time.
            $this->con->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');

            $this->validateMediaItems($mediaItems); // Check for missing files, etc.
            $this->validateRequest($scheduleItems, true);

            /*
             * create array of arrays
             * array of schedule item info
             * (sched_id is the cc_schedule id and is set if an item is being
             *  moved because it is already in cc_schedule)
             * [0] = Array(
             *     id => 1,
             *     cliplength => 00:04:32,
             *     cuein => 00:00:00,
             *     cueout => 00:04:32,
             *     fadein => 00.5,
             *     fadeout => 00.5,
             *     sched_id => ,
             *     type => 0)
             * [1] = Array(
             *     id => 2,
             *     cliplength => 00:05:07,
             *     cuein => 00:00:00,
             *     cueout => 00:05:07,
             *     fadein => 00.5,
             *     fadeout => 00.5,
             *     sched_id => ,
             *     type => 0)
             */
            $this->insertAfter($scheduleItems, $mediaItems, null, $adjustSched);

            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    /*
     * @param array $selectedItem
     * @param array $afterItem
     */
    public function moveItem($selectedItems, $afterItems, $adjustSched = true)
    {
        // $startProfile = microtime(true);

        $this->con->beginTransaction();
        // $this->con->useDebug(true);

        try {
            // Increase the transaction isolation level to prevent two concurrent requests from potentially resulting
            // in tracks scheduled at the same time.
            $this->con->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');

            $this->validateItemMove($selectedItems, $afterItems[0]);
            $this->validateRequest($selectedItems);
            $this->validateRequest($afterItems);

            $endProfile = microtime(true);
            // Logging::debug("validating move request took:");
            // Logging::debug(floatval($endProfile) - floatval($startProfile));

            $afterInstance = CcShowInstancesQuery::create()->findPK($afterItems[0]['instance'], $this->con);

            // map show instances to cc_schedule primary keys.
            $modifiedMap = [];
            $movedData = [];

            // prepare each of the selected items.
            for ($i = 0; $i < count($selectedItems); ++$i) {
                $selected = CcScheduleQuery::create()->findPk($selectedItems[$i]['id'], $this->con);
                $selectedInstance = $selected->getCcShowInstances($this->con);

                $data = $this->fileInfo;
                $data['id'] = $selected->getDbFileId();
                $data['cliplength'] = $selected->getDbClipLength();
                $data['cuein'] = $selected->getDbCueIn();
                $data['cueout'] = $selected->getDbCueOut();
                $data['fadein'] = $selected->getDbFadeIn();
                $data['fadeout'] = $selected->getDbFadeOut();
                $data['sched_id'] = $selected->getDbId();

                $movedData[] = $data;

                // figure out which items must be removed from calculated show times.
                $showInstanceId = $selectedInstance->getDbId();
                $schedId = $selected->getDbId();
                if (isset($modifiedMap[$showInstanceId])) {
                    array_push($modifiedMap[$showInstanceId], $schedId);
                } else {
                    $modifiedMap[$showInstanceId] = [$schedId];
                }
            }

            // calculate times excluding the to be moved items.
            foreach ($modifiedMap as $instance => $schedIds) {
                $startProfile = microtime(true);

                $this->removeGaps($instance, $schedIds);

                // $endProfile = microtime(true);
                // Logging::debug("removing gaps from instance $instance:");
                // Logging::debug(floatval($endProfile) - floatval($startProfile));
            }

            // $startProfile = microtime(true);

            $this->insertAfter($afterItems, null, $movedData, $adjustSched, true);

            // $endProfile = microtime(true);
            // Logging::debug("inserting after removing gaps.");
            // Logging::debug(floatval($endProfile) - floatval($startProfile));

            $modified = array_keys($modifiedMap);
            // need to adjust shows we have moved items from.
            foreach ($modified as $instanceId) {
                $instance = CcShowInstancesQuery::create()->findPK($instanceId, $this->con);
                $instance->updateScheduleStatus($this->con);
            }

            // $this->con->useDebug(false);
            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    public function removeItems($scheduledItems, $adjustSched = true, $cancelShow = false)
    {
        $showInstances = [];
        $this->con->beginTransaction();

        try {
            $this->validateRequest($scheduledItems, true, true);

            $scheduledIds = [];
            foreach ($scheduledItems as $item) {
                $scheduledIds[] = $item['id'];
            }

            $removedItems = CcScheduleQuery::create()->findPks($scheduledIds);

            // This array is used to keep track of every show instance that was
            // effected by the track deletion. It will be used later on to
            // remove gaps in the schedule and adjust crossfade times.
            $effectedInstanceIds = [];

            foreach ($removedItems as $removedItem) {
                $instance = $removedItem->getCcShowInstances($this->con);
                $effectedInstanceIds[$instance->getDbId()] = $instance->getDbId();

                // check if instance is linked and if so get the schedule items
                // for all linked instances so we can delete them too
                if (!$cancelShow && $instance->getCcShow()->isLinked()) {
                    // returns all linked instances if linked
                    $ccShowInstances = $this->getInstances($instance->getDbId());

                    $instanceIds = [];
                    foreach ($ccShowInstances as $ccShowInstance) {
                        $instanceIds[] = $ccShowInstance->getDbId();
                    }
                    $effectedInstanceIds = array_merge($effectedInstanceIds, $instanceIds);

                    // Delete the same track, represented by $removedItem, in
                    // each linked show instance.
                    $itemsToDelete = CcScheduleQuery::create()
                        ->filterByDbPosition($removedItem->getDbPosition())
                        ->filterByDbInstanceId($instanceIds, Criteria::IN)
                        ->filterByDbId($removedItem->getDbId(), Criteria::NOT_EQUAL)
                        ->delete($this->con);
                }

                // check to truncate the currently playing item instead of deleting it.
                if ($removedItem->isCurrentItem($this->epochNow)) {
                    $nEpoch = $this->epochNow;
                    $sEpoch = $removedItem->getDbStarts('U.u');

                    $length = bcsub($nEpoch, $sEpoch, 6);
                    $cliplength = Application_Common_DateHelper::secondsToPlaylistTime($length);

                    $cueinSec = Application_Common_DateHelper::playlistTimeToSeconds($removedItem->getDbCueIn());
                    $cueOutSec = bcadd($cueinSec, $length, 6);
                    $cueout = Application_Common_DateHelper::secondsToPlaylistTime($cueOutSec);

                    // Set DbEnds - 1 second because otherwise there can be a timing issue
                    // when sending the new schedule to Pypo where Pypo thinks the track is still
                    // playing.
                    $removedItem->setDbCueOut($cueout)
                        ->setDbClipLength($cliplength)
                        ->setDbEnds($this->nowDT)
                        ->save($this->con);
                } else {
                    $removedItem->delete($this->con);
                }
            }
            Application_Model_StoredFile::updatePastFilesIsScheduled();

            if ($adjustSched === true) {
                foreach ($effectedInstanceIds as $instance) {
                    $this->removeGaps($instance);
                    $this->calculateCrossfades($instance);
                }
            }

            // update the status flag in cc_schedule.
            $instances = CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($effectedInstanceIds)
                ->find($this->con);

            foreach ($instances as $instance) {
                $instance->updateScheduleStatus($this->con);
                $instance->correctSchedulePositions();
            }

            // update the last scheduled timestamp.
            CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($showInstances)
                ->update(['DbLastScheduled' => new DateTime('now', new DateTimeZone('UTC'))], $this->con);

            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    // This is used to determine the duration of a files array
    public function timeLengthOfFiles($files)
    {
        $timeLength = 0;
        foreach ($files as $file) {
            $timeLength += Application_Common_DateHelper::playlistTimeToSeconds($file['cliplength']);
            $timeLength += $file['fadein'];
            $timeLength += $file['fadeout'];
        }

        return $timeLength;
    }

    /*
     * Used for cancelling the current show instance.
     *
     * @param $p_id id of the show instance to cancel.
     */
    public function cancelShow($p_id)
    {
        $this->con->beginTransaction();

        try {
            $instance = CcShowInstancesQuery::create()->findPK($p_id);

            if (!$instance->getDbRecord()) {
                $items = CcScheduleQuery::create()
                    ->filterByDbInstanceId($p_id)
                    ->filterByDbEnds($this->nowDT, Criteria::GREATER_THAN)
                    ->find($this->con);

                if (count($items) > 0) {
                    $remove = [];
                    $ts = $this->nowDT->format('U');

                    for ($i = 0; $i < count($items); ++$i) {
                        $remove[$i]['instance'] = $p_id;
                        $remove[$i]['timestamp'] = $ts;
                        $remove[$i]['id'] = $items[$i]->getDbId();
                    }

                    $this->removeItems($remove, false, true);
                }
            } else {
                $rebroadcasts = $instance->getCcShowInstancessRelatedByDbId(null, $this->con);
                $rebroadcasts->delete($this->con);
            }

            $instance->setDbEnds($this->nowDT);
            $instance->save($this->con);

            $this->con->commit();

            if ($instance->getDbRecord()) {
                Application_Model_RabbitMq::SendMessageToShowRecorder('cancel_recording');
            }
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }
}

class OutDatedScheduleException extends Exception {}
