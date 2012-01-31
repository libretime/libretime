<?php

class Application_Model_ShowBuilder {

    private $timezone;
    private $startDT;
    private $endDT;
    private $user;

    private $defaultRowArray = array(
        "header" => false,
        "footer" => false,
        "empty" => false,
        "checkbox" => false,
        "id" => "",
        "instance" => "",
        "starts" => "",
        "startsUnix" => null,
        "ends" => "",
        "endsUnix" => null,
        "runtime" => "",
        "title" => "",
        "creator" => "",
        "album" => ""
    );

    /*
     * @param DateTime $p_startsDT
     * @param DateTime $p_endsDT
     */
    public function __construct($p_startDT, $p_endDT) {

        $this->startDT = $p_startDT;
        $this->endDT = $p_endDT;
        $this->timezone = date_default_timezone_get();
        $this->user = Application_Model_User::GetCurrentUser();
    }

    /*
     * @param DateInterval $p_interval
     *
     * @return string $runtime
     */
    private function formatDuration($p_interval){

        $hours = $p_interval->format("%h");
        $mins = $p_interval->format("%i");

        if( $hours == 0) {
            $runtime = $p_interval->format("%i:%S");
        }
        else {
            $runtime = $p_interval->format("%h:%I:%S");
        }

        return $runtime;
    }

    private function makeFooterRow() {

        $row = $this->defaultRowArray;
        $row["footer"] = true;

        return $row;
    }

    private function makeHeaderRow($p_item) {

        $row = $this->defaultRowArray;

        $showStartDT = new DateTime($p_item["si_starts"], new DateTimeZone("UTC"));
        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $showEndDT = new DateTime($p_item["si_ends"], new DateTimeZone("UTC"));
        $showEndDT->setTimezone(new DateTimeZone($this->timezone));

        //$diff =

        $row["header"] = true;
        $row["starts"] = $showStartDT->format("Y-m-d H:i");
        $row["startsUnix"] = $showStartDT->format("U");
        $row["ends"] = $showEndDT->format("Y-m-d H:i");
        $row["endsUnix"] = $showEndDT->format("U");
        $row["duration"] = $showEndDT->format("U") - $showStartDT->format("U");
        $row["title"] = $p_item["show_name"];

        return $row;
    }

    private function makeScheduledItemRow($p_item) {
        $row = $this->defaultRowArray;

        if (isset($p_item["sched_starts"])) {

            $schedStartDT = new DateTime($p_item["sched_starts"], new DateTimeZone("UTC"));
            $schedStartDT->setTimezone(new DateTimeZone($this->timezone));
            $schedEndDT = new DateTime($p_item["sched_ends"], new DateTimeZone("UTC"));
            $schedEndDT->setTimezone(new DateTimeZone($this->timezone));

            $runtime = $schedStartDT->diff($schedEndDT);

            $row["id"] = intval($p_item["sched_id"]);
            $row["instance"] = intval($p_item["si_id"]);
            $row["starts"] = $schedStartDT->format("H:i:s");
            $row["startsUnix"] = $schedStartDT->format("U");
            $row["ends"] = $schedEndDT->format("H:i:s");
            $row["endsUnix"] = $schedEndDT->format("U");
            $row["duration"] = $schedEndDT->format("U") - $schedStartDT->format("U");
            $row["runtime"] = $this->formatDuration($runtime);
            $row["title"] = $p_item["file_track_title"];
            $row["creator"] = $p_item["file_artist_name"];
            $row["album"] = $p_item["file_album_title"];

            if ($this->user->canSchedule($item["show_id"]) === true) {
                $row["checkbox"] = true;
            }
        }
        //show is empty
        else {
            $row["empty"] = true;
        }

        return $row;
    }

    public function GetItems() {

        $current_id = -1;
        $display_items = array();

        $scheduled_items = Application_Model_Schedule::GetScheduleDetailItems($this->startDT->format("Y-m-d H:i:s"), $this->endDT->format("Y-m-d H:i:s"));

        foreach ($scheduled_items as $item) {

            //make a header row.
            if ($current_id !== $item["si_id"]) {

                //make a footer row.
                if ($current_id !== -1) {
                    $display_items[] = $this->makeFooterRow();
                }

                $display_items[] = $this->makeHeaderRow($item);

                $current_id = $item["si_id"];
            }

            //make a normal data row.
            $display_items[] = $this->makeScheduledItemRow($item);
        }

        //make the last footer if there were any scheduled items.
        if (count($scheduled_items) > 0) {
            $display_items[] = $this->makeFooterRow();
        }

        return $display_items;
    }
}