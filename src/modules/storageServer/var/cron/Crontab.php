<?php
define('CRON_COMMENT', 0);
define('CRON_ASSIGN',  1);
define('CRON_CMD',     2);
define('CRON_SPECIAL', 3);
define('CRON_EMPTY',   4);

/**
 * A class that interfaces with the crontab. (cjpa@audiophile.com)
 *
 * This class lets you manipulate a user's crontab.
 * It lets you add delete update entries easily.
 *
 * @author $Author: $
 * @version $Revision: $
 * @package Campcaster
 * @subpackage StorageServer.Cron
 */
class Crontab
{
    // {{{ properties
    /**
     * Holds all the different lines.
     *     Lines are associative arrays with the following fields:
     *       "minute"     : holds the minutes (0-59)
     *       "hour"       : holds the hour (0-23)
     *       "dayofmonth" : holds the day of the month (1-31)
     *       "month"      : the month (1-12 or the names)
     *       "dayofweek"  : 0-7 (or the names)
     *
     *   or a line can be a 2-value array that represents an assignment:
     *           "name" => "value"
     *   or a line can be a comment (string beginning with #)
     *   or it can be a special command (beginning with an @)
     * @var array
     */
    private $crontabs;

    /**
     * The user for whom the crontab will be manipulated
     * @var string
     */
    private $user;

    /**
     * Lists the type of line of each line in $crontabs.
     *   can be: any of the CRON_* constants.
     *   so $linetype[5] is the type of $crontabs[5].
     * @var string
     */
    private $linetypes;

    // }}}

    /**
     * Constructor
     *
     * Initialises $this->crontabs
     *
     * @param string $user the user for whom the crontab will be manipulated
     */
    function Crontab($user)
    {
        $this->user = $user;
        $this->readCrontab();
    }

    /**
     * This reads the crontab of $this->user and parses it in $this->crontabs
     *
     */
    function readCrontab()
    {
        // return code is 0 or 1 if crontab was empty, elsewhere stop here
        $cmd = "crontab -u {$this->user} -l";
        @exec("crontab -u {$this->user} -l", $crons, $return);
        if ($return > 1) {
            return PEAR::raiseError("*** Can't read crontab ***\n".
                "    Set crontab manually!\n");
        }

        foreach ($crons as $line)
        {
            $line = trim($line); // discarding all prepending spaces and tabs

            // empty lines..
            if (!$line) {
                $this->crontabs[] = "empty line";
                $this->linetypes[] = CRON_EMPTY;
                continue;
            }

            // checking if this is a comment
            if ($line[0] == "#") {
                $this->crontabs[] = trim($line);
                $this->linetypes[] = CRON_COMMENT;
                continue;
            }

            // Checking if this is an assignment
            if (ereg("(.*)=(.*)", $line, $assign)) {
                $this->crontabs[] = array ("name" => $assign[1], "value" => $assign[2]);
                $this->linetypes[] = CRON_ASSIGN;
                continue;
            }

            // Checking if this is a special @-entry. check man 5 crontab for more info
            if ($line[0] == '@') {
                $this->crontabs[] = split("[ \t]", $line, 2);
                $this->linetypes[] = CRON_SPECIAL;
                continue;
            }

            // It's a regular crontab-entry
            $ct = split("[ \t]", $line, 6);
            $this->addCron($ct[0], $ct[1], $ct[2], $ct[3], $ct[4], $ct[5], $ct[6]);
        }
    }

