<?php

class Application_Model_ShowBuilder {

    private $timezone;
    private $startDT;
    private $endDT;

    private $defaultRowArray = array(
        "header" => false,
        "footer" => false,
        "empty" => false,
        "instance" => "",
        "starts" => "",
        "ends" => "",
        "title" => ""
    );

    /*
     * @param DateTime $p_startsDT
     * @param DateTime $p_endsDT
     */
    public function __construct($p_startDT, $p_endDT) {

        $this->startDT = $p_startDT;
        $this->endDT = $p_endDT;
        $this->timezone = date_default_timezone_get();
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

        $row["header"] = true;
        $row["starts"] = $showStartDT->format("Y-m-d H:i");
        $row["ends"] = $showEndDT->format("Y-m-d H:i");
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

            $row["instance"] = $p_item["si_id"];
            $row["starts"] = $schedStartDT->format("Y-m-d H:i:s");
            $row["ends"] = $schedEndDT->format("Y-m-d H:i:s");
            $row["title"] = $p_item["file_track_title"];
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