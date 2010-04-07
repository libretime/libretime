<?php
/**
 * @author Sebastian Gobel <sebastian.goebel@web.de>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 * @copyright 2006 MDLF, Inc.
 * @link http://www.campware.org
 */
class uiScheduler extends uiCalendar {
	/**
	 * @var array
	 */
    public $curr;

    /**
     * For the "schedule at time" value.
     *
     * @var array
     */
    public $scheduleAtTime;

    /**
     * For the "snap to previous" value.
     *
     * @var array
     */
    public $schedulePrev;

    /**
     * For the "snap to next" value.
     *
     * @var array
     */
    public $scheduleNext;

    /**
     * @var SchedulerPhpClient
     */
    public $spc;

    /**
     * @var uiBase
     */
    public $Base;

    /**
     * @var string
     */
    public $reloadUrl;

    /**
     * @var string
     */
    public $closeUrl;

    /**
     * Playlists that are available to be scheduled.  These are copied
     * from the scratchpad.
     *
     * @var array
     */
    private $availablePlaylists;

    public $firstDayOfWeek;
	private $scriptError;
    public $error;


    public function __construct(&$uiBase)
    {
        $this->curr =& $_SESSION[UI_CALENDAR_SESSNAME]['current'];
        $this->scheduleAtTime =& $_SESSION[UI_CALENDAR_SESSNAME]['scheduleAtTime'];
        $this->schedulePrev =& $_SESSION[UI_CALENDAR_SESSNAME]['schedulePrev'];
        $this->scheduleNext =& $_SESSION[UI_CALENDAR_SESSNAME]['scheduleNext'];
        #$this->error =& $_SESSION['SCHEDULER']['error'];

        if (!is_array($this->curr)) {
            $this->curr['view'] = UI_SCHEDULER_DEFAULT_VIEW;
            $this->curr['year'] = strftime("%Y");
            $this->curr['month'] = strftime("%m");
            $this->curr['week'] = strftime("%V");
            $this->curr['day'] = strftime("%d");
            $this->curr['hour'] = strftime("%H");
            $this->curr['dayname'] = strftime("%A");
            $this->curr['monthname'] = strftime("%B");
            $this->curr['isToday'] = TRUE;
        }

        $this->Base =& $uiBase;
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->closeUrl = UI_BROWSER.'?popup[]=_close';
        parent::__construct();
        $this->initXmlRpc();
    } // constructor