    /**
     * Writes the current crontab
     */
    function writeCrontab()
    {
        global $DEBUG, $PATH;

        $filename = ($DEBUG ? tempnam("$PATH/crons", "cron") : tempnam("/tmp", "cron"));
        $file = fopen($filename, "w");

        foreach($this->linetypes as $i => $line) {
            switch ($this->linetypes[$i]) {
                case CRON_COMMENT:
                    $line = $this->crontabs[$i];
                    break;
                case CRON_ASSIGN:
                    $line = $this->crontabs[$i][name]." = ".$this->crontabs[$i][value];
                    break;
                case CRON_CMD:
                    $line = implode(" ", $this->crontabs[$i]);
                    break;
                case CRON_SPECIAL:
                    $line = implode(" ", $this->crontabs[$i]);
                    break;
                case CRON_EMPTY:
                    $line = "\n"; // an empty line in the crontab-file
                    break;
                default:
                    unset($line);
                    echo "Something very weird is going on. This line ($i) has an unknown type.\n";
                    break;
            }

            // echo "line $i : $line\n";

            if ($line) {
                $r = @fwrite($file, $line."\n");
                if($r === FALSE) {
                    return PEAR::raiseError("*** Can't write crontab ***\n".
                        "    Set crontab manually!\n");
                }
            }
        }
        fclose($file);

        if ($DEBUG) {
            echo "DEBUGMODE: not updating crontab. writing to $filename instead.\n";
        } else {
            exec("crontab -u {$this->user} $filename", $returnar, $return);
            if ($return != 0) {
                echo "Error running crontab ($return). $filename not deleted\n";
            } else {
                unlink($filename);
            }
        }
    }


    /**
     * Add a item of type CRON_CMD to the end of $this->crontabs
     *
     * @param string $m
     *      minute
     * @param string $h
     *      hour
     * @param string $dom
     *      day of month
     * @param string $mo
     *      month
     * @param string $dow
     *      day of week
     * @param string $cmd
     *      command
     *
     */
    function addCron($m, $h, $dom, $mo, $dow, $cmd)
    {
        $this->crontabs[] = array ("minute" => $m, "hour" => $h, "dayofmonth" => $dom, "month" => $mo, "dayofweek" => $dow, "command" => $cmd);
        $this->linetypes[] = CRON_CMD;
    }


    /**
     * Add a comment to the cron to the end of $this->crontabs
     *
     * @param string $comment
     */
    function addComment($comment)
    {
        $this->crontabs[] = "# $comment\n";
        $this->linetypes[] = CRON_COMMENT;
    }


    /**
     * Add a special command (check man 5 crontab for more information)
     *
     * @param string $sdate special date
     *         string         meaning
     *         ------         -------
     *         @reboot        Run once, at startup.
     *         @yearly        Run once a year, "0 0 1 1 *".
     *         @annually      (same as @yearly)
     *         @monthly       Run once a month, "0 0 1 * *".
     *         @weekly        Run once a week, "0 0 * * 0".
     *         @daily         Run once a day, "0 0 * * *".
     *         @midnight      (same as @daily)
     *         @hourly        Run once an hour, "0 * * * *".
     * @param string $cmd command
     */
    function addSpecial($sdate, $cmd)
    {
        $this->crontabs[] = array ("special" => $sdate, "command" => $cmd);
        $this->linetypes[] = CRON_SPECIAL;
    }


    /**
     * Add an assignment (name = value)
     *
     * @param string $name
     * @param string $value
     */
    function addAssign($name, $value)
    {
        $this->crontabs[] = array ("name" => $name, "value" => $value);
        $this->linetypes[] = CRON_ASSIGN;
    }


    /**
     * Delete a line from the arrays.
     *
     * @param int $index the index in $this->crontabs
     */
    function delEntry($index)
    {
        unset ($this->crontabs[$index]);
        unset ($this->linetypes[$index]);
    }


    /**
     * Get all the lines of a certain type in an array
     *
     * @param string $type
     */
    function getByType($type)
    {
        if ($type < CRON_COMMENT || $type > CRON_EMPTY)
        {
            trigger_error("Wrong type: $type", E_USER_WARNING);
            return 0;
        }

        $returnar = array ();
        for ($i = 0; $i < count($this->linetypes); $i ++)
            if ($this->linetypes[$i] == $type)
                $returnar[] = $this->crontabs[$i];

        return $returnar;
    }
}
?>