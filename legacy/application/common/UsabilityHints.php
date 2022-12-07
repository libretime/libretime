<?php

declare(strict_types=1);

class Application_Common_UsabilityHints
{
    /**
     * @param $userPath User's current location in Airtime (i.e. /Plupload)
     *
     * @return string
     */
    public static function getUsabilityHint($userPath = null)
    {
        // We want to display hints in this order:
        // 1. Check if files are uploaded
        // 2. Check if a show is scheduled
        // 3. Check if current or next show needs content

        // Once the user is on the page linked to from the hint we want to
        // display a new message further describing what to do. Once this
        // action has been done we can hide the message and get the next
        // usability hint, if there is one.

        $userIsOnCalendarPage = false;
        $userIsOnAddMediaPage = false;
        $userIsOnShowbuilderPage = false;
        $userIsSuperAdmin = Application_Model_User::getCurrentUser()->isSuperAdmin();

        // If $userPath is set the request came from AJAX so the user's
        // current location inside Airtime gets passed in to this function.
        if (!is_null($userPath)) {
            // We check if the controller names are in the user's current location
            // so we can ignore leading or trailing slashes, special characters like '#',
            // and additional controller action names like '/user/add-user'

            if (strpos(strtolower($userPath), 'plupload') !== false) {
                $userIsOnAddMediaPage = true;
            }

            if (strpos(strtolower($userPath), 'schedule') !== false) {
                $userIsOnCalendarPage = true;
            }

            if (strpos(strtolower($userPath), 'showbuilder') !== false) {
                $userIsOnShowbuilderPage = true;
            }
        } else {
            // If $userPath is not set the request came from inside Airtime so
            // we can use Zend's Front Controller to get the user's current location.
            $currentController = strtolower(Zend_Controller_Front::getInstance()->getRequest()->getControllerName());

            if ($currentController == 'schedule') {
                $userIsOnCalendarPage = true;
            }

            if ($currentController == 'plupload') {
                $userIsOnAddMediaPage = true;
            }

            if ($currentController == 'showbuilder') {
                $userIsOnShowbuilderPage = true;
            }
        }

        if (self::zeroFilesUploaded()) {
            if ($userIsOnAddMediaPage) {
                return _('Upload some tracks below to add them to your library!');
            }

            return sprintf(
                _("It looks like you haven't uploaded any audio files yet. %sUpload a file now%s."),
                '<a href="/plupload">',
                '</a>'
            );
        }
        if (!self::isFutureOrCurrentShowScheduled()) {
            if ($userIsOnCalendarPage) {
                return _("Click the 'New Show' button and fill out the required fields.");
            }

            return sprintf(
                _("It looks like you don't have any shows scheduled. %sCreate a show now%s."),
                '<a href="/schedule">',
                '</a>'
            );
        }
        if (self::isCurrentShowEmpty()) {
            // If the current show is linked users cannot add content to it so we have to provide a different message.
            if (self::isCurrentShowLinked()) {
                if ($userIsOnCalendarPage) {
                    return _("To start broadcasting, cancel the current linked show by clicking on it and selecting 'Cancel Show'.");
                }

                return sprintf(_('Linked shows need to be filled with tracks before it starts. To start broadcasting cancel the current linked show and schedule an unlinked show.
                    %sCreate an unlinked show now%s.'), '<a href="/schedule">', '</a>');
            }
            if ($userIsOnCalendarPage) {
                return _("To start broadcasting, click on the current show and select 'Schedule Tracks'");
            }

            return sprintf(
                _('It looks like the current show needs more tracks. %sAdd tracks to your show now%s.'),
                '<a href="/schedule">',
                '</a>'
            );
        }
        if (!self::getCurrentShow() && self::isNextShowEmpty()) {
            if ($userIsOnCalendarPage) {
                return _("Click on the show starting next and select 'Schedule Tracks'");
            }

            return sprintf(
                _('It looks like the next show is empty. %sAdd tracks to your show now%s.'),
                '<a href="/schedule">',
                '</a>'
            );
        }

        return '';
    }

    /**
     * Returns true if no files have been uploaded.
     */
    private static function zeroFilesUploaded()
    {
        $fileCount = CcFilesQuery::create()
            ->filterByDbFileExists(true)
            ->filterByDbHidden(false)
            ->count();

        if ($fileCount == 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if there is at least one show currently scheduled
     * or in the future.
     */
    private static function isFutureOrCurrentShowScheduled()
    {
        $futureShow = self::getNextFutureShow();
        $currentShow = self::getCurrentShow();

        if (is_null($futureShow) && is_null($currentShow)) {
            return false;
        }

        return true;
    }

    private static function isCurrentShowEmpty()
    {
        $currentShow = self::getCurrentShow();

        if (is_null($currentShow)) {
            return false;
        }
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $scheduledTracks = CcScheduleQuery::create()
            ->filterByDbInstanceId($currentShow->getDbId())
            ->filterByDbEnds($now, Criteria::GREATER_EQUAL)
            ->find();
        if ($scheduledTracks->count() == 0) {
            return true;
        }

        return false;
    }

    private static function isNextShowEmpty()
    {
        $futureShow = self::getNextFutureShow();

        if (is_null($futureShow)) {
            return false;
        }
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $scheduledTracks = CcScheduleQuery::create()
            ->filterByDbInstanceId($futureShow->getDbId())
            ->filterByDbStarts($now, Criteria::GREATER_EQUAL)
            ->find();
        if ($scheduledTracks->count() == 0) {
            return true;
        }

        return false;
    }

    private static function getCurrentShow()
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        return CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::LESS_THAN)
            ->filterByDbEnds($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->findOne();
    }

    private static function getNextFutureShow()
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        return CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->orderByDbStarts()
            ->findOne();
    }

    private static function isCurrentShowLinked()
    {
        $currentShow = self::getCurrentShow();
        if (!is_null($currentShow)) {
            $show = CcShowQuery::create()
                ->filterByDbId($currentShow->getDbShowId())
                ->findOne();
            if ($show->isLinked()) {
                return true;
            }

            return false;
        }

        return false;
    }
}
