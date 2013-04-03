<?php

class Application_Service_CalendarService
{
    private $currentUser;
    private $ccShowInstance;
    private $showId;

    public function __construct($instanceId = null)
    {
        if (!is_null($instanceId)) {
            $this->ccShowInstance = CcShowInstancesQuery::create()->findPk($instanceId);
            $this->showId = $this->ccShowInstance->getDbShowId();
        }

        $service_user = new Application_Service_UserService();
        $this->currentUser = $service_user->getCurrentUser();
    }

    /**
     *
     * Enter description here ...
     */
    public function makeContextMenu()
    {
        $menu = array();
        $now = time();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $isAdminOrPM = $this->currentUser->isAdminOrPM();
        $isHostOfShow = $this->currentUser->isHostOfShow($this->showId);

        //DateTime objects in UTC
        $startDT = $this->ccShowInstance->getDbStarts(null);
        $endDT = $this->ccShowInstance->getDbEnds(null);

        //timestamps
        $start = $startDT->getTimestamp();
        $end = $endDT->getTimestamp();

        //show has ended
        if ($now > $end) {
            if ($this->ccShowInstance->isRecorded()) {

                $ccFile = $this->ccShowInstance->getCcFiles();

                $menu["view_recorded"] = array(
                    "name" => _("View Recorded File Metadata"),
                    "icon" => "overview",
                    "url" => $baseUrl."library/edit-file-md/id/".$ccFile->getDbId());

                //recorded show can be uploaded to soundcloud
                if (Application_Model_Preference::GetUploadToSoundcloudOption()) {
                    $scid = $ccFile->getDbSoundcloudId();

                    if ($scid > 0) {
                        $menu["soundcloud_view"] = array(
                            "name" => _("View on Soundcloud"),
                            "icon" => "soundcloud",
                            "url" => $ccFile->getDbSoundcloudLinkToFile());
                    }

                    $text = is_null($scid) ? _('Upload to SoundCloud') : _('Re-upload to SoundCloud');
                    $menu["soundcloud_upload"] = array(
                        "name"=> $text,
                        "icon" => "soundcloud");
                }
            }
        } else {
            //Show content can be modified from the calendar if:
            // the show has not started,
            // the user is admin or hosting the show,
            // the show is not recorded or rebroadcasted
            if ($now < $start && ($isAdminOrPM || $isHostOfShow) &&
            !$this->ccShowInstance->isRecorded() && !$this->ccShowInstance->isRebroadcast()) {

                $menu["schedule"] = array(
                        "name"=> _("Add / Remove Content"),
                        "icon" => "add-remove-content",
                        "url" => $baseUrl."showbuilder/builder-dialog/");

                $menu["clear"] = array(
                        "name"=> _("Remove All Content"),
                        "icon" => "remove-all-content",
                        "url" => $baseUrl."schedule/clear-show");
            }

            //"Show Content" should be a menu item at all times except when
            //the show is recorded
            if (!$this->ccShowInstance->isRecorded()) {

                $menu["content"] = array(
                    "name"=> _("Show Content"),
                    "icon" => "overview",
                    "url" => $baseUrl."schedule/show-content-dialog");
            }

            //show is currently playing and user is admin
            if ($start <= $now && $now < $end && $isAdminOrPM) {

                if ($this->ccShowInstance->isRecorded()) {
                    $menu["cancel_recorded"] = array(
                        "name"=> _("Cancel Current Show"),
                        "icon" => "delete");
                } else {
                    $menu["cancel"] = array(
                        "name"=> _("Cancel Current Show"),
                        "icon" => "delete");
                }
            }

            $isRepeating = $this->ccShowInstance->getCcShow()->getFirstCcShowDay()->isRepeating();
            if (!$this->ccShowInstance->isRebroadcast()) {
                if ($isRepeating) {
                    $menu["edit"] = array(
                        "name" => _("Edit"),
                        "icon" => "edit",
                        "items" => array());

                    $menu["edit"]["items"]["all"] = array(
                        "name" => _("Edit Show"),
                        "icon" => "edit",
                        "url" => $baseUrl."Schedule/populate-show-form");

                    $menu["edit"]["items"]["instance"] = array(
                        "name" => _("Edit This Instance"),
                        "icon" => "edit",
                        "url" => $baseUrl."Schedule/populate-repeating-show-instance-form");
                } else {
                    $menu["edit"] = array(
                        "name"=> _("Edit Show"),
                        "icon" => "edit",
                        "_type"=>"all",
                        "url" => $baseUrl."Schedule/populate-show-form");
                }
            }

            //show hasn't started yet and user is admin
            if ($now < $start && $isAdminOrPM) {
                //show is repeating so give user the option to delete all
                //repeating instances or just the one
                if ($isRepeating) {
                    $menu["del"] = array(
                        "name"=> _("Delete"),
                        "icon" => "delete",
                        "items" => array());

                    $menu["del"]["items"]["single"] = array(
                        "name"=> _("Delete This Instance"),
                        "icon" => "delete",
                        "url" => $baseUrl."schedule/delete-show-instance");

                    $menu["del"]["items"]["following"] = array(
                        "name"=> _("Delete This Instance and All Following"),
                        "icon" => "delete",
                        "url" => $baseUrl."schedule/delete-show");
                } else {
                    $menu["del"] = array(
                        "name"=> _("Delete"),
                        "icon" => "delete",
                        "url" => $baseUrl."schedule/delete-show");
                }
            }
        }
        return $menu;
    }

    /*
     * @param $dateTime
     *      php Datetime object to add deltas to
     *
     * @param $deltaDay
     *      php int, delta days show moved
     *
     * @param $deltaMin
     *      php int, delta mins show moved
     *
     * @return $newDateTime
     *      php DateTime, $dateTime with the added time deltas.
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

}