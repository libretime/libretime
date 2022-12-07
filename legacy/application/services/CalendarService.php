<?php

declare(strict_types=1);

class Application_Service_CalendarService
{
    private $currentUser;
    private $ccShowInstance;
    private $ccShow;

    public function __construct($instanceId = null)
    {
        if (!is_null($instanceId)) {
            $this->ccShowInstance = CcShowInstancesQuery::create()->findPk($instanceId);
            if (is_null($this->ccShowInstance)) {
                throw new Exception('Instance does not exist');
            }
            $this->ccShow = $this->ccShowInstance->getCcShow();
        }

        $service_user = new Application_Service_UserService();
        $this->currentUser = $service_user->getCurrentUser();
    }

    /**
     * Enter description here ...
     */
    public function makeContextMenu()
    {
        $menu = [];
        $now = time();
        $baseUrl = Config::getBasePath();
        $isAdminOrPM = $this->currentUser->isAdminOrPM();
        $isHostOfShow = $this->currentUser->isHostOfShow($this->ccShow->getDbId());

        // DateTime objects in UTC
        $startDT = $this->ccShowInstance->getDbStarts(null);
        $endDT = $this->ccShowInstance->getDbEnds(null);

        // timestamps
        $start = $startDT->getTimestamp();
        $end = $endDT->getTimestamp();

        // show has ended
        if ($now > $end) {
            if ($this->ccShowInstance->isRecorded()) {
                $ccFile = $this->ccShowInstance->getCcFiles();
                if (!isset($ccFile)) {
                    $menu['error when recording'] = [
                        'name' => _("Record file doesn't exist"),
                        'icon' => 'error',
                    ];
                } else {
                    $menu['view_recorded'] = [
                        'name' => _('View Recorded File Metadata'),
                        'icon' => 'overview',
                        'url' => $baseUrl . 'library/edit-file-md/id/' . $ccFile->getDbId(),
                    ];
                }
            } else {
                $menu['content'] = [
                    // "name"=> _("Show Content"),
                    'name' => _('View'),
                    'icon' => 'overview',
                    'url' => $baseUrl . 'schedule/show-content-dialog',
                ];
            }
        } else {
            // Show content can be modified from the calendar if:
            // the user is admin or hosting the show,
            // the show is not recorded
            $currentShow = Application_Model_Show::getCurrentShow();
            $currentShowId = count($currentShow) == 1 ? $currentShow[0]['id'] : null;
            $showIsLinked = $this->ccShow->isLinked();

            // user can add/remove content if the show has not ended
            if ($now < $end && ($isAdminOrPM || $isHostOfShow) && !$this->ccShowInstance->isRecorded()) {
                // if the show is not linked OR if the show is linked AND not the current playing show
                // the user can add/remove content
                if (!$showIsLinked || ($showIsLinked && $currentShowId != $this->ccShow->getDbId())) {
                    $menu['schedule'] = [
                        // "name"=> _("Add / Remove Content"),
                        'name' => _('Schedule Tracks'),
                        'icon' => 'add-remove-content',
                        'url' => $baseUrl . 'showbuilder/builder-dialog/',
                    ];
                }
            }

            // "Show Content" should be a menu item at all times except when
            // the show is recorded
            if (!$this->ccShowInstance->isRecorded()) {
                $menu['content'] = [
                    // "name"=> _("Show Content"),
                    'name' => _('View'),
                    'icon' => 'overview',
                    'url' => $baseUrl . 'schedule/show-content-dialog',
                ];
            }

            // user can remove all content if the show has not started
            if ($now < $start && ($isAdminOrPM || $isHostOfShow) && !$this->ccShowInstance->isRecorded()) {
                // if the show is not linked OR if the show is linked AND not the current playing show
                // the user can remove all content
                if (!$showIsLinked || ($showIsLinked && $currentShowId != $this->ccShow->getDbId())) {
                    $menu['clear'] = [
                        // "name"=> _("Remove All Content"),
                        'name' => _('Clear Show'),
                        'icon' => 'remove-all-content',
                        'url' => $baseUrl . 'schedule/clear-show',
                    ];
                }
            }

            // show is currently playing and user is admin
            if ($start <= $now && $now < $end && $isAdminOrPM) {
                // Menu separator
                $menu['sep1'] = '-----------';

                if ($this->ccShowInstance->isRecorded()) {
                    $menu['cancel_recorded'] = [
                        // "name"=> _("Cancel Current Show"),
                        'name' => _('Cancel Show'),
                        'icon' => 'delete',
                    ];
                } else {
                    $menu['cancel'] = [
                        // "name"=> _("Cancel Current Show"),
                        'name' => _('Cancel Show'),
                        'icon' => 'delete',
                    ];
                }
            }

            $excludeIds = $this->ccShow->getEditedRepeatingInstanceIds();

            $isRepeating = $this->ccShow->isRepeating();
            $populateInstance = false;
            if ($isRepeating && in_array($this->ccShowInstance->getDbId(), $excludeIds)) {
                $populateInstance = true;
            }

            if (!$this->ccShowInstance->isRebroadcast() && $isAdminOrPM) {
                // Menu separator
                $menu['sep2'] = '-----------';

                if ($isRepeating) {
                    if ($populateInstance) {
                        $menu['edit'] = [
                            // "name" => _("Edit This Instance"),
                            'name' => _('Edit Instance'),
                            'icon' => 'edit',
                            'url' => $baseUrl . 'Schedule/populate-repeating-show-instance-form',
                        ];
                    } else {
                        $menu['edit'] = [
                            'name' => _('Edit'),
                            'icon' => 'edit',
                            'items' => [],
                        ];

                        $menu['edit']['items']['all'] = [
                            'name' => _('Edit Show'),
                            'icon' => 'edit',
                            'url' => $baseUrl . 'Schedule/populate-show-form',
                        ];

                        $menu['edit']['items']['instance'] = [
                            // "name" => _("Edit This Instance"),
                            'name' => _('Edit Instance'),
                            'icon' => 'edit',
                            'url' => $baseUrl . 'Schedule/populate-repeating-show-instance-form',
                        ];
                    }
                } else {
                    $menu['edit'] = [
                        'name' => _('Edit Show'),
                        'icon' => 'edit',
                        '_type' => 'all',
                        'url' => $baseUrl . 'Schedule/populate-show-form',
                    ];
                }
            }

            // show hasn't started yet and user is admin
            if ($now < $start && $isAdminOrPM) {
                // Menu separator
                $menu['sep3'] = '-----------';

                // show is repeating so give user the option to delete all
                // repeating instances or just the one
                if ($isRepeating) {
                    $menu['del'] = [
                        'name' => _('Delete'),
                        'icon' => 'delete',
                        'items' => [],
                    ];

                    $menu['del']['items']['single'] = [
                        // "name"=> _("Delete This Instance"),
                        'name' => _('Delete Instance'),
                        'icon' => 'delete',
                        'url' => $baseUrl . 'schedule/delete-show-instance',
                    ];

                    $menu['del']['items']['following'] = [
                        // "name"=> _("Delete This Instance and All Following"),
                        'name' => _('Delete Instance and All Following'),
                        'icon' => 'delete',
                        'url' => $baseUrl . 'schedule/delete-show',
                    ];
                } elseif ($populateInstance) {
                    $menu['del'] = [
                        'name' => _('Delete'),
                        'icon' => 'delete',
                        'url' => $baseUrl . 'schedule/delete-show-instance',
                    ];
                } else {
                    $menu['del'] = [
                        'name' => _('Delete'),
                        'icon' => 'delete',
                        'url' => $baseUrl . 'schedule/delete-show',
                    ];
                }
            }
        }

        return $menu;
    }

