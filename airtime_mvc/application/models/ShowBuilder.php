<?php

require_once 'formatters/LengthFormatter.php';

class Application_Model_ShowBuilder {

    private $timezone;
    private $startDT;
    private $endDT;
    private $user;
    private $opts;

    private $contentDT;
    private $epoch_now;

    private $defaultRowArray = array(
        "header" => false,
        "footer" => false,
        "empty" => false,
        "allowed" => false,
        "id" => 0,
        "instance" => "",
        "starts" => "",
        "ends" => "",
        "runtime" => "",
        "title" => "",
        "creator" => "",
        "album" => "",
        "timestamp" => null,
        "cuein" => "",
        "cueout" => "",
        "fadein" => "",
        "fadeout" => ""
    );

    /*
     * @param DateTime $p_startsDT
     * @param DateTime $p_endsDT
     */
    public function __construct($p_startDT, $p_endDT, $p_opts) {

        $this->startDT = $p_startDT;
        $this->endDT = $p_endDT;
        $this->timezone = date_default_timezone_get();
        $this->user = Application_Model_User::GetCurrentUser();
        $this->opts = $p_opts;
        $this->epoch_now = time();
    }

    private function formatTimeFilled($p_sec) {

        $formatted = "";
        $sign = ($p_sec < 0) ? "-" : "+";

        $time = Application_Model_Playlist::secondsToPlaylistTime(abs($p_sec));
        Logging::log("time is: ".$time);
        $info = explode(":", $time);

        $formatted .= $sign;

        if (intval($info[0]) > 0) {
            $info[0] = ltrim($info[0], "0");
            $formatted .= " {$info[0]}h";
        }

        if (intval($info[1]) > 0) {
            $info[1] = ltrim($info[1], "0");
            $formatted .= " {$info[1]}m";
        }

        if (intval($info[2]) > 0) {
            $sec = round($info[2], 0);
            $formatted .= " {$sec}s";
        }

        return $formatted;
    }

    private function isAllowed($p_item, &$row) {

        $showStartDT = new DateTime($p_item["si_starts"], new DateTimeZone("UTC"));

        //can only schedule the show if it hasn't started and you are allowed.
        if ($this->epoch_now < $showStartDT->format('U') && $this->user->canSchedule($p_item["show_id"]) == true) {
            $row["allowed"] = true;
        }
    }

    private function getItemStatus($p_item, &$row) {

        $showEndDT = new DateTime($p_item["si_ends"]);
        $schedStartDT = new DateTime($p_item["sched_starts"]);
        $schedEndDT = new DateTime($p_item["sched_ends"]);

        $showEndEpoch = intval($showEndDT->format("U"));
        $schedStartEpoch = intval($schedStartDT->format("U"));
        $schedEndEpoch = intval($schedEndDT->format("U"));

        if ($schedEndEpoch < $showEndEpoch) {
            $status = 0; //item will playout in full
        }
        else if ($schedStartEpoch < $showEndEpoch && $schedEndEpoch > $showEndEpoch) {
            $status = 1; //item is on boundry
        }
        else {
            $status = 2; //item is overscheduled won't play.
        }

        $row["status"] = $status;
    }

    private function getRowTimestamp($p_item, &$row) {

        if (is_null($p_item["si_last_scheduled"])) {
            $ts = 0;
        }
        else {
            $dt = new DateTime($p_item["si_last_scheduled"], new DateTimeZone("UTC"));
            $ts = intval($dt->format("U"));
        }
        $row["timestamp"] = $ts;
    }

    private function makeHeaderRow($p_item) {

        $row = $this->defaultRowArray;
        $this->isAllowed($p_item, $row);
        Logging::log("making header for show id ".$p_item["show_id"]);
        $this->getRowTimestamp($p_item, $row);

        $showStartDT = new DateTime($p_item["si_starts"], new DateTimeZone("UTC"));
        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $showEndDT = new DateTime($p_item["si_ends"], new DateTimeZone("UTC"));
        $showEndDT->setTimezone(new DateTimeZone($this->timezone));

        $row["header"] = true;
        $row["starts"] = $showStartDT->format("Y-m-d H:i");
        $row["ends"] = $showEndDT->format("Y-m-d H:i");
        $row["duration"] = $showEndDT->format("U") - $showStartDT->format("U");
        $row["title"] = $p_item["show_name"];
        $row["instance"] = intval($p_item["si_id"]);

        $this->contentDT = $showStartDT;

        return $row;
    }

