<?php

declare(strict_types=1);

define('MAX_REBROADCAST_DATES', 10);
define('NO_REPEAT', -1);
define('REPEAT_WEEKLY', 0);
define('REPEAT_BI_WEEKLY', 1);
define('REPEAT_MONTHLY_MONTHLY', 2);
define('REPEAT_MONTHLY_WEEKLY', 3);
define('REPEAT_TRI_WEEKLY', 4);
define('REPEAT_QUAD_WEEKLY', 5);

class Application_Service_ShowService
{
    private $ccShow;
    private $isRecorded;
    private $isRebroadcast;
    private $repeatType;
    private $isUpdate;
    private $oldShowTimezone;
    private $localShowStartHour;
    private $localShowStartMin;
    private $origCcShowDay;
    private $origShowRepeatStatus;
    private $instanceIdsForScheduleUpdates;

    // keeps track of which show instances are new from either adding a new show
    // day or changing the repeat type day during a show edit, or when a user moves
    // forward in the calendar
    private $newInstanceIdsCreated;

    public function __construct($showId = null, $showData = null, $isUpdate = false)
    {
        if (!is_null($showId)) {
            $this->ccShow = CcShowQuery::create()->findPk($showId);
        }

        if (isset($showData['add_show_repeats']) && $showData['add_show_repeats']) {
            $this->repeatType = $showData['add_show_repeat_type'];
            if ($showData['add_show_repeat_type'] == 2) {
                $this->repeatType = $showData['add_show_monthly_repeat_type'];
            }
        } else {
            $this->repeatType = -1;
        }

        $this->isRecorded = (isset($showData['add_show_record']) && $showData['add_show_record']) ? 1 : 0;
        $this->isRebroadcast = (isset($showData['add_show_rebroadcast']) && $showData['add_show_rebroadcast']) ? 1 : 0;
        $this->isUpdate = $isUpdate;
        $this->instanceIdsForScheduleUpdates = [];
        $this->newInstanceIdsCreated = [];
    }

    public function editRepeatingShowInstance($showData)
    {
        $service_user = new Application_Service_UserService();
        $currentUser = $service_user->getCurrentUser();

        $showData['add_show_duration'] = $this->formatShowDuration(
            $showData['add_show_duration']
        );

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            if (!$currentUser->isAdminOrPM()) {
                throw new Exception('Permission denied');
            }

            $showId = $showData['add_show_id'];

            // UPDATE SCHEDULE START TIME
            // get the ccShow object to which this instance belongs
            // so we can get the original start date and time
            $this->ccShow = CcShowQuery::create()
                ->findPk($showId);

            // DateTime in shows's local time
            $newStartDateTime = new DateTime(
                $showData['add_show_start_date'] . ' ' .
                    $showData['add_show_start_time'],
                new DateTimeZone($showData['add_show_timezone'])
            );

            $ccShowInstanceOrig = CcShowInstancesQuery::create()
                ->findPk($showData['add_show_instance_id']);

            // convert original start time into the show's local timezone
            $origLocalStartDateTime = $ccShowInstanceOrig->getLocalStartDateTime();

            $diff = $this->calculateShowStartDiff(
                $newStartDateTime,
                $origLocalStartDateTime
            );

            if ($diff != 0) {
                Application_Service_SchedulerService::updateScheduleStartTime(
                    [$showData['add_show_instance_id']],
                    $diff
                );
            }
            // UPDATE SCHEDULE START TIME ENDS

            /*
             * In the case where an instance is being edited for a second
             * (or third, fourth, etc.) time we need to delete the old
             * cc_show_day record
             *
             * Since we don't store the cc_show_day ids we need to use the
             * original start time from cc_show_instances, convert it to the show's
             * local timezone, and find the record in cc_show_days
             *
             * *** There is a flaw here: We have to assume the show timezone has
             * *** not changed (make timezone readonly??)
             */
            $origCcShowDay = CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbRepeatType(-1)
                ->filterByDbFirstShow($origLocalStartDateTime->format('Y-m-d'))
                ->filterByDbStartTime($origLocalStartDateTime->format('H:i:s'))
                ->delete();

            /*
             * Set the new cc_show_day record
             * Associates it with the current show_id and sets it to non-repeating
             */
            $this->setCcShowDays($showData);

            /*
             * We need to find the new show day rule we just created by passing
             * in the first show and start time in case multiple single
             * instances have been edited out of the repeating sequence.
             */
            $showDay = CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbRepeatType(-1)
                ->filterByDbFirstShow($showData['add_show_start_date'])
                ->filterByDbStartTime($showData['add_show_start_time'] . ':00')
                ->findOne();

            $ccShowInstance = $this->createNonRepeatingInstance(
                $showDay,
                $this->getPopulateShowUntilDateTIme()
            );

            // update cc_schedule with the new instance id
            $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME);
            $selectCriteria = new Criteria();
            $selectCriteria->add(CcSchedulePeer::INSTANCE_ID, $showData['add_show_instance_id']);
            $updateCriteria = new Criteria();
            $updateCriteria->add(CcSchedulePeer::INSTANCE_ID, $ccShowInstance->getDbId());
            BasePeer::doUpdate($selectCriteria, $updateCriteria, $con);

            $ccShowInstance->updateDbTimeFilled($con);
            $ccShowInstance->updateScheduleStatus($con);
            $ccShowInstance
                ->setDbDescription($showData['add_show_instance_description'])
                ->save();

            // delete the edited instance from the repeating sequence
            $ccShowInstanceOrig
                ->setDbModifiedInstance(true)
                ->setDbDescription($showData['add_show_instance_description'])
                ->save();