    public function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    } // fn setReload


    public function setClose()
    {
        $this->Base->redirUrl = $this->closeUrl;
    } // fn setClose


    /**
     * @param array $arr
     * 		Can have keys:
     * 		["view"]
     * 		["today"]
     * 		["year"]
     * 		["month"]
     * 		["day"]
     * 		["hour"]
     * @return void
     */
    function set($arr)
    {
        extract($arr);

        if (isset($view)) {
            $this->curr['view'] = $view;
        }
        if (isset($today)) {
            list($year, $month, $day) = explode("-", strftime("%Y-%m-%d"));
        }
        if (isset($year) && is_numeric($year)) {
            $this->curr['year'] = sprintf('%04d', $year);
        }
        if (isset($month) && is_numeric($month)) {
            $this->curr['month'] = sprintf('%02d', $month);
        }
        if (isset($day) && is_numeric($day)) {
            $this->curr['day'] = sprintf('%02d', $day);
        }
        if (isset($hour) && is_numeric($hour)) {
            $this->curr['hour'] = sprintf('%02d', $hour);
        }

        $stampNow = self::datetimeToTimestamp($this->curr['year']
            .$this->curr['month']
            .$this->curr['day']
            .'T'.$this->curr['hour'].':00:00');
        $stampTarget = $stampNow;

        if (isset($month) && ($month==='++')) {
            $stampTarget = strtotime("+1 month", $stampNow);
        }
        if (isset($month) && ($month==='--')) {
            $stampTarget = strtotime("-1 month", $stampNow);
        }
        if (isset($week) && ($week==='++')) {
            $stampTarget = strtotime("+1 week", $stampNow);
        }
        if (isset($week) && ($week==='--')) {
            $stampTarget = strtotime("-1 week", $stampNow);
        }
        if (isset($day) && ($day==='++')) {
            $stampTarget = strtotime("+1 day", $stampNow);
        }
        if (isset($day) && ($day==='--')) {
            $stampTarget = strtotime("-1 day", $stampNow);
        }

        $this->curr['year'] = strftime("%Y", $stampTarget);
        $this->curr['month'] = strftime("%m", $stampTarget);
        $this->curr['week'] = strftime("%V", $stampTarget);
        $this->curr['day'] = strftime("%d", $stampTarget);
        $this->curr['hour'] = strftime("%H", $stampTarget);
        $this->curr['dayname'] = strftime("%A", $stampTarget);
        $this->curr['monthname'] = strftime("%B", $stampTarget);

        if ($this->curr['year'] === strftime("%Y") && $this->curr['month'] === strftime("%m") && $this->curr['day'] === strftime("%d")) {
            $this->curr['isToday'] = TRUE;
        } else {
            $this->curr['isToday'] = FALSE;
        }
    } // fn set


    /**
     * Set the schedule time given by parameters,
     * calculate previous and next clip to snap with it
     *
     * @param input $arr
     * 		Can contain keys:
     * 		["today"]
     * 		["year"]
     * 		["month"]
     * 		["day"]
     * 		["hour"]
     * 		["minute"]
     * 		["second"]
     *
     * @return void
     */
    public function setScheduleAtTime($arr)
    {
//        $today = $arr['today'];
//        $year = $arr['year'];
//        $month = $arr['month'];
//        $day = $arr['day'];
//        $hour = $arr['hour'];
//        $minute = $arr['minute'];
//        $second = $arr['second'];
        extract($arr);

        if (isset($today)) {
            list($year, $month, $day) = explode("-", strftime("%Y-%m-%d"));
        }
        if (is_numeric($year)) {
            $this->scheduleAtTime['year'] = sprintf('%04d', $year);
        }
        if (is_numeric($month)) {
            $this->scheduleAtTime['month'] = sprintf('%02d', $month);
        }
        if (is_numeric($day)) {
            $this->scheduleAtTime['day'] = sprintf('%02d', $day);
        }
        if (is_numeric($hour)) {
            $this->scheduleAtTime['hour'] = sprintf('%02d', $hour);
        }
        if (is_numeric($minute)) {
            $this->scheduleAtTime['minute'] = sprintf('%02d', $minute);
        }
        if (is_numeric($second)) {
            $this->scheduleAtTime['second'] = sprintf('%02d', $second);
        }

        $this->schedulePrev['year'] = $this->scheduleAtTime['year'];
        $this->schedulePrev['month'] = $this->scheduleAtTime['month'];
        $this->schedulePrev['day'] = $this->scheduleAtTime['day'];
        $this->schedulePrev['hour'] = 0;
        $this->schedulePrev['minute'] = 0;
        $this->schedulePrev['second'] = 0;

        $this->scheduleNext['year'] = $this->scheduleAtTime['year'];
        $this->scheduleNext['month'] = $this->scheduleAtTime['month'];
        $this->scheduleNext['day'] = $this->scheduleAtTime['day'];
        $this->scheduleNext['hour'] = 23;
        $this->scheduleNext['minute'] = 59;
        $this->scheduleNext['second'] = 59;

        $this->scheduleAtTime['stamp'] = self::datetimeToTimestamp(
            $this->scheduleAtTime['year']
            .$this->scheduleAtTime['month']
            .$this->scheduleAtTime['day']
            .'T'.$this->scheduleAtTime['hour']
            .':'.$this->scheduleAtTime['minute']
            .':'.$this->scheduleAtTime['second']);

        $week = $this->getWeekEntrys();
        if (is_array($week)) {
            // Search for previous entry
            if (count($week[$this->scheduleAtTime['day']]) >= 1) {
                $reversedDays = array_reverse($week[$this->scheduleAtTime['day']]);
                foreach ($reversedDays as $hourly) {
                    $reversedHours = array_reverse($hourly);
                    foreach ($reversedHours as $entry) {
                        if ($entry['end_stamp'] <=  $this->scheduleAtTime['stamp']) {
                            list ($this->schedulePrev['hour'], $this->schedulePrev['minute'], $this->schedulePrev['second']) =
                                explode (':', strftime('%H:%M:%S', strtotime('+'.UI_SCHEDULER_PAUSE_PL2PL, strtotime($entry['end'])))
                            );
                            break 2;
                        }
                    }
                }
            }
            reset($week);

            // Search for next entry
            if (count($week[$this->scheduleAtTime['day']]) >= 1) {
                // Go through each hour
                foreach ($week[$this->scheduleAtTime['day']] as $hourly) {
                    // Go through all the entries of the hour
                    foreach ($hourly as $entry) {
                        if ($entry['start_stamp'] >= $this->scheduleAtTime['stamp']) {
                            list ($this->scheduleNext['hour'], $this->scheduleNext['minute'], $this->scheduleNext['second']) =
                                explode (':', strftime('%H:%M:%S', strtotime('-'.UI_SCHEDULER_PAUSE_PL2PL, strtotime($entry['start']))));
                            break 2;
                        }
                    }
                }
            }
        }
    } // fn setScheduleAtTime


    /**
     * Get all items scheduled for the week.
     *
     * @return false|array
     */
    public function getWeekEntrys()
    {
        // build array within all entrys of current week
        $this->buildWeek();
        $thisWeekStart = strftime("%Y%m%d", $this->Week[0]['timestamp']);
        $nextWeekStart = strftime("%Y%m%d", $this->Week[6]['timestamp'] + 86400);
        $arr = $this->displayScheduleMethod($thisWeekStart.'T00:00:00', $nextWeekStart.'T00:00:00');

        if (!is_array($arr)) {
            return FALSE;
        }

        $items = array();
        foreach ($arr as $key => $val) {
        	$id = BasicStor::IdFromGunid($val['playlistId']);
        	$startDay = strftime('%d', self::datetimeToTimestamp($val['start']));
        	$startHour = number_format(strftime('%H', self::datetimeToTimestamp($val['start'])));
            $items[$startDay][$startHour][]= array (
                'id' => $id,
                'scheduleid'=> $val['id'],
                'start' => substr($val['start'], strpos($val['start'], 'T')+1),
                'end' => substr($val['end'], strpos($val['end'], 'T')+1),
                'start_stamp' => self::datetimeToTimestamp($val['start']),
                'end_stamp' => self::datetimeToTimestamp($val['end']),
                'title' => $this->Base->getMetadataValue($id, UI_MDATA_KEY_TITLE),
                'creator' => $this->Base->getMetadataValue($id, UI_MDATA_KEY_CREATOR),
                'type' => 'Playlist'
            );
        }
        return $items;
    } // fn getWeekEntrys


    /**
     * Get all items scheduled for a given day.
     *
     * @return false|array
     */
    public function getDayEntrys()
    {
        // build array within all entrys of current day
        $this->buildDay();
        $thisDay = strftime("%Y%m%d", $this->Day[0]['timestamp']);
        $nextDay = strftime("%Y%m%d", $this->Day[0]['timestamp'] + 86400);
        $arr = $this->displayScheduleMethod($thisDay.'T00:00:00', $nextDay.'T00:00:00');

        if (!is_array($arr)) {
            return FALSE;
        }

        $items = array();
        foreach ($arr as $key => $val) {
        	$start = self::datetimeToTimestamp($val['start']);
            $end = self::datetimeToTimestamp($val['end']);
        	$Y = strftime('%Y', $start);
            $m = number_format(strftime('%m', $start));
            $d = number_format(strftime('%d', $start));
            $h = number_format(strftime('%H', $start));
            $M = number_format(strftime('%i', $start));

            $id = BasicStor::IdFromGunid($val['playlistId']);

            $startHour = (int)strftime('%H', $start);
            $endHour = (int)strftime('%H', $end);
            $startTime = substr($val['start'], strpos($val['start'], 'T')+1);
            $endTime = substr($val['end'], strpos($val['end'], 'T') + 1);
            $title = $this->Base->getMetadataValue($id, UI_MDATA_KEY_TITLE);
            $creator = $this->Base->getMetadataValue($id, UI_MDATA_KEY_CREATOR);

            // Item starts today
            if (strftime('%Y%m%d', $start) === $thisDay) {
                $endsToday = (strftime('%d', $start) === strftime('%d', $end)) ? TRUE : FALSE;
                $endsHere = strftime('%H', $start) === strftime('%H', $end) ? TRUE : FALSE;
            	$items[$startHour]['start'][] =
            	   array('id' => $id,
	                     'scheduleid' => $val['id'],
	                     'start' => $startTime,
	                     'end' => $endTime,
	                     'title' => $title,
	                     'creator' => $creator,
	                     'type' => 'Playlist',
	                     'endstoday' => $endsToday,
                         'endshere' => $endsHere);
            }

            // Item ends today
            if ( (strftime('%Y%m%d', $end) === $thisDay) && ($startHour !== $endHour) ) {
                $startsYesterday = (strftime('%d', $start) === strftime('%d', $end)) ? FALSE : TRUE;

            	$items[$endHour]['end'][] =
            	   array('id' => $id,
	                     'scheduleid' => $val['id'],
	                     'start' => $startTime,
	                     'end' => $endTime,
	                     'title' => $title,
	                     'creator' => $creator,
	                     'type' => 'Playlist',
	                     'startsyesterday' => $startsYesterday);
            }

            $diffHours = floor(($end - $start)/3600);
            // If the item spans three hours or more
            if ( $diffHours > 2 ) {
                // $skip becomes true if we dont actually have to do
                // this section.
                $skip = false;
                if (strftime('%Y%m%d', $start) === $thisDay) {
                    // For the edge case of starting at the end of
                    // today.
                    if ($startHour == 23) {
                        $skip = true;
                    }
                    $countStart = $startHour + 1;
                } else {
                    $countStart = 0;
                }
                if (strftime('%Y%m%d', $end) === $thisDay) {
                    // For the edge case of ending at the beginning
                    // of today.
                    if ($endHour == 0) {
                        $skip = true;
                    }
                    $countEnd = $endHour - 1;
                } else {
                    $countEnd = 23;
                }
                if (!$skip) {
                    for ($i = $countStart; $i <= $countEnd; $i++) {
                    	$items[$i]['span'] =
                    	   array('id' => $id,
        	                     'scheduleid' => $val['id'],
        	                     'start' => $startTime,
        	                     'end' => $endTime,
        	                     'title' => $title,
        	                     'creator' => $creator,
        	                     'type' => 'Playlist');
                    }
                }
            }
        }
        return $items;
    } // fn getDayEntrys

    /*
    function getDayHourlyEntrys($year, $month, $day)
    {
        $date = $year.$month.$day;
        $arr = $this->displayScheduleMethod($date.'T00:00:00', $date.'T23:59:59.999999');
        if (!count($arr))
            return FALSE;
        foreach ($arr as $key => $val) {
            $items[date('H', self::datetimeToTimestamp($val['start']))][]= array (
                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
                'title'     => $this->Base->getMetadataValue(BasicStor::IdFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
                'creator'   => $this->Base->getMetadataValue(BasicStor::IdFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
            );
        }
        #print_r($items);
        return $items;
    }
    */

    private function getDayUsage($year, $month, $day)
    {
        $thisDay = $year.$month.$day;
        $nextDay = strftime("%Y%m%d", strtotime('+1 day', strtotime("$year-$month-$day")));
        $arr = $this->displayScheduleMethod($thisDay.'T00:00:00', $nextDay.'T00:00:00');

        if (!is_array($arr)) {
            return FALSE;
        }

        foreach ($arr as $key => $val) {
        	$id = BasicStor::IdFromGunid($val['playlistId']);
            $arr[$key]['title'] = $this->Base->getMetadataValue($id, UI_MDATA_KEY_TITLE);
            $arr[$key]['creator'] = $this->Base->getMetadataValue($id, UI_MDATA_KEY_CREATOR);
            $arr[$key]['pos'] = self::datetimeToTimestamp($val['start']);
            $arr[$key]['span'] = date('H', self::datetimeToTimestamp($val['end'])) - date('H', self::datetimeToTimestamp($val['start'])) +1;
        }
        return $arr;
    } // fn getDayUsage


    /**
     * Return the percentage of the day for which audio has been scheduled.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    public function getDayUsagePercentage($year, $month, $day)
    {
        if (!$arr = $this->getDayUsage($year, $month, $day)) {
            return false;
        }

        $duration = 0;
        foreach ($arr as $val) {
            $duration += (self::datetimeToTimestamp($val['end'])-self::datetimeToTimestamp($val['start']))/86400*100;
        }
        return $duration;
    } // fn getDayUsagePercentage


    /**
     * Return an array of numbers from 0 to 23.
     *
     * @return array
     */
    public function getDayTimingScale()
    {
        for ($n = 0; $n <= 23; $n++) {
            $scale[] = $n;
        }

        return $scale;
    } //fn getDayTimingScale


    /**
     * @return array
     */
    public function getScheduleForm()
    {
        global $ui_fmask;
        foreach ($this->availablePlaylists as $val) {
            $ui_fmask['schedule']['gunid_duration']['options'][$val['gunid'].'|'.$val['duration']] = $val['title'];
        }

        $form = new HTML_QuickForm('schedule', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $ui_fmask['schedule']);
        $settime = array('H' => $this->scheduleAtTime['hour'],
                         'i' => $this->scheduleAtTime['minute'],
                         's' => $this->scheduleAtTime['second']);
        $setdate = array('Y' => $this->scheduleAtTime['year'],
                         'm' => $this->scheduleAtTime['month'],
                         'd' => $this->scheduleAtTime['day']);
        $form->setDefaults(array('time' => $settime,
                                 'date' => $setdate));

        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output = $renderer->toArray();
        return $output;
    } // fn getScheduleForm


    public function getPlaylistToSchedule($id)
    {
        if ($id) {
            $this->Base->SCRATCHPAD->addItem($id);
            $this->availablePlaylists[] = array(
                'gunid'     => BasicStor::GunidFromId($id),
                'title'     => $this->Base->getMetadataValue($id, UI_MDATA_KEY_TITLE),
                'duration'  => $this->Base->getMetadataValue($id, UI_MDATA_KEY_DURATION),
            );
            return TRUE;
        } else {
            return $this->copyPlaylistFromScratchpad();
        }
    } // fn getPlaylistToSchedule


    /**
     * Get the currently available playlists in the scratchpad and
     * add them to the internal $this->availablePlaylists array.
     *
     * @return boolean
     */
    private function copyPlaylistFromScratchpad()
    {
    	$scratchpad = $this->Base->SCRATCHPAD->get();
        foreach ($scratchpad as $val) {
            if ($val['type'] === 'playlist'
            	&& ($this->Base->gb->playlistIsAvailable($val['id'], $this->Base->sessid) === TRUE)
            	&& ($val['id'] != $this->Base->PLAYLIST->activeId) ) {
                $this->availablePlaylists[] = $val;
            }
        }
        if (!count($this->availablePlaylists)) {
            return FALSE;
        }

        return TRUE;
    } // fn copyPlfromSP


    /**
     * Get an scheduled playlist
     *
     * Note: just use methods here which work without valid authentification.
     *
     * @param int $p_playlist_nr
     *      0=current pl; 1=next pl ...
     * @return array
     */
    public function getScheduledPlaylist($p_playlist_nr=0, $p_period=3600)
    {
        $now = time();
        $start = strftime('%Y%m%dT%H:%M:%S', $now);
        $end =  $p_playlist_nr ? strftime('%Y%m%dT%H:%M:%S', $now + $p_period) : strftime('%Y%m%dT%H:%M:%S', $now); 
        $playlists = $this->displayScheduleMethod($start, $end);

        if (!is_array($playlists) || !count($playlists)) {
            return FALSE;
        }

        switch ($p_playlist_nr) {
            case 0:
                if ($playlist = current($playlists)) {
                    return $playlist;
                }
            break;
            
            default:
                $pos = 0;
                foreach ($playlists as $playlist) {
                    if (self::datetimeToTimestamp($playlist['start']) > $now) {
                        $pos++;
                        if ($pos == $p_playlist_nr) {
                            return $playlist;   
                        }
                    }
                }
            break;            
        }        
        return false;
    }   // fn getScheduledPlaylist
        
        
    public function getClipFromCurrent($p_playlist, $p_item_nr=0)
    {
        //  subtract difference to UTC
        $offset = strftime('%H:%M:%S', time() - self::datetimeToTimestamp($p_playlist['start']) - 3600 * strftime('%H', 0));

        $clip = $this->Base->gb->displayPlaylistClipAtOffset($this->Base->sessid, $p_playlist['playlistId'], $offset, $p_item_nr, $_SESSION['langid'], UI_DEFAULT_LANGID);

        if (!$clip['gunid']) {
            return FALSE;
        }

        $secondsElapsed = Playlist::playlistTimeToSeconds($clip['elapsed']);
        $secondsRemaining =  Playlist::playlistTimeToSeconds($clip['remaining']);
        list($duration['h'], $duration['m'], $duration['s']) = explode(':', Playlist::secondsToPlaylistTime($secondsElapsed + $secondsRemaining));
        list($elapsed['h'], $elapsed['m'], $elapsed['s']) = explode(':', $clip['elapsed']);
        list($remaining['h'], $remaining['m'], $remaining['s']) = explode(':', $clip['remaining']);
        $duration = array_map('round', $duration);
        $elapsed = array_map('round', $elapsed);
        $remaining = array_map('round', $remaining);
        $percentage =  $secondsElapsed ? (100 * $secondsElapsed / ($secondsElapsed + $secondsRemaining)) : 100;
        
        return array('title' => $clip['title'],
                     'duration'  => $duration,
                     'elapsed'   => $elapsed,
                     'remaining' => $remaining,
                     'percentage'=> $percentage,
                     'playlist'  => $clip['playlist']
               );
    }
    
    public function getClipFromPlaylist($p_playlist, $p_position=0)
    {
        $pos = 0;
        $playlist = new uiPlaylist($this->Base);
        $flat = $playlist->getFlat(BasicStor::IdFromGunid($p_playlist['playlistId']));

        foreach ($flat as $clip) {
            if ($pos == $p_position) {
                $found = true;
                break;
            }
            $pos++;
        }
        if ($found) {   
            list($duration['h'], $duration['m'], $duration['s']) = explode(':', $clip['attrs']['clipLength']);
            list($elapsed['h'], $elapsed['m'], $elapsed['s']) = explode(':', '00:00:00');
            $remaining = $duration;
            $duration = array_map('round', $duration);
            $elapsed = array_map('round', $elapsed);
            $remaining = array_map('round', $remaining);
            $percentage =  $secondsElapsed ? (100 * $secondsElapsed / ($secondsElapsed + $secondsRemaining)) : 100;
            return array(
                     'title' => $clip['title'],
                     'duration'  => $duration,
                     'elapsed'   => $elapsed,
                     'remaining' => $remaining,
                     'percentage'=> $percentage
            );
        }
        return false;
    }

    public function getScheduleInfo_jscom($p_playlist_nr=0)
    {
        // just use methods which work without valid authentification

        $c_pl = self::getScheduledPlaylist();
        if ($c_clip = $this->getClipFromCurrent($c_pl, 0)) {
            $n_clip = $this->getClipFromCurrent($c_pl, 1);
        }
        if ($u_pl = self::getScheduledPlaylist(1)) {
            $u_clip = $this->getClipFromPlaylist($u_pl);
            $u_pl_start = explode(':', date('H:i:s', self::datetimeToTimestamp($u_pl['start'])));  
        }
        
        return array(
            'current'               => $c_clip ? 1 : 0,
            'current.title'         => addcslashes($c_clip['title'], "'"),
            'current.pltitle'       => addcslashes($this->Base->getMetadataValue(BasicStor::IdFromGunid($c_pl['playlistId']), UI_MDATA_KEY_TITLE), "'"),
            'current.elapsed.h'     => $c_clip['elapsed']['h'],
            'current.elapsed.m'     => $c_clip['elapsed']['m'],
            'current.elapsed.s'     => $c_clip['elapsed']['s'],
            'current.duration.h'    => $c_clip['duration']['h'],
            'current.duration.m'    => $c_clip['duration']['m'],
            'current.duration.s'    => $c_clip['duration']['s'],
            
            
            'next'                  => $n_clip ? 1 : 0,
            'next.title'            => $n_clip ? addcslashes($n_clip['title'], "'") : "",
            'next.pltitle'          => addcslashes($this->Base->getMetadataValue(BasicStor::IdFromGunid($n_pl['playlistId']), UI_MDATA_KEY_TITLE), "'"),
            'next.duration.h'       => $n_clip ? $n_clip['duration']['h'] : 0,
            'next.duration.m'       => $n_clip ? $n_clip['duration']['m'] : 0,
            'next.duration.s'       => $n_clip ? $n_clip['duration']['s'] : 0,
            
            'upcoming'              => $u_pl ? 1 : 0,
            'upcoming.title'        => addcslashes($u_clip['title'], "'"),
            'upcoming.pltitle'      => addcslashes($this->Base->getMetadataValue(BasicStor::IdFromGunid($u_pl['playlistId']), UI_MDATA_KEY_TITLE), "'"),
            'upcoming.duration.h'   => $u_clip['duration']['h'],
            'upcoming.duration.m'   => $u_clip['duration']['m'],
            'upcoming.duration.s'   => $u_clip['duration']['s'],
            'upcoming.plstart.h'    => $u_pl_start[0],
            'upcoming.plstart.m'    => $u_pl_start[1],
            'upcoming.plstart.s'    => $u_pl_start[2],
            
        );
    } // fn getNowPlaying4jscom


    /**
     * Convert a string timestamp to UNIX time value.
     *
     * @param string $i
     * @return int
     */
    public static function datetimeToTimestamp($i)
    {
        $i = str_replace('T', ' ', $i);
        $formatted = $i[0].$i[1].$i[2].$i[3].'-'.$i[4].$i[5].'-'.$i[6].$i[7].strrchr($i, ' ');
        return self::strtotime($formatted);
    } // fn datetimeToTimestamp


    /**
     * There is a bug in strtotime() - it does not support
     * datetime-format using "T" character correctly, so we need this
     * function.
     *
     * @param string $input
     * @return string
     */
    private static function strtotime($input)
    {
        return strtotime(str_replace('T', ' ', $input));
    }


    protected function _scheduledDays($period)
    {
        if ($period === 'month') {
            require_once('Calendar/Month/Weekdays.php');
            $Period = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], $this->firstDayOfWeek);
            $Period->build();
        } elseif ($period === 'week') {
            require_once('Calendar/Week.php');
            $Period = new Calendar_Week($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->firstDayOfWeek);
            $Period->build();
        } else {
            return array();
        }
        $d = $Period->fetch();
        // The next two lines are due to a bug in Calendar_Month_Weekdays
        $corrMonth = ($d->thisMonth() <= 12) ? sprintf('%02d', $d->thisMonth()) : '01';
        $corrYear = ($d->thisMonth() <= 12) ? $d->thisYear() : $d->thisYear()+1;
        $first = array('day'   => sprintf('%02d', $d->thisDay()),
                       'month' => $corrMonth,
                       'year'  => $corrYear);

        while ($l = $Period->fetch()) {
            $d = $l;
        }
        // The next two lines are due to a bug in Calendar_Month_Weekdays
        $corrMonth = ($d->thisMonth() <= 12) ? sprintf('%02d', $d->thisMonth()) : '01';
        $corrYear = ($d->thisMonth() <= 12) ? $d->thisYear() : $d->thisYear()+1;
        $last = array('day'   => sprintf('%02d', $d->thisDay()),
                      'month' => $corrMonth,
                      'year'  => $corrYear);

        $days = $this->_receiveScheduledDays($first['year'].$first['month'].$first['day'], $last['year'].$last['month'].$last['day']);
        foreach ($days as $val) {
            $selections[] = new Calendar_Day($val['year'], $val['month'], $val['day']);
        }
        return $selections;
    } // fn _scheduledDays


    private function _receiveScheduledDays($dfrom, $dto)
    {
        $dfrom = $dfrom.'T00:00:00';
        $dto = $dto.'T23:59:59';
        if (($pArr = $this->displayScheduleMethod($dfrom, $dto)) === FALSE) {
            return array(FALSE);
        }

        $pStampArr = null;
        foreach ($pArr as $val) {
            $pStampArr[] = array('start' => self::datetimeToTimestamp($val['start']),
                                 'end'   => self::datetimeToTimestamp($val['end']));
        }
        if (is_array($pStampArr)) {
            for ($n = self::datetimeToTimestamp($dfrom); $n <= self::datetimeToTimestamp($dto); $n+=86400) {
                foreach ($pStampArr as $val) {
                    if ($val['start'] < $n+86400 && $val['end'] >= $n) {
                        $days[date('Ymd', $n)] = array('year'  => date('Y', $n),
                                                                       'month' => date('m', $n),
                                                                       'day'   => date('d', $n));
                    }
                }
            }
            return $days;
        }
        return array(FALSE);
    } // fn _receiveScheduledDays


    /**
     * Return true if the argument is an array and has a key index "error"
     * which is an array.  If it is an error, set the internal error
     * message, which can be retrieved with getErrorMsg().
     *
     * @param mixed $r
     * @return boolean
     */
    function _isError($r)
    {
        if (isset($r['error']) && is_array($r['error'])) {
            $this->setErrorMsg(tra('Error: $1', str_replace("\n", "\\n", addslashes($r['error']['message']))));
            return TRUE;
        }
        return FALSE;
    } // fn _isError


    function getErrorMsg()
    {
        return $this->error;
    } // fn getErrorMsg


    /**
     * Set the internal error message.
     *
     * @param string $msg
     * @return void
     */
    public function setErrorMsg($msg)
    {
        $this->error = $msg;
    } // fn setErrorMsg


    /********************************************************************
     * Scheduler Daemon methods
     ********************************************************************/

    /**
     * Return TRUE if the scheduler startup script has been configured.
     * If not, the internal variable $this->scriptError is set with the
     * error message.
     *
     * @return boolean
     */
    private function scriptIsConfigured()
    {
    	$this->scriptError = null;
        if (!isset($this->Base->STATIONPREFS['schedulerStartupScript'])
        	|| empty($this->Base->STATIONPREFS['schedulerStartupScript'])) {
           	$this->scriptError = 'Scheduler startup script has not been defined.  Please set this value in the "Preferences->System Settings".';
           	return FALSE;
        } elseif (!file_exists($this->Base->STATIONPREFS['schedulerStartupScript'])) {
        	$this->scriptError = sprintf('The scheduler startup script you defined does not exist.  You can set this value in "Preferences->System Settings".  The current value is "%s"', $this->Base->STATIONPREFS['schedulerStartupScript']);
        	return FALSE;
        }
        return TRUE;
    }


    public function getScriptError()
    {
    	return $this->scriptError;
    }


    /**
     * Try to start the scheduler daemon.
     *
     * @param boolean $msg
     * @return boolean
     */
    public function startDaemon($msg=FALSE)
    {
    	if (!$this->scriptIsConfigured()) {
			return FALSE;
    	}
        if ($this->daemonIsRunning($msg) === TRUE) {
            return TRUE;
        }

        $cmd = "{$this->Base->STATIONPREFS['schedulerStartupScript']} start 1>/tmp/scheduler_startup.log 2>&1";
        exec($cmd);
        flush();
        sleep(2);

        if ($this->daemonIsRunning($msg)===FALSE) {
            if ($msg) {
		        $output = file('/tmp/scheduler_startup.log');
		        $message = '';
		        foreach ($output as $line) {
		            $message .= trim(addslashes($line)).'\n';
		        }
            	$this->Base->_retMsg('Scheduler did not start. Returned message:\n$1', $message);
            }
            return FALSE;
        }
    } // fn startDaemon


    /**
     * Try to stop the scheduler daemon.
     *
     * @param boolean $msg
     * @return boolean
     */
    public function stopDaemon($msg=FALSE)
    {
    	if (!$this->scriptIsConfigured()) {
			return FALSE;
    	}
        if ($this->daemonIsRunning($msg) === FALSE) {
            return TRUE;
        }

        $cmd = "{$this->Base->STATIONPREFS['schedulerStartupScript']} stop 1>/tmp/scheduler_startup.log 2>&1";
        exec($cmd);
        flush();
        sleep(2);

        if ($this->daemonIsRunning($msg)===TRUE) {
            if ($msg) {
		        $output = file('/tmp/scheduler_startup.log');
		        foreach ($output as $line) {
		            $message .= trim(addslashes($line)).'\n';
		        }
                $this->Base->_retMsg('Scheduler did not stop. Returned message:\n$1', $message);
            }
            return FALSE;
        }
    } // fn stopDaemon


    /**
     * Return TRUE if the scheduler daemon is running.
     *
     * @param boolean $msg
     * @return boolean
     */
    public function daemonIsRunning($msg = FALSE)
    {
    	if (!$this->scriptIsConfigured()) {
			return FALSE;
    	}
        $cmd = "{$this->Base->STATIONPREFS['schedulerStartupScript']} status";
        exec($cmd, $output);

        if (empty($output)) {
           	$this->scriptError = 'Scheduler startup script does not appear to be valid.  Please check the value you have set in "Preferences->System Settings".';
           	return FALSE;
        }
        $message = "";
        foreach ($output as $line) {
            $message .= trim(addslashes($line)).'\n';
        }

        if (strstr($message, 'is running')) {
            if ($msg) {
                $this->Base->_retMsg('Scheduler is running.');
            }
            return TRUE;
        }

        return FALSE;
    } // fn daemonIsRunning


    /********************************************************************
     * XML-RPC wrapper methods
     ********************************************************************/

    /**
     * Create the XMLRPC client.
     */
    function initXmlRpc()
    {
        include_once(dirname(__FILE__).'/ui_schedulerPhpClient.class.php');
        $this->spc =& SchedulerPhpClient::factory($mdefs, FALSE, FALSE);
    } // fn initXmlRpc


    /**
     * Upload a playlist to the scheduler.
     *
     * @param array $formdata
     *      Must have the following keys set:
     *      ['playlist'] -> gunid of playlist
     *      ['date']['Y'] - Year
     *      ['date']['m'] - month
     *      ['date']['d'] - day
     *      ['date']['H'] - hour
     *      ['date']['i'] - minute
     *      ['date']['s'] - second
     *
     * @return boolean
     *      TRUE on success, FALSE on failure.
     */
    function uploadPlaylistMethod(&$formdata)
    {
        $gunid = $formdata['playlist'];
        $datetime = $formdata['date']['Y']
            .sprintf('%02d', $formdata['date']['m'])
            .sprintf('%02d', $formdata['date']['d'])
            .'T'.sprintf('%02d', $formdata['time']['H'])
            .':'.sprintf('%02d', $formdata['time']['i'])
            .':'.sprintf('%02d', $formdata['time']['s']);

        $r = $this->spc->UploadPlaylistMethod($this->Base->sessid, $gunid, $datetime);
        if ($this->_isError($r)) {
            return FALSE;
        }
        return TRUE;
    } // fn uploadPlaylistMethod


    /**
     * Remove a playlist from the scheduler.
     *
     * @param string $id
     *      gunid of the playlist
     * @return boolean
     *      TRUE on success, FALSE on failure.
     */
    function removeFromScheduleMethod($id)
    {
        $r = $this->spc->removeFromScheduleMethod($this->Base->sessid, $id);
        if ($this->_isError($r)) {
            return FALSE;
        }
        if (UI_VERBOSE) {
            $this->Base->_retMsg('Entry with ScheduleId $1 removed.', $id);
        }
        return TRUE;
    } // fn removeFromScheduleMethod


    /**
     * Get the scheduled items between the $from and $to dates.
     *
     * @param string $from
     *      In the format YYYMMDDTHH:MM:SS
     * @param string $to
     *      In the format YYYMMDDTHH:MM:SS
     * @return array|false
     */
    function displayScheduleMethod($from, $to)
    {
        $r = $this->spc->displayScheduleMethod($this->Base->sessid, $from, $to);
        if ($this->_isError($r)) {
            return FALSE;
        }
        return $r;
    } // fn displayScheduleMethod


    /********************************************************************
     * Export Methods
     ********************************************************************/

    /**
     * Get the token for the schedule which is currently being exported.
     * It is stored in the user preferences.
     *
     * @return string
     */
    function getExportToken()
    {
        $token = $this->Base->gb->loadPref($this->Base->sessid, UI_SCHEDULER_EXPORTTOKEN_KEY);

        if (PEAR::isError($token) || empty($token)) {
            return false;
        }
        return $token;
    } // fn getExportToken


    /**
     * Export a schedule within a certain time range.
     *
     * @param string $from
     *      Date-time format
     * @param string $to
     *      Date-time format
     * @return boolean
     */
    function scheduleExportOpen($from, $to)
    {
        $criteria = array('filetype' => UI_FILETYPE_ANY);
        $token = $this->spc->exportOpenMethod($this->Base->sessid, $criteria, $from, $to);

        if (PEAR::isError($token)) {
            $this->Base->_retMsg('Error initializing scheduler export: $1', $token->getMessage());
            return false;
        }

        if (isset($token["error"])) {
            $this->Base->_retMsg('Error initializing scheduler export: $1',
                                 $token["error"]["code"].":".$token["error"]["message"]);
            return false;
        }
        $this->Base->gb->savePref($this->Base->sessid, UI_SCHEDULER_EXPORTTOKEN_KEY, $token['token']);
        //$this->scheduleExportCheck();
        return true;
    } // fn scheduleExportOpen


    /**
     * Check the status of a schedule that is being exported.
     *
     * @return string|false
     */
    function scheduleExportCheck()
    {
        $token = $this->getExportToken();

        if (empty($token)) {
            $this->Base->_retMsg('Token not available');
            return false;
        }

        $res = $this->spc->exportCheckMethod($token);
        if (PEAR::isError($res)) {
            $this->Base->_retMsg('Unable to check scheduler export status: $1', $res->getMessage());
            return false;
        }
        return $res;
    } // fn scheduleExportCheck


    function scheduleExportClose()
    {
        $token = $this->getExportToken();

        if (empty($token)) {
            $this->Base->_retMsg('Token not available');
            return false;
        }

        $status = $this->spc->exportCloseMethod($token);

        if (PEAR::isError($status)) {
            $this->Base->_retMsg('Error closing scheduler export: $1', $status->getMessage());
            return false;
        }

        if ($status === true) {
            $this->Base->gb->delPref($this->Base->sessid, UI_SCHEDULER_EXPORTTOKEN_KEY);
        }

        return $status;
    } // fn scheduleExportClose


    /********************************************************************
     * Import Methods
     ********************************************************************/


    function getImportToken()
    {
        $token = $this->Base->gb->loadPref($this->Base->sessid, UI_SCHEDULER_IMPORTTOKEN_KEY);

        if (PEAR::isError($token)) {
            return false;
        }
        return $token;
    } // fn getImportToken


    function scheduleImportOpen($filename)
    {
        $token = $this->spc->importOpenMethod($this->Base->sessid, $filename);

        if (PEAR::isError($token)) {
            $this->Base->_retMsg('Error initializing scheduler import: $1', $token->getMessage());
            return false;
        }

        $this->scheduleImportCheck();

        $this->Base->gb->savePref($this->Base->sessid, UI_SCHEDULER_IMPORTTOKEN_KEY, $token['token']);

        return true;
    } // fn scheduleImportOpen


    function scheduleImportCheck()
    {
        $token = $this->getImportToken();

        if ($token === false) {
            return false;
        }

        $res = $this->spc->importCheckMethod($token);
        //echo '<XMP style="background:yellow;">'; var_dump($res); echo "</XMP>\n";
        if (PEAR::isError($res)) {
            $this->Base->_retMsg('Unable to check scheduler import status: $1', $res->getMessage());
            return false;
        }

        return $res;
    } // fn scheduleImportCheck


    function scheduleImportClose()
    {
        $token = $this->getImportToken();

        if ($token === false) {
            $this->Base->_retMsg('Token not available');
            return false;
        }

        $status = $this->spc->importCloseMethod($token);

        if (PEAR::isError($status)) {
            $this->Base->_retMsg('Error closing scheduler import: $1', $status->getMessage());
            return false;
        }

        if ($status === true) {
            $this->Base->gb->delPref($this->Base->sessid, UI_SCHEDULER_IMPORTTOKEN_KEY);
        }

        return $status;
    } // fn scheduleImportClose
    
    public function getSchedulerTime()
    {
        static $first, $cached;
        if (!$first) {
            $first = time();
            $r = $this->spc->GetSchedulerTimeMethod();
            if ($this->_isError($r)) {
                return false;    
            }
            $cached = self::datetimeToTimestamp($r['schedulerTime']);
        }
        $schedulerTime = $cached + time() - $first;
        return $schedulerTime;
    }

} // class uiScheduler
?>