    /**
     * Enter description here ...
     *
     * @param DateTime $dateTime object to add deltas to
     * @param int      $deltaDay delta days show moved
     * @param int      $deltaMin delta minutes show moved
     */
    public static function addDeltas($dateTime, $deltaDay, $deltaMin)
    {
        $newDateTime = clone $dateTime;

        $days = abs($deltaDay);
        $mins = abs($deltaMin);

        $dayInterval = new DateInterval("P{$days}D");
        $minInterval = new DateInterval("PT{$mins}M");

        if ($deltaDay > 0) {
            $newDateTime->add($dayInterval);
        } elseif ($deltaDay < 0) {
            $newDateTime->sub($dayInterval);
        }

        if ($deltaMin > 0) {
            $newDateTime->add($minInterval);
        } elseif ($deltaMin < 0) {
            $newDateTime->sub($minInterval);
        }

        return $newDateTime;
    }

    private function validateShowMove($deltaDay, $deltaMin)
    {
        if (!$this->currentUser->isAdminOrPM()) {
            throw new Exception(_('Permission denied'));
        }

        if ($this->ccShow->isRepeating()) {
            throw new Exception(_("Can't drag and drop repeating shows"));
        }

        $today_timestamp = time();

        $startsDateTime = $this->ccShowInstance->getDbStarts(null);
        $endsDateTime = $this->ccShowInstance->getDbEnds(null);

        if ($today_timestamp > $startsDateTime->getTimestamp()) {
            throw new Exception(_("Can't move a past show"));
        }

        // the user is moving the show on the calendar from the perspective of local time.
        // incase a show is moved across a time change border offsets should be added to the localtime
        // stamp and then converted back to UTC to avoid show time changes!
        $showTimezone = $this->ccShow->getFirstCcShowDay()->getDbTimezone();
        $startsDateTime->setTimezone(new DateTimeZone($showTimezone));
        $endsDateTime->setTimezone(new DateTimeZone($showTimezone));

        $duration = $startsDateTime->diff($endsDateTime);

        $newStartsDateTime = self::addDeltas($startsDateTime, $deltaDay, $deltaMin);
        /* WARNING: Do not separately add a time delta to the start and end times because
                    that does not preserve the duration across a DST time change.
                    For example, 5am - 3 hours = 3am when DST occurs at 2am.
                             BUT, 6am - 3 hours = 3am also!
                              So when a DST change occurs, adding the deltas like this
                              separately does not conserve the duration of a show.
                    Since that's what we want (otherwise we'll get a zero length show),
                    we calculate the show duration FIRST, then we just add that on
                    to the start time to calculate the end time.
                    This is a safer approach.
                    The key lesson here is that in general: duration != end - start
                    ... so be careful!
        */
        // $newEndsDateTime = self::addDeltas($endsDateTime, $deltaDay, $deltaMin); <--- Wrong, don't do it.
        $newEndsDateTime = clone $newStartsDateTime;
        $newEndsDateTime = $newEndsDateTime->add($duration);

        // convert our new starts/ends to UTC.
        $newStartsDateTime->setTimezone(new DateTimeZone('UTC'));
        $newEndsDateTime->setTimezone(new DateTimeZone('UTC'));

        if ($today_timestamp > $newStartsDateTime->getTimestamp()) {
            throw new Exception(_("Can't move show into past"));
        }

        // check if show is overlapping
        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            $newStartsDateTime,
            $newEndsDateTime,
            true,
            $this->ccShowInstance->getDbId()
        );
        if ($overlapping) {
            throw new Exception(_('Cannot schedule overlapping shows'));
        }