            $con->commit();
            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $con->rollback();
            Logging::info('EXCEPTION: Show update failed.');
            Logging::info($e->getMessage());
        }
    }

    /**
     * If a user is editing a show we need to store the original timezone and
     * start time in case the show's timezone is changed and we are crossing
     * over DST.
     */
    private function storeOrigLocalShowInfo()
    {
        if ($this->ccShow->isRepeating()) {
            $this->origCcShowDay = clone $this->ccShow->getFirstRepeatingCcShowDay();
            $this->origShowRepeatStatus = true;
        } else {
            $this->origCcShowDay = clone $this->ccShow->getFirstCcShowDay();
            $this->origShowRepeatStatus = false;
        }

        $this->oldShowTimezone = $this->origCcShowDay->getDbTimezone();

        $origStartTime = explode(':', $this->origCcShowDay->getDbStartTime());
        $this->localShowStartHour = $origStartTime[0];
        $this->localShowStartMin = $origStartTime[1];
    }

    public function addUpdateShow($showData)
    {
        $service_user = new Application_Service_UserService();
        $currentUser = $service_user->getCurrentUser();

        $showData['add_show_duration'] = $this->formatShowDuration(
            $showData['add_show_duration']
        );

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            if (!$currentUser->isAdminOrPM()) {
                throw new Exception('Permission denied');
            }

            // update ccShow
            $this->setCcShow($showData);

            $daysAdded = [];

            if ($this->isUpdate) {
                $showId = $this->ccShow->getDbId();

                // Only delete the previous logo if a new one is being uploaded
                if (array_key_exists('add_show_logo_name', $showData) && $showData['add_show_logo_name'] !== '') {
                    if (!Rest_ShowImageController::deleteShowImagesFromStor($showId)) {
                        throw new Exception('Error deleting show images');
                    }
                }

                if (!$this->ccShow->getCcShowDayss()->isEmpty()) {
                    $this->storeOrigLocalShowInfo();
                }

                $daysAdded = $this->delegateInstanceCleanup($showData);

                $this->deleteRebroadcastInstances();

                $this->deleteCcShowHosts();

                if ($this->isRebroadcast) {
                    // delete entry in cc_show_rebroadcast
                    $this->deleteCcShowRebroadcasts();
                }

                $this->storeInstanceIds();
            }

            // update ccShowDays
            $this->setCcShowDays($showData);

            // update ccShowRebroadcasts
            $this->setCcShowRebroadcasts($showData);

            // update ccShowHosts
            $this->setCcShowHosts($showData);

            // create new ccShowInstances
            $this->delegateInstanceCreation($daysAdded);

            if ($this->isUpdate) {
                /* If the show is repeating and the start date changes we need
                 * to ignore that difference when re-calculating schedule start times.
                 * Otherwise it might calculate a difference of a week, for example.
                 */
                if (
                    $this->ccShow->isRepeating()
                    && $this->origCcShowDay->getLocalStartDateAndTime()->format('Y-m-d') != $showData['add_show_start_date']
                ) {
                    $showData['add_show_start_date'] = $this->origCcShowDay->getLocalStartDateAndTime()->format('Y-m-d');
                }

                $this->adjustSchedule($showData);
            }

            $con->commit();
            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $con->rollback();
            $this->isUpdate ? $action = 'update' : $action = 'creation';
            Logging::info('EXCEPTION: Show ' . $action . ' failed.');
            Logging::info($e->getMessage());
        }

        // Added to pass along to the RESTful ShowImageController
        return $this->ccShow->getDbId();
    }

    /**
     * Returns an array of instance ids that already exist
     * We need this if a show is being updated so we can separate the
     * instances that already exist and any new instances that
     * get created (by adding a new repeat show day).
     */
    private function storeInstanceIds()
    {
        $instances = $this->ccShow->getFutureCcShowInstancess();
        foreach ($instances as $instance) {
            $this->instanceIdsForScheduleUpdates[] = $instance->getDbId();
        }
    }

    /**
     * Adjusts the items in cc_schedule to reflect the
     * new (if one) start and end time of the show getting updated.
     *
     * @param mixed $showData
     */
    private function adjustSchedule($showData)
    {
        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        $this->updateScheduleStartEndTimes($showData);

        $ccShowInstances = $this->ccShow->getFutureCcShowInstancess();
        foreach ($ccShowInstances as $instance) {
            $instance->updateScheduleStatus($con);
        }
    }

    /**
     * Receives a cc_show id and determines whether to create a
     * single show instance or repeating show instances.
     *
     * @param null|mixed $daysAdded
     * @param null|mixed $end
     * @param mixed      $fillInstances
     */
    public function delegateInstanceCreation($daysAdded = null, $end = null, $fillInstances = false)
    {
        $populateUntil = $this->getPopulateShowUntilDateTIme();

        if (is_null($this->ccShow)) {
            $ccShowDays = $this->getShowDaysInRange($populateUntil, $end);
        } else {
            if ($this->ccShow->isRepeating()) {
                $ccShowDays = $this->ccShow->getRepeatingCcShowDays();
            } else {
                // $ccShowDays = $this->ccShow->getCcShowDayss();

                /* Cannot use the above statement to get the cc_show_days
                 * object because it's getting the old object before the
                 * show was edited. clearInstancePool() didn't work.
                 */
                $ccShowDays = CcShowDaysQuery::create()
                    ->filterByDbShowId($this->ccShow->getDbId())
                    ->find();
            }
        }

        if (!is_null($end)) {
            $populateUntil = $end;
        }

        /* In case the user is moving forward in the calendar and there are
         * linked shows in the schedule we need to keep track of each cc_show
         * so we know which shows need to be filled with content
         */
        $ccShows = [];

        foreach ($ccShowDays as $day) {
            $this->ccShow = $day->getCcShow();
            $this->isRecorded = $this->ccShow->isRecorded();
            $this->isRebroadcast = $this->ccShow->isRebroadcast();

            $show_id = $day->getDbShowId();
            if (!isset($ccShows[$show_id])) {
                $ccShows[$show_id] = $day->getccShow();
            }

            // keep track of the new show instances getting created
            // so we can fill their schedule after
            if (!isset($this->newInstanceIdsCreated[$show_id])) {
                $this->newInstanceIdsCreated[$show_id] = [];
            }

            switch ($day->getDbRepeatType()) {
                case NO_REPEAT:
                    $this->createNonRepeatingInstance($day, $populateUntil);

                    break;

                case REPEAT_WEEKLY:
                    $this->createWeeklyRepeatInstances(
                        $day,
                        $populateUntil,
                        REPEAT_WEEKLY,
                        new DateInterval('P7D'),
                        $daysAdded
                    );

                    break;

                case REPEAT_BI_WEEKLY:
                    $this->createWeeklyRepeatInstances(
                        $day,
                        $populateUntil,
                        REPEAT_BI_WEEKLY,
                        new DateInterval('P14D'),
                        $daysAdded
                    );

                    break;

                case REPEAT_TRI_WEEKLY:
                    $this->createWeeklyRepeatInstances(
                        $day,
                        $populateUntil,
                        REPEAT_TRI_WEEKLY,
                        new DateInterval('P21D'),
                        $daysAdded
                    );

                    break;

                case REPEAT_QUAD_WEEKLY:
                    $this->createWeeklyRepeatInstances(
                        $day,
                        $populateUntil,
                        REPEAT_QUAD_WEEKLY,
                        new DateInterval('P28D'),
                        $daysAdded
                    );

                    break;

                case REPEAT_MONTHLY_MONTHLY:
                    $this->createMonthlyRepeatInstances($day, $populateUntil);

                    break;

                case REPEAT_MONTHLY_WEEKLY:
                    $this->createMonthlyRepeatInstances($day, $populateUntil);

                    break;
            }
        }

        foreach ($ccShows as $ccShow) {
            if (($this->isUpdate || $fillInstances) && $ccShow->isLinked()) {
                Application_Service_SchedulerService::fillLinkedInstances(
                    $ccShow,
                    $this->newInstanceIdsCreated[$ccShow->getDbId()]
                );
            }
        }

        /*if (isset($this->linkedShowContent)) {
            Application_Service_SchedulerService::fillPreservedLinkedShowContent(
                $this->ccShow, $this->linkedShowContent);
        }*/

        return $this->ccShow;
    }

    private function getShowDaysInRange($start, $end)
    {
        $endTimeString = $end->format(DEFAULT_TIMESTAMP_FORMAT);
        if (!is_null($start)) {
            $startTimeString = $start->format(DEFAULT_TIMESTAMP_FORMAT);
        } else {
            $today_timestamp = new DateTime('now', new DateTimeZone('UTC'));
            $startTimeString = $today_timestamp->format(DEFAULT_TIMESTAMP_FORMAT);
        }

        $c = new Criteria();
        $c->add(CcShowDaysPeer::FIRST_SHOW, $endTimeString, Criteria::LESS_THAN);
        $c->addAnd(CcShowDaysPeer::LAST_SHOW, $startTimeString, Criteria::GREATER_THAN);
        $c->addAnd(CcShowDaysPeer::REPEAT_TYPE, -1, Criteria::NOT_EQUAL);
        $c->addOr(CcShowDaysPeer::LAST_SHOW, null, Criteria::ISNULL);

        return CcShowDaysPeer::doSelect($c);
    }

    public static function formatShowDuration($duration)
    {
        $hPos = strpos($duration, 'h');
        $mPos = strpos($duration, 'm');

        $hValue = 0;
        $mValue = 0;

        if ($hPos !== false) {
            $hValue = trim(substr($duration, 0, $hPos));
        }
        if ($mPos !== false) {
            $hPos = $hPos === false ? 0 : $hPos + 1;
            $mValue = trim(substr($duration, $hPos, -1));
        }

        return $hValue . ':' . $mValue;
    }

    /**
     * Deletes all the cc_show_days entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show day specs.
     */
    private function deleteCcShowDays()
    {
        CcShowDaysQuery::create()->filterByDbShowId($this->ccShow->getDbId())->delete();
    }

    private function deleteRebroadcastInstances()
    {
        $sql = <<<'SQL'
DELETE FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
AND show_id = :showId
AND rebroadcast = 1;
SQL;
        Application_Common_Database::prepareAndExecute($sql, [
            ':showId' => $this->ccShow->getDbId(),
            ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
        ], 'execute');
    }

    private function deleteAllShowDays($showId)
    {
        CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->delete();
    }

    /**
     * TODO: This function is messy. Needs refactoring.
     *
     * When editing a show we may need to perform some actions to reflect the new specs:
     * - Delete some show instances
     * - Update duration
     * - Update start and end time
     *
     * @param $showData edit show form values in raw form
     * @param $isRecorded value computed from the edit show form
     * @param $repeatType value computed from the edit show form
     */
    private function delegateInstanceCleanup($showData)
    {
        $showId = $this->ccShow->getDbId();

        $daysAdded = [];

        // CcShowDay object
        if ($this->ccShow->isRepeating()) {
            $currentShowDay = $this->ccShow->getFirstRepeatingCcShowDay();

            // all cc_show_days
            $ccShowDays = $this->ccShow->getRepeatingCcShowDays();
        } else {
            $currentShowDay = $this->ccShow->getFirstCcShowDay();

            // all cc_show_days
            $ccShowDays = $this->ccShow->getCcShowDayss();
        }

        // new end date in the show's timezone (from the select box)
        $endDateTime = $this->calculateEndDate($showData);

        // repeat option was toggled
        if ($showData['add_show_repeats'] != $currentShowDay->isRepeating()) {
            $this->deleteAllRepeatInstances($currentShowDay, $showId);

            if (!$showData['add_show_repeats']) {
                $this->deleteAllShowDays($showId);
            }

            // if repeat option was checked we need to treat the current show day
            // as a new show day so the repeat instances get created properly
            // in createWeeklyRepeatInstances()
            if ($showData['add_show_repeats']) {
                array_push($daysAdded, $currentShowDay->getDbDay());
            }
        }

        if ($showData['add_show_repeats']) {
            $localShowStart = $currentShowDay->getLocalStartDateAndTime();

            // if the start date changes, these are the repeat types
            // that require show instance deletion
            $deleteRepeatTypes = [
                REPEAT_BI_WEEKLY, REPEAT_TRI_WEEKLY, REPEAT_QUAD_WEEKLY, REPEAT_MONTHLY_MONTHLY,
                REPEAT_MONTHLY_WEEKLY,
            ];

            if (
                in_array($this->repeatType, $deleteRepeatTypes)
                && $showData['add_show_start_date'] != $localShowStart->format('Y-m-d')
            ) {
                // Start date has changed when repeat type is bi-weekly or monthly.
                // This screws up the repeating positions of show instances, so
                // we need to delete them (CC-2351)
                $this->deleteAllInstances($showId);
            }

            $currentRepeatType = $currentShowDay->getDbRepeatType();
            // only delete instances if the show being edited was already repeating
            // and the repeat type changed
            if ($currentRepeatType != -1 && $this->repeatType != $currentRepeatType) {
                $this->deleteAllInstances($showId);
                $this->deleteAllShowDays($showId);

            // when repeating by day of the month (1st, 2nd, etc.) we do not store the repeat week days
            } elseif ($currentRepeatType != 2) {
                // repeat type is the same, check if the days of the week are the same
                $repeatingDaysChanged = false;

                $showDays = [];
                foreach ($ccShowDays as $day) {
                    $showDays[] = $day->getDbDay();
                }

                if (count($showData['add_show_day_check']) == count($showDays)) {
                    // same number of days checked, lets see if they are the same numbers
                    $intersect = array_intersect($showData['add_show_day_check'], $showDays);
                    if (count($intersect) != count($showData['add_show_day_check'])) {
                        $repeatingDaysChanged = true;
                    }
                } else {
                    $repeatingDaysChanged = true;
                }

                if ($repeatingDaysChanged) {
                    $daysRemoved = array_diff($showDays, $showData['add_show_day_check']);
                    $newDays = array_diff($showData['add_show_day_check'], $showDays);
                    foreach ($newDays as $newDay) {
                        array_push($daysAdded, $newDay);
                    }

                    if (count($daysRemoved) > 0) {
                        // delete repeating show instances for the repeating
                        // days that were removed
                        /*if ($this->ccShow->isLinked()) {
                            $this->preserveLinkedShowContent();
                        }*/
                        $this->deleteRemovedShowDayInstances(
                            $daysRemoved,
                            $ccShowDays,
                            $showId
                        );
                    }
                }

                if (
                    $showData['add_show_start_date'] != $localShowStart->format('Y-m-d')
                    || $showData['add_show_start_time'] != $localShowStart->format('H:i')
                ) {
                    // start date has been pushed forward so we need to delete
                    // any instances of this show scheduled before the new start date
                    if ($showData['add_show_start_date'] > $localShowStart->format('Y-m-d')) {
                        $this->deleteInstancesBeforeDate($showData['add_show_start_date'], $showId);
                    }
                }
            }

            // get the endate from the past for this show.
            // check if this is null if "no end"
            $currentShowEndDateTime = $this->getRepeatingEndDate();

            if ($endDateTime && $currentShowEndDateTime != $endDateTime) {
                $endDate = clone $endDateTime;
                $endDate->setTimezone(new DateTimeZone('UTC'));

                // show's "No End" option was toggled
                // or the end date comes earlier
                if (is_null($currentShowEndDateTime) || ($endDateTime < $currentShowEndDateTime)) {
                    // "No End" option was unchecked so we need to delete the
                    // repeat instances that are scheduled after the new end date
                    // OR
                    // end date was pushed back so we have to delete any
                    // instances of this show scheduled after the new end date
                    $this->deleteInstancesFromDate($endDate->format('Y-m-d'), $showId);
                }
            }
        }

        return $daysAdded;
    }

    /*private function preserveLinkedShowContent()
    {
        // Get show content from any future linked instance. It doesn't
        // matter which instance since content is the same in all.
        //
        $ccShowInstance = $this->ccShow->getFutureCcShowInstancess()->getFirst();

        if (!$ccShowInstance) {
            return;
        }
        $ccSchedules = CcScheduleQuery::create()
            ->filterByDbInstanceId($ccShowInstance->getDbId())
            ->find();

       if (!$ccSchedules->isEmpty()) {
           $this->linkedShowContent = $ccSchedules;
       }
    }*/

    // returns a DateTime of the current show end date set to the timezone of the show.
    public function getRepeatingEndDate()
    {
        $sql = <<<'SQL'
SELECT last_show, timezone
FROM cc_show_days
WHERE show_id = :showId
ORDER BY last_show DESC
LIMIT 1
SQL;

        $query = Application_Common_Database::prepareAndExecute(
            $sql,
            ['showId' => $this->ccShow->getDbId()],
            'single'
        );

        $date = null;

        if ($query !== false && isset($query['last_show'])) {
            $date = new DateTime(
                $query['last_show'],
                new DateTimeZone($query['timezone'])
            );
        }

        return $date;
    }

    private function deleteInstancesFromDate($endDate, $showId)
    {
        $sql = <<<'SQL'
DELETE FROM cc_show_instances
WHERE date(starts) >= :endDate::DATE
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;
        Application_Common_Database::prepareAndExecute($sql, [
            ':endDate' => $endDate, ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
            ':showId' => $showId,
        ], 'execute');
    }

    private function deleteInstancesBeforeDate($newStartDate, $showId)
    {
        $sql = <<<'SQL'
DELETE
FROM cc_show_instances
WHERE date(starts) < :newStartDate::DATE
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;

        Application_Common_Database::prepareAndExecute($sql, [
            ':newStartDate' => $newStartDate, ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
            ':showId' => $showId,
        ], 'execute');
    }

    /**
     * Enter description here ...
     *
     * @param $daysRemoved array of days (days of the week) removed
     *     (days of the week are represented numerically
     *      0=>sunday, 1=>monday, 2=>tuesday, etc.)
     * @param $showDays array of ccShowDays objects
     * @param mixed $showId
     */
    private function deleteRemovedShowDayInstances($daysRemoved, $showDays, $showId)
    {
        $daysRemovedUTC = [];

        // convert the start day of the week to UTC
        foreach ($showDays as $showDay) {
            if (in_array($showDay->getDbDay(), $daysRemoved)) {
                $showDay->reload();
                $startDay = $showDay->getUTCStartDateAndTime();
                $daysRemovedUTC[] = $startDay->format('w');
            }
        }

        foreach ($daysRemoved as $day) {
            // delete the cc_show_day entries as well
            CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbDay($day)
                ->delete();
        }

        $uncheckedDays = pg_escape_string(implode(',', $daysRemovedUTC));

        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE EXTRACT(DOW FROM starts) IN ({$uncheckedDays})
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;

        Application_Common_Database::prepareAndExecute(
            $sql,
            [
                ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT), ':showId' => $showId,
            ],
            'execute'
        );
    }

    public function deleteShow($instanceId, $singleInstance = false)
    {
        $service_user = new Application_Service_UserService();
        $currentUser = $service_user->getCurrentUser();

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            if (!$currentUser->isAdminOrPM()) {
                throw new Exception('Permission denied');
            }

            $ccShowInstance = CcShowInstancesQuery::create()
                ->findPk($instanceId);
            if (!$ccShowInstance) {
                throw new Exception('Could not find show instance');
            }

            // Delete show images
            $showId = $ccShowInstance->getDbShowId();
            if (!$singleInstance && !Rest_ShowImageController::deleteShowImagesFromStor($showId)) {
                throw new Exception('Error deleting show images');
            }

            if ($singleInstance) {
                $ccShowInstances = [$ccShowInstance];
            } else {
                $ccShowInstances = CcShowInstancesQuery::create()
                    ->filterByDbShowId($showId)
                    ->filterByDbStarts($ccShowInstance->getDbStarts(), Criteria::GREATER_EQUAL)
                    ->find();
            }

            if (gmdate(DEFAULT_TIMESTAMP_FORMAT) <= $ccShowInstance->getDbEnds()) {
                $this->deleteShowInstances($ccShowInstances, $showId);
            } else {
                throw new Exception('Cannot delete a show instance in the past');
            }

            Application_Model_StoredFile::updatePastFilesIsScheduled();

            Application_Model_RabbitMq::PushSchedule();

            $con->commit();

            return $showId;
        } catch (Exception $e) {
            $con->rollback();
            Logging::info('Delete show instance failed');
            Logging::info($e->getMessage());

            return false;
        }
    }

    public function deleteShowInstances($ccShowInstances, $showId)
    {
        foreach ($ccShowInstances as $ccShowInstance) {
            $instanceId = $ccShowInstance->getDbId();

            $ccShowInstance
                ->setDbModifiedInstance(true)
                ->save();

            // delete the rebroadcasts of the removed recorded show
            if ($ccShowInstance->isRecorded()) {
                CcShowInstancesQuery::create()
                    ->filterByDbOriginalShow($instanceId)
                    ->delete();
            }

            // delete all files scheduled in cc_schedules table
            CcScheduleQuery::create()
                ->filterByDbInstanceId($instanceId)
                ->delete();
        }

        if ($this->checkToDeleteCcShow($showId)) {
            CcShowQuery::create()
                ->filterByDbId($showId)
                ->delete();
        // There is only one cc_show_instance if the user selects 'Delete This Instance'
        // There is more than one cc_show_instance if the user selects 'Delete This
        // Instance and All Following'. We only need to set the last_show value
        // when 'Delete This Instance and All Following' has been selected
        } elseif (count($ccShowInstances) > 1) {
            $this->setLastRepeatingShowDate($showId);
        }
    }

    private function setLastRepeatingShowDate($showId)
    {
        $ccShowInstances = CcShowInstancesQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbModifiedInstance(false)
            ->filterByDbRebroadcast(0)
            ->orderByDbStarts()
            ->find();

        /* We need to update the last_show in cc_show_days so the instances
         * don't get recreated as the user moves forward in the calendar
         */
        $lastShowDays = [];

        // get the show's timezone
        $ccShow = CcShowQuery::create()->findPk($showId);
        if ($ccShow->isRepeating()) {
            $showTimezone = $ccShow->getFirstRepeatingCcShowDay()->getDbTimezone();
        } else {
            $showTimezone = $ccShow->getFirstCcShowDay()->getDbTimezone();
        }

        /* Creates an array where the key is the day of the week (monday,
         * tuesday, etc.) and the value is the last show date for each
         * day of the week. We will use this array to update the last_show
         * for each cc_show_days entry of a cc_show
         */
        foreach ($ccShowInstances as $instance) {
            $instanceStartDT = $instance->getDbStarts(null);
            $instanceStartDT->setTimezone(new DateTimeZone($showTimezone));
            $lastShowDays[$instanceStartDT->format('w')] = $instanceStartDT;
        }

        foreach ($lastShowDays as $dayOfWeek => $lastShowStartDT) {
            $ccShowDay = CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbDay($dayOfWeek)
                ->filterByDbRepeatType(-1, Criteria::NOT_EQUAL)
                ->findOne();

            if (isset($ccShowDay)) {
                $lastShowStartDT->setTimeZone(new DateTimeZone(
                    $ccShowDay->getDbTimezone()
                ));
                $lastShowEndDT = Application_Service_CalendarService::addDeltas(
                    $lastShowStartDT,
                    1,
                    0
                );

                $ccShowDay
                    ->setDbLastShow($lastShowEndDT->format('Y-m-d'))
                    ->save();
            }
        }

        // NOTE: Some cc_show_day records may not get updated because there may not be an instance
        // left on one of the repeating days so we have to find the entries where the last_show is
        // still null
        $showDays = CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbRepeatType(0, Criteria::GREATER_EQUAL)
            ->filterByDbLastShow(null)
            ->find();
        foreach ($showDays as $showDay) {
            $showDay->setDbLastShow($showDay->getDbFirstShow())->save();
        }
    }

    private function checkToDeleteCcShow($showId)
    {
        // check if there are any non deleted show instances remaining.
        $ccShowInstances = CcShowInstancesQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbModifiedInstance(false)
            ->filterByDbRebroadcast(0)
            ->orderByDbStarts()
            ->find();

        if ($ccShowInstances->isEmpty()) {
            return true;
        }

        return false;
    }

    private function deleteAllInstances($showId)
    {
        $sql = <<<'SQL'
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;
        Application_Common_Database::prepareAndExecute(
            $sql,
            [
                ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
                ':showId' => $showId,
            ],
            'execute'
        );
    }

    private function deleteAllRepeatInstances($currentShowDay, $showId)
    {
        $firstShow = $currentShowDay->getUTCStartDateAndTime();

        $sql = <<<'SQL'
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
  AND starts != :firstShow
SQL;
        Application_Common_Database::prepareAndExecute(
            $sql,
            [
                ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
                ':showId' => $showId,
                ':firstShow' => $firstShow->format(DEFAULT_TIMESTAMP_FORMAT),
            ],
            'execute'
        );
    }

    /**
     * Determines what the show end date should be based on
     * the form data.
     *
     * @param $showData add/edit show form data
     *
     * @return DateTime object in user's local timezone
     */
    private function calculateEndDate($showData)
    {
        // if no end return null
        if ($showData['add_show_no_end']) {
            $endDate = null;
        }
        // if the show is repeating & ends, then return the end date
        elseif ($showData['add_show_repeats']) {
            $endDate = new DateTime(
                $showData['add_show_end_date'],
                new DateTimeZone($showData['add_show_timezone'])
            );
            $endDate->add(new DateInterval('P1D'));
        }
        // the show doesn't repeat, so add one day to the start date.
        else {
            $endDate = new DateTime(
                $showData['add_show_start_date'],
                new DateTimeZone($showData['add_show_timezone'])
            );
            $endDate->add(new DateInterval('P1D'));
        }

        return $endDate;
    }

    private function updateScheduleStartEndTimes($showData)
    {
        $showId = $this->ccShow->getDbId();
        // DateTime in show's local time
        $newStartDateTime = new DateTime(
            $showData['add_show_start_date'] . ' ' .
                $showData['add_show_start_time'],
            new DateTimeZone($showData['add_show_timezone'])
        );

        $diff = $this->calculateShowStartDiff(
            $newStartDateTime,
            $this->origCcShowDay->getLocalStartDateAndTime()
        );

        Application_Service_SchedulerService::updateScheduleStartTime(
            $this->instanceIdsForScheduleUpdates,
            $diff
        );
    }

    /**
     * Returns the difference in seconds between a show's new and
     * old start time.
     *
     * @param $newStartDateTime DateTime object
     * @param $oldStartDateTime DateTime object
     */
    private function calculateShowStartDiff($newStartDateTime, $oldStartDateTime)
    {
        return $newStartDateTime->getTimestamp() - $oldStartDateTime->getTimestamp();
    }

    /**
     * Updates the start and end time for cc_show_instances.
     *
     * @param $showData edit show form data
     * @param mixed $diff
     */
    private function updateInstanceStartEndTime($diff)
    {
        $sql = <<<'SQL'
UPDATE cc_show_instances
SET starts = starts + :diff1::INTERVAL,
    ends = ends + :diff2::INTERVAL
WHERE show_id = :showId
  AND starts > :timestamp::TIMESTAMP
SQL;

        Application_Common_Database::prepareAndExecute(
            $sql,
            [
                ':diff1' => $diff, ':diff2' => $diff,
                ':showId' => $this->ccShow->getDbId(), ':timestamp' => gmdate(DEFAULT_TIMESTAMP_FORMAT),
            ],
            'execute'
        );
    }

    /**
     * Enter description here ...
     *
     * @param ccShowDays $showDay
     * @param DateTime   $showStartDate user's local time
     * @param mixed      $instanceId
     */
    private function createRebroadcastInstances($showDay, $showStartDate, $instanceId)
    {
        $currentUtcTimestamp = gmdate(DEFAULT_TIMESTAMP_FORMAT);
        $showId = $this->ccShow->getDbId();

        $sql = 'SELECT * FROM cc_show_rebroadcast WHERE show_id=:show_id';
        $rebroadcasts = Application_Common_Database::prepareAndExecute(
            $sql,
            [':show_id' => $showId],
            'all'
        );

        foreach ($rebroadcasts as $rebroadcast) {
            $days = explode(' ', $rebroadcast['day_offset']);
            $time = explode(':', $rebroadcast['start_time']);
            $offset = ['days' => $days[0], 'hours' => $time[0], 'mins' => $time[1]];

            [$utcStartDateTime, $utcEndDateTime] = $this->createUTCStartEndDateTime(
                $showStartDate,
                $showDay->getDbDuration(),
                $offset
            );

            if ($utcStartDateTime->format(DEFAULT_TIMESTAMP_FORMAT) > $currentUtcTimestamp) {
                $ccShowInstance = new CcShowInstances();
                $ccShowInstance->setDbShowId($showId);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord(0);
                $ccShowInstance->setDbRebroadcast(1);
                $ccShowInstance->setDbOriginalShow($instanceId);
                $ccShowInstance->save();
            }
        }
    }

    /**
     * Sets a single cc_show_instance table row.
     *
     * @param mixed $showDay
     * @param mixed $populateUntil
     */
    private function createNonRepeatingInstance($showDay, $populateUntil)
    {
        // DateTime object
        $start = $showDay->getLocalStartDateAndTime();

        [$utcStartDateTime, $utcEndDateTime] = $this->createUTCStartEndDateTime(
            $start,
            $showDay->getDbDuration()
        );

        if ($utcStartDateTime->getTimestamp() < $populateUntil->getTimestamp()) {
            $ccShowInstance = new CcShowInstances();
            if ($this->isUpdate) {
                // use original cc_show_day object to get the current cc_show_instance
                $origStartDateTime = new DateTime(
                    $this->origCcShowDay->getDbFirstShow() . ' ' . $this->origCcShowDay->getDbStartTime(),
                    new DateTimeZone($this->origCcShowDay->getDbTimezone())
                );
                $origStartDateTime->setTimezone(new DateTimeZone('UTC'));
                $ccShowInstance = $this->getInstance($origStartDateTime);
                if (!$ccShowInstance) {
                    throw new Exception('Could not find show instance with start time: ' . $origStartDateTime->format(DEFAULT_TIMESTAMP_FORMAT));
                }
            }

            $ccShowInstance->setDbShowId($this->ccShow->getDbId());
            $ccShowInstance->setDbStarts($utcStartDateTime);
            $ccShowInstance->setDbEnds($utcEndDateTime);
            $ccShowInstance->setDbRecord($showDay->getDbRecord());
            $ccShowInstance->save();

            if ($this->isRebroadcast) {
                $this->createRebroadcastInstances($showDay, $start, $ccShowInstance->getDbId());
            }
        }

        return $ccShowInstance;
    }

    /**
     * Sets multiple cc_show_instances table rows.
     *
     * @param unknown_type $showDay
     * @param unknown_type $populateUntil
     * @param unknown_type $repeatInterval
     * @param unknown_type $isRebroadcast
     * @param mixed        $repeatType
     * @param null|mixed   $daysAdded
     */
    private function createWeeklyRepeatInstances(
        $showDay,
        $populateUntil,
        $repeatType,
        $repeatInterval,
        $daysAdded = null
    ) {
        $show_id = $showDay->getDbShowId();
        $first_show = $showDay->getDbFirstShow(); // non-UTC
        $last_show = $showDay->getDbLastShow(); // non-UTC
        $duration = $showDay->getDbDuration();
        $day = $showDay->getDbDay();
        $record = $showDay->getDbRecord();
        $timezone = $showDay->getDbTimezone();

        // DateTime local
        $start = $this->getNextRepeatingPopulateStartDateTime($showDay);

        if (is_null($repeatInterval) && $repeatType == REPEAT_MONTHLY_WEEKLY) {
            $repeatInterval = self::getMonthlyWeeklyRepeatInterval($start, $timezone);
        }

        // DatePeriod in user's local time
        $datePeriod = $this->getDatePeriod(
            $start,
            $timezone,
            $last_show,
            $repeatInterval,
            $populateUntil
        );

        if ($last_show) {
            $utcLastShowDateTime = new DateTime($last_show, new DateTimeZone($timezone));
            $utcLastShowDateTime->setTimezone(new DateTimeZone('UTC'));
        } else {
            $utcLastShowDateTime = null;
        }

        $previousDate = clone $start;

        foreach ($datePeriod as $date) {
            [$utcStartDateTime, $utcEndDateTime] = $this->createUTCStartEndDateTime(
                $date,
                $duration
            );
            /*
             * Make sure start date is less than populate until date AND
             * last show date is null OR start date is less than last show date
             */
            if (
                $utcStartDateTime <= $populateUntil
                && (is_null($utcLastShowDateTime) || $utcStartDateTime < $utcLastShowDateTime)
            ) {
                $lastCreatedShow = clone $utcStartDateTime;
                /* There may not always be an instance when editing a show
                 * This will be the case when we are adding a new show day to
                 * a repeating show
                 */
                if ($this->isUpdate) {
                    if ($this->hasInstance($utcStartDateTime)) {
                        $ccShowInstance = $this->getInstance($utcStartDateTime);
                        $newInstance = false;
                    } else {
                        $newInstance = true;
                        $ccShowInstance = new CcShowInstances();
                    }
                } else {
                    $newInstance = true;
                    $ccShowInstance = new CcShowInstances();
                }

                /* When editing the start/end time of a repeating show, we don't want to
                 * change shows that are in the past so we check the end time.
                 */
                if ($newInstance || $ccShowInstance->getDbEnds() > gmdate(DEFAULT_TIMESTAMP_FORMAT)) {
                    $ccShowInstance->setDbShowId($show_id);
                    $ccShowInstance->setDbStarts($utcStartDateTime);
                    $ccShowInstance->setDbEnds($utcEndDateTime);
                    $ccShowInstance->setDbRecord($record);
                    $ccShowInstance->save();
                }

                if ($newInstance) {
                    array_push($this->newInstanceIdsCreated[$show_id], $ccShowInstance->getDbId());
                }

                if ($this->isRebroadcast) {
                    $this->createRebroadcastInstances($showDay, $date, $ccShowInstance->getDbId());
                }
            }
            $previousDate = clone $date;
        }

        /* We need to set the next populate date for repeat shows so when a user
         * moves forward in the calendar we know when to start generating new
         * show instances.
         * If $utcStartDateTime is not set then we know zero new shows were
         * created and we shouldn't update the next populate date.
         */
        if (isset($lastCreatedShow)) {
            /* Set UTC to local time before setting the next repeat date. If we don't
             * the next repeat date might be scheduled for the following day
             * THIS MUST BE IN THE TIMEZONE THE SHOW WAS CREATED IN */
            $lastCreatedShow->setTimezone(new DateTimeZone($timezone));
            $nextDate = $lastCreatedShow->add($repeatInterval);
            $this->setNextRepeatingShowDate($nextDate->format('Y-m-d'), $day, $show_id);
        }
    }

    private function createMonthlyRepeatInstances($showDay, $populateUntil)
    {
        $show_id = $showDay->getDbShowId();
        $first_show = $showDay->getDbFirstShow(); // non-UTC
        $last_show = $showDay->getDbLastShow(); // non-UTC
        $duration = $showDay->getDbDuration();
        $day = $showDay->getDbDay();
        $record = $showDay->getDbRecord();
        $timezone = $showDay->getDbTimezone();

        // DateTime local
        $start = $this->getNextRepeatingPopulateStartDateTime($showDay);
        if (isset($last_show)) {
            $end = new DateTime($last_show, new DateTimeZone($timezone));
        } else {
            $end = $populateUntil;
        }

        // We will only need this if the repeat type is MONTHLY_WEEKLY
        [$weekNumberOfMonth, $dayOfWeek] =
            self::getMonthlyWeeklyRepeatInterval(
                new DateTime($first_show, new DateTimeZone($timezone))
            );

        $this->repeatType = $showDay->getDbRepeatType();

        if ($last_show) {
            $utcLastShowDateTime = new DateTime($last_show, new DateTimeZone($timezone));
            $utcLastShowDateTime->setTimezone(new DateTimeZone('UTC'));
        } else {
            $utcLastShowDateTime = null;
        }

        while ($start->getTimestamp() < $end->getTimestamp()) {
            [$utcStartDateTime, $utcEndDateTime] = $this->createUTCStartEndDateTime(
                $start,
                $duration
            );
            /*
             * Make sure start date is less than populate until date AND
             * last show date is null OR start date is less than last show date
             */
            if (
                $utcStartDateTime->getTimestamp() <= $populateUntil->getTimestamp()
                && (is_null($utcLastShowDateTime)
                    || $utcStartDateTime->getTimestamp() < $utcLastShowDateTime->getTimestamp())
            ) {
                $lastCreatedShow = clone $utcStartDateTime;
                /* There may not always be an instance when editing a show
                 * This will be the case when we are adding a new show day to
                 * a repeating show
                 */
                if ($this->isUpdate && $this->hasInstance($utcStartDateTime)) {
                    $ccShowInstance = $this->getInstance($utcStartDateTime);
                    $newInstance = false;
                } else {
                    $newInstance = true;
                    $ccShowInstance = new CcShowInstances();
                }

                /* When editing the start/end time of a repeating show, we don't want to
                 * change shows that started in the past. So check the start time.
                 */
                if ($newInstance || $ccShowInstance->getDbStarts() > gmdate(DEFAULT_TIMESTAMP_FORMAT)) {
                    $ccShowInstance->setDbShowId($show_id);
                    $ccShowInstance->setDbStarts($utcStartDateTime);
                    $ccShowInstance->setDbEnds($utcEndDateTime);
                    $ccShowInstance->setDbRecord($record);
                    $ccShowInstance->save();
                }

                if ($newInstance) {
                    array_push($this->newInstanceIdsCreated[$show_id], $ccShowInstance->getDbId());
                }

                if ($this->isRebroadcast) {
                    $this->createRebroadcastInstances($showDay, $start, $ccShowInstance->getDbId());
                }
            }
            if ($this->repeatType == REPEAT_MONTHLY_WEEKLY) {
                $monthlyWeeklyStart = new DateTime(
                    $utcStartDateTime->format('Y-m'),
                    new DateTimeZone('UTC')
                );
                $monthlyWeeklyStart->add(new DateInterval('P1M'));
                $start = self::getNextMonthlyWeeklyRepeatDate(
                    $monthlyWeeklyStart,
                    $timezone,
                    $showDay->getDbStartTime(),
                    $weekNumberOfMonth,
                    $dayOfWeek
                );
            } else {
                $start = $this->getNextMonthlyMonthlyRepeatDate(
                    $start,
                    $timezone,
                    $showDay->getDbStartTime()
                );
            }
        }
        $this->setNextRepeatingShowDate($start->format('Y-m-d'), $day, $show_id);
    }

    /**
     * i.e. last thursday of each month
     * i.e. second monday of each month.
     *
     * @param string $showStart
     * @param string $timezone  user's local timezone
     */
    public static function getMonthlyWeeklyRepeatInterval($showStart)
    {
        $start = clone $showStart;
        $dayOfMonth = $start->format('j');
        $dayOfWeek = $start->format('l');
        $yearAndMonth = $start->format('Y-m');
        $firstDayOfWeek = strtotime($dayOfWeek . ' ' . $yearAndMonth);
        // if $dayOfWeek is Friday, what number of the month does
        // the first Friday fall on
        $numberOfFirstDayOfWeek = date('j', $firstDayOfWeek);

        $weekCount = 0;
        while ($dayOfMonth >= $numberOfFirstDayOfWeek) {
            ++$weekCount;
            $dayOfMonth -= 7;
        }

        switch ($weekCount) {
            case 1:
                $weekNumberOfMonth = 'first';

                break;

            case 2:
                $weekNumberOfMonth = 'second';

                break;

            case 3:
                $weekNumberOfMonth = 'third';

                break;

            case 4:
                $weekNumberOfMonth = 'fourth';

                break;

            case 5:
                $weekNumberOfMonth = 'fifth';

                break;
        }

        /* return DateInterval::createFromDateString(
            $weekNumberOfMonth." ".$dayOfWeek." of next month"); */
        return [$weekNumberOfMonth, $dayOfWeek];
    }

    /**
     * Enter description here ...
     *
     * @param $start user's local time
     * @param mixed $timezone
     * @param mixed $startTime
     */
    private function getNextMonthlyMonthlyRepeatDate($start, $timezone, $startTime)
    {
        $dt = new DateTime($start->format('Y-m'), new DateTimeZone($timezone));

        do {
            $dt->add(new DateInterval('P1M'));
        } while (!checkdate(intval($dt->format('m')), intval($start->format('d')), intval($dt->format('Y'))));

        $dt->setDate(intval($dt->format('Y')), intval($dt->format('m')), intval($start->format('d')));

        $startTime = explode(':', $startTime);
        $hours = isset($startTime[0]) ? $startTime[0] : '00';
        $minutes = isset($startTime[1]) ? $startTime[1] : '00';
        $seconds = isset($startTime[2]) ? $startTime[2] : '00';
        $dt->setTime(intval($hours), intval($minutes), intval($seconds));

        return $dt;
    }

    /**
     * Returns a DateTime object of when the next repeating show that repeats
     * monthly, by day of the week (i.e. every fourth Tuesday) should be created.
     *
     * @param DateTime $start
     *                           $start only has the year and month of the next show
     * @param string   $timezone
     * @param string (i.e. '14:30' $startTime
     * @param string (i.e. 'first', 'second') $weekNumberOfMonth
     * @param string (i.e. 'Monday') $dayOfWeek
     */
    public static function getNextMonthlyWeeklyRepeatDate(
        $start,
        $timezone,
        $startTime,
        $weekNumberOfMonth,
        $dayOfWeek
    ) {
        $dt = new DateTime($start->format('Y-m'), new DateTimeZone($timezone));
        $tempDT = clone $dt;
        $fifthWeekExists = false;
        do {
            $nextDT = date_create($weekNumberOfMonth . ' ' . $dayOfWeek .
                ' of ' . $tempDT->format('F') . ' ' . $tempDT->format('Y'));
            $nextDT->setTimezone(new DateTimeZone($timezone));

            /* We have to check if the next date is in the same month in case
             * the repeat day is in the fifth week of the month.
             * If it's not in the same month we know that a fifth week of
             * the next month does not exist. So let's skip it.
             */
            if ($tempDT->format('F') == $nextDT->format('F')) {
                $fifthWeekExists = true;
            }
            $tempDT->add(new DateInterval('P1M'));
        } while (!$fifthWeekExists);

        $dt = $nextDT;

        $startTime = explode(':', $startTime);
        $hours = isset($startTime[0]) ? $startTime[0] : 0;
        $minutes = isset($startTime[1]) ? $startTime[1] : 0;
        $seconds = isset($startTime[2]) ? $startTime[2] : 0;
        $dt->setTime(intval($hours), intval($minutes), intval($seconds));

        return $dt;
    }

    private function getNextRepeatingPopulateStartDateTime($showDay)
    {
        $nextPopDate = $showDay->getDbNextPopDate();
        $startTime = $showDay->getDbStartTime();

        if (isset($nextPopDate)) {
            return new DateTime($nextPopDate . ' ' . $startTime, new DateTimeZone($showDay->getDbTimezone()));
        }

        return new DateTime($showDay->getDbFirstShow() . ' ' . $startTime, new DateTimeZone($showDay->getDbTimezone()));
    }

    /**
     * Create a DatePeriod object in the user's local time
     * It will get converted to UTC before the show instance gets created.
     *
     * @param mixed $start
     * @param mixed $timezone
     * @param mixed $lastShow
     * @param mixed $repeatInterval
     * @param mixed $populateUntil
     */
    private function getDatePeriod($start, $timezone, $lastShow, $repeatInterval, $populateUntil)
    {
        if (isset($lastShow)) {
            $endDatePeriod = new DateTime($lastShow, new DateTimeZone($timezone));
        } else {
            $endDatePeriod = $populateUntil;
        }

        return new DatePeriod($start, $repeatInterval, $endDatePeriod);
    }

    private function hasInstance($starts)
    {
        return $this->getInstance($starts) ? true : false;
    }

    /**
     * Attempts to retrieve the cc_show_instance belonging to a cc_show
     * that starts at $starts. We have to pass in the start
     * time in case the show is repeating.
     *
     * Returns the instance if one was found (one that is not a recording
     * and modified instance is false (has not been deleted))
     *
     * @param mixed $starts
     */
    private function getInstance($starts)
    {
        $temp = clone $starts;
        $temp->setTimezone(new DateTimeZone($this->oldShowTimezone));
        $temp->setTime($this->localShowStartHour, $this->localShowStartMin);

        $temp->setTimezone(new DateTimeZone('UTC'));

        $ccShowInstance = CcShowInstancesQuery::create()
            ->filterByDbStarts($temp->format(DEFAULT_TIMESTAMP_FORMAT), Criteria::EQUAL)
            ->filterByDbShowId($this->ccShow->getDbId(), Criteria::EQUAL)
            // ->filterByDbModifiedInstance(false, Criteria::EQUAL)
            ->filterByDbRebroadcast(0, Criteria::EQUAL)
            ->limit(1)
            ->find();

        if ($ccShowInstance->isEmpty()) {
            return false;
        }

        return $ccShowInstance[0];
    }

    private function hasCcShowDay($repeatType, $day)
    {
        return $this->getCcShowDay($repeatType, $day) ? true : false;
    }

    private function getCcShowDay($repeatType, $day)
    {
        $ccShowDay = CcShowDaysQuery::create()
            ->filterByDbShowId($this->ccShow->getDbId())
            ->filterByDbDay($day)
            ->filterByDbRepeatType($repeatType)
            ->limit(1)
            ->find();

        if ($ccShowDay->isEmpty()) {
            return false;
        }

        return $ccShowDay[0];
    }

    /**
     * Sets the fields for a cc_show table row.
     *
     * @param $ccShow
     * @param mixed $showData
     */
    public function setCcShow($showData)
    {
        if (!$this->isUpdate) {
            $ccShow = new CcShow();
        } else {
            $ccShow = CcShowQuery::create()->findPk($showData['add_show_id']);
        }

        $ccShow->setDbName($showData['add_show_name']);
        $ccShow->setDbDescription($showData['add_show_description']);
        $ccShow->setDbUrl($showData['add_show_url']);
        $ccShow->setDbGenre($showData['add_show_genre']);
        $ccShow->setDbColor($showData['add_show_color']);
        $ccShow->setDbBackgroundColor($showData['add_show_background_color']);
        $ccShow->setDbLiveStreamUsingAirtimeAuth($showData['cb_airtime_auth'] == 1);
        $ccShow->setDbLiveStreamUsingCustomAuth($showData['cb_custom_auth'] == 1);
        $ccShow->setDbLiveStreamUser($showData['custom_username']);
        $ccShow->setDbLiveStreamPass($showData['custom_password']);
        $ccShow->setDbHasAutoPlaylist($showData['add_show_has_autoplaylist'] == 1);
        $ccShow->setDbAutoPlaylistRepeat($showData['add_show_autoplaylist_repeat'] == 1);
        // added to prevent errors with insert due to a lack of data
        if ($showData['add_show_autoplaylist_id'] != '') {
            $ccShow->setDbAutoPlaylistId($showData['add_show_autoplaylist_id']);
        }

        // Here a user has edited a show and linked it.
        // We need to grab the existing show instances ids and fill their content
        // with the content from the show instance that was clicked on to edit the show.
        // We do this because linked show instances need to have the same content in each.
        if ($this->isUpdate && (!$ccShow->getDbLinked() && $showData['add_show_linked'])) {
            $existingShowInstanceIds = $ccShow->getFutureInstanceIds(new Criteria());
            Application_Service_SchedulerService::fillLinkedInstances($ccShow, $existingShowInstanceIds, $showData['add_show_instance_id']);
        }
        $ccShow->setDbLinked($showData['add_show_linked']);

        $ccShow->save();
        $this->ccShow = $ccShow;
    }

    /**
     * Sets the fields for a cc_show_days table row.
     *
     * @param $showId
     * @param $userId
     * @param $repeatType
     * @param $isRecorded
     * @param $showDay ccShowDay object we are setting values on
     * @param mixed $showData
     */
    private function setCcShowDays($showData)
    {
        $showId = $this->ccShow->getDbId();

        $startDateTime = new DateTime(
            $showData['add_show_start_date'] . ' ' . $showData['add_show_start_time'],
            new DateTimeZone($showData['add_show_timezone'])
        );

        $endDateTime = $this->calculateEndDate($showData);
        if (!is_null($endDateTime)) {
            $endDate = $endDateTime->format('Y-m-d');
        } else {
            $endDate = null;
        }

        // Our calculated start DOW must be used for non repeating since a day has not been selected.
        // For all repeating shows, only the selected days of the week will be repeated on.
        $startDow = $startDateTime->format('w');
        if (!$showData['add_show_repeats']) {
            $showData['add_show_day_check'] = [$startDow];
        }

        // Don't set day for monthly repeat type, it's invalid
        if ($showData['add_show_repeats'] && $showData['add_show_repeat_type'] == 2) {
            if ($this->isUpdate) {
                $showDay = CcShowDaysQuery::create()
                    ->filterByDbShowId($showId)
                    ->filterByDbRepeatType($this->origCcShowDay->getDbRepeatType())
                    ->findOne();
                if (!$showDay) {
                    // repeat type changed so we have to create a new show_day rule
                    $showDay = new CcShowDays();
                }
            } else {
                $showDay = new CcShowDays();
            }

            $showDay->setDbFirstShow($startDateTime->format('Y-m-d'));
            $showDay->setDbLastShow($endDate);
            $showDay->setDbStartTime($startDateTime->format('H:i:s'));
            $showDay->setDbTimezone($showData['add_show_timezone']);
            $showDay->setDbDuration($showData['add_show_duration']);
            $showDay->setDbRepeatType($this->repeatType);
            $showDay->setDbShowId($showId);
            $showDay->setDbRecord($this->isRecorded);
            // in case we are editing a show we need to set this to the first show
            // so when editing, the date period iterator will start from the beginning
            $showDay->setDbNextPopDate($startDateTime->format('Y-m-d'));
            $showDay->save();
        } else {
            foreach ($showData['add_show_day_check'] as $day) {
                $daysAdd = 0;
                $startDateTimeClone = clone $startDateTime;
                if ($startDow !== $day) {
                    if ($startDow > $day) {
                        $daysAdd = 6 - $startDow + 1 + $day;
                    } else {
                        $daysAdd = $day - $startDow;
                    }

                    $startDateTimeClone->add(new DateInterval('P' . $daysAdd . 'D'));
                }
                if (is_null($endDate) || $startDateTimeClone->getTimestamp() <= $endDateTime->getTimestamp()) {
                    if ($this->isUpdate) {
                        if (
                            $this->origCcShowDay->getDbRepeatType() == 2
                            || $this->origCcShowDay->getDbRepeatType() == 3
                        ) {
                            $day = null;
                        } elseif (!$this->origShowRepeatStatus) {
                            // keep current show day to use for updating cc_show_day rule
                            $keepDay = $day;
                            $day = $this->origCcShowDay->getDbDay();
                        }

                        $showDay = CcShowDaysQuery::create()
                            ->filterByDbShowId($showId)
                            ->filterByDbRepeatType($this->origCcShowDay->getDbRepeatType())
                            ->filterByDbDay($day)
                            ->findOne();
                        if (!$showDay) {
                            // if no show day object was found it is because a new
                            // repeating day of the week was added OR the repeat
                            // type has changed
                            $showDay = new CcShowDays();
                        }

                        if (isset($keepDay)) {
                            $day = $keepDay;
                        }
                    } else {
                        $showDay = new CcShowDays();
                    }

                    $showDay->setDbFirstShow($startDateTimeClone->format('Y-m-d'));
                    $showDay->setDbLastShow($endDate);
                    $showDay->setDbStartTime($startDateTimeClone->format('H:i'));
                    $showDay->setDbTimezone($showData['add_show_timezone']);
                    $showDay->setDbDuration($showData['add_show_duration']);
                    $showDay->setDbDay($day);
                    $showDay->setDbRepeatType($this->repeatType);
                    $showDay->setDbShowId($showId);
                    $showDay->setDbRecord($this->isRecorded);
                    // in case we are editing a show we need to set this to the first show
                    // so when editing, the date period iterator will start from the beginning
                    $showDay->setDbNextPopDate($startDateTimeClone->format('Y-m-d'));
                    $showDay->save();
                }
            }
        }
    }

    /**
     * Deletes all the cc_show_rebroadcast entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show specs.
     */
    private function deleteCcShowRebroadcasts()
    {
        CcShowRebroadcastQuery::create()->filterByDbShowId($this->ccShow->getDbId())->delete();
    }

    /**
     * Sets the fields for a cc_show_rebroadcast table row.
     *
     * @param $showId
     * @param $repeatType
     * @param $isRecorded
     * @param mixed $showData
     */
    private function setCcShowRebroadcasts($showData)
    {
        $showId = $this->ccShow->getDbId();

        if (($this->isRecorded && $showData['add_show_rebroadcast']) && ($this->repeatType != -1)) {
            for ($i = 1; $i <= MAX_REBROADCAST_DATES; ++$i) {
                if ($showData['add_show_rebroadcast_date_' . $i]) {
                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($showData['add_show_rebroadcast_date_' . $i]);
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_' . $i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        } elseif ($this->isRecorded && $showData['add_show_rebroadcast'] && ($this->repeatType == -1)) {
            for ($i = 1; $i <= MAX_REBROADCAST_DATES; ++$i) {
                if ($showData['add_show_rebroadcast_date_absolute_' . $i]) {
                    $rebroadcastDate = new DateTime($showData["add_show_rebroadcast_date_absolute_{$i}"]);
                    $startDate = new DateTime($showData['add_show_start_date']);
                    $offsetDays = $startDate->diff($rebroadcastDate);

                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($offsetDays->format('%a days'));
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_absolute_' . $i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }
    }

    /**
     * Deletes all the cc_show_hosts entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show specs.
     */
    private function deleteCcShowHosts()
    {
        CcShowHostsQuery::create()->filterByDbShow($this->ccShow->getDbId())->delete();
    }

    /**
     * Sets the fields for a cc_show_hosts table row.
     *
     * @param $showId
     * @param mixed $showData
     */
    private function setCcShowHosts($showData)
    {
        if (is_array($showData['add_show_hosts'])) {
            foreach ($showData['add_show_hosts'] as $host) {
                $showHost = new CcShowHosts();
                $showHost->setDbShow($this->ccShow->getDbId());
                $showHost->setDbHost($host);
                $showHost->save();
            }
        }
    }

    /**
     * Gets the date and time shows (particularly repeating shows)
     * can be populated until.
     *
     * @return DateTime object
     */
    private static function getPopulateShowUntilDateTIme()
    {
        $populateUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        if (is_null($populateUntil)) {
            $populateUntil = new DateTime('now', new DateTimeZone('UTC'));
            Application_Model_Preference::SetShowsPopulatedUntil($populateUntil);
        }

        return $populateUntil;
    }

    /**
     * Enter description here ...
     *
     * @param DateTime $showStart user's local time
     * @param string   $duration  time interval (h)h:(m)m(:ss)
     * @param string   $timezone  "Europe/Prague"
     * @param array    $offset    (days, hours, mins) used for rebroadcast shows
     *
     * @return array of 2 DateTime objects, start/end time of the show in UTC
     */
    private function createUTCStartEndDateTime($showStart, $duration, $offset = null)
    {
        $startDateTime = clone $showStart;
        $timezone = $startDateTime->getTimezone();

        if (isset($offset)) {
            // $offset["hours"] and $offset["mins"] represents the start time
            // of a rebroadcast show
            $startDateTime = new DateTime($startDateTime->format('Y-m-d') . ' ' .
                $offset['hours'] . ':' . $offset['mins'], $timezone);
            $startDateTime->add(new DateInterval("P{$offset['days']}D"));
        }

        $endDateTime = clone $startDateTime;
        $duration = explode(':', $duration);
        [$hours, $mins] = array_slice($duration, 0, 2);
        $endDateTime->add(new DateInterval("PT{$hours}H{$mins}M"));

        $startDateTime->setTimezone(new DateTimeZone('UTC'));
        $endDateTime->setTimezone(new DateTimeZone('UTC'));

        return [$startDateTime, $endDateTime];
    }

    /**
     * Show instances for repeating shows only get created up
     * until what is visible on the calendar. We need to set the
     * date for when the next repeating show instance should be created
     * as the user browses the calendar further.
     *
     * @param mixed $nextDate
     * @param mixed $day
     * @param mixed $showId
     */
    private function setNextRepeatingShowDate($nextDate, $day, $showId)
    {
        $nextInfo = explode(' ', $nextDate);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbDay($day)
            ->filterByDbRepeatType(-1, Criteria::NOT_EQUAL)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }
}