    private function makeScheduledItemRow($p_item) {
        $row = $this->defaultRowArray;

        $this->isAllowed($p_item, $row);
        $this->getRowTimestamp($p_item, $row);

        if (isset($p_item["sched_starts"])) {

            $schedStartDT = new DateTime($p_item["sched_starts"], new DateTimeZone("UTC"));
            $schedStartDT->setTimezone(new DateTimeZone($this->timezone));
            $schedEndDT = new DateTime($p_item["sched_ends"], new DateTimeZone("UTC"));
            $schedEndDT->setTimezone(new DateTimeZone($this->timezone));

            $this->getItemStatus($p_item, $row);

            $row["id"] = intval($p_item["sched_id"]);
            $row["instance"] = intval($p_item["si_id"]);
            $row["starts"] = $schedStartDT->format("H:i:s");
            $row["ends"] = $schedEndDT->format("H:i:s");

            $formatter = new LengthFormatter($p_item['file_length']);
            $row['runtime'] = $formatter->format();

            $row["title"] = $p_item["file_track_title"];
            $row["creator"] = $p_item["file_artist_name"];
            $row["album"] = $p_item["file_album_title"];

            $row["cuein"] = $p_item["cue_in"];
            $row["cueout"] = $p_item["cue_out"];
            $row["fadein"] = round(substr($p_item["fade_in"], 6), 6);
            $row["fadeout"] = round(substr($p_item["fade_out"], 6), 6);

            $this->contentDT = $schedEndDT;
        }
        //show is empty
        else {

            $row["empty"] = true;
            $row["id"] = 0 ;
            $row["instance"] = intval($p_item["si_id"]);
        }

        return $row;
    }

    private function makeFooterRow($p_item) {

        $row = $this->defaultRowArray;
        $this->isAllowed($p_item, $row);
        $row["footer"] = true;

        $showEndDT = new DateTime($p_item["si_ends"], new DateTimeZone("UTC"));
        $contentDT = $this->contentDT;

        $runtime = bcsub($contentDT->format("U.u"), $showEndDT->format("U.u"), 6);
        $row["runtime"] = $runtime;
        $row["fRuntime"] = $this->formatTimeFilled($runtime);

        return $row;
    }

    public function GetItems() {

        $current_id = -1;
        $display_items = array();

        $shows = array();
        if ($this->opts["myShows"] === 1) {

            $host_shows = CcShowHostsQuery::create()
                ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
                ->filterByDbHost($this->user->getId())
                ->find();

            foreach ($host_shows as $host_show) {
                $shows[] = $host_show->getDbShow();
            }
        }
        else if ($this->opts["showFilter"] !== 0) {
            $shows[] = $this->opts["showFilter"];
        }

        $scheduled_items = Application_Model_Schedule::GetScheduleDetailItems($this->startDT->format("Y-m-d H:i:s"), $this->endDT->format("Y-m-d H:i:s"), $shows);

        for ($i = 0, $rows = count($scheduled_items); $i < $rows; $i++) {

            $item = $scheduled_items[$i];

            //make a header row.
            if ($current_id !== $item["si_id"]) {

                //make a footer row.
                if ($current_id !== -1) {
                    //pass in the previous row as it's the last row for the previous show.
                    $display_items[] = $this->makeFooterRow($scheduled_items[$i-1]);
                }

                $display_items[] = $this->makeHeaderRow($item);

                $current_id = $item["si_id"];
            }

            //make a normal data row.
            $row = $this->makeScheduledItemRow($item);
            //don't display the empty rows.
            if (isset($row)) {
                $display_items[] = $row;
            }
        }

        //make the last footer if there were any scheduled items.
        if (count($scheduled_items) > 0) {
            $display_items[] = $this->makeFooterRow($scheduled_items[count($scheduled_items)-1]);
        }

        return $display_items;
    }
}