        if ($this->ccShow->isRecorded()) {
            // rebroadcasts should start at max 1 hour after a recorded show has ended.
            $minRebroadcastStart = self::addDeltas($newEndsDateTime, 0, 60);
            // check if we are moving a recorded show less than 1 hour before any of its own rebroadcasts.
            $rebroadcasts = CcShowInstancesQuery::create()
                ->filterByDbOriginalShow($this->ccShow->getDbId())
                ->filterByDbStarts($minRebroadcastStart->format(DEFAULT_TIMESTAMP_FORMAT), Criteria::LESS_THAN)
                ->find();

            if (count($rebroadcasts) > 0) {
                throw new Exception(_("Can't move a recorded show less than 1 hour before its rebroadcasts."));
            }
        }

        if ($this->ccShow->isRebroadcast()) {
            $recordedShow = CcShowInstancesQuery::create()
                ->filterByCcShow($this->ccShowInstance->getDbOriginalShow())
                ->findOne();
            if (is_null($recordedShow)) {
                $this->ccShowInstance->delete();

                throw new Exception(_('Show was deleted because recorded show does not exist!'));
            }

            $recordEndDateTime = new DateTime($recordedShow->getDbEnds(), new DateTimeZone('UTC'));
            $newRecordEndDateTime = self::addDeltas($recordEndDateTime, 0, 60);

            if ($newStartsDateTime->getTimestamp() < $newRecordEndDateTime->getTimestamp()) {
                throw new Exception(_('Must wait 1 hour to rebroadcast.'));
            }
        }

        return [$newStartsDateTime, $newEndsDateTime];
    }

    public function moveShow($deltaDay, $deltaMin)
    {
        try {
            $con = Propel::getConnection();
            $con->beginTransaction();

            // new starts,ends are in UTC
            [$newStartsDateTime, $newEndsDateTime] = $this->validateShowMove(
                $deltaDay,
                $deltaMin
            );

            $oldStartDateTime = $this->ccShowInstance->getDbStarts(null);

            $this->ccShowInstance
                ->setDbStarts($newStartsDateTime)
                ->setDbEnds($newEndsDateTime)
                ->save($con);

            if (!$this->ccShowInstance->getCcShow()->isRebroadcast()) {
                // we can get the first show day because we know the show is
                // not repeating, and therefore will only have one show day entry
                $ccShowDay = $this->ccShow->getFirstCcShowDay();
                $showTimezone = new DateTimeZone($ccShowDay->getDbTimezone());
                $ccShowDay
                    ->setDbFirstShow($newStartsDateTime->setTimezone($showTimezone)->format('Y-m-d'))
                    ->setDbStartTime($newStartsDateTime->format('H:i'))
                    ->save($con);
            }

            $diff = $newStartsDateTime->getTimestamp() - $oldStartDateTime->getTimestamp();

            Application_Service_SchedulerService::updateScheduleStartTime(
                [$this->ccShowInstance->getDbId()],
                $diff
            );

            $con->commit();
            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $con->rollback();

            return $e->getMessage();
        }
    }

    // TODO move the method resizeShow from Application_Model_Show here.
    public function resizeShow($deltaDay, $deltaMin)
    {
        try {
            $con = Propel::getConnection();
            $con->beginTransaction();

            $con->commit();
            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
