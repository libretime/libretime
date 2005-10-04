<?php
class uiScheduler extends uiCalendar
{
    function uiScheduler(&$uiBase)
    {
        $this->curr           =& $_SESSION[UI_CALENDAR_SESSNAME]['current'];
        $this->scheduleAtTime =& $_SESSION[UI_CALENDAR_SESSNAME]['scheduleAtTime'];
        $this->schedulePrev   =& $_SESSION[UI_CALENDAR_SESSNAME]['schedulePrev'];
        $this->scheduleNext   =& $_SESSION[UI_CALENDAR_SESSNAME]['scheduleNext'];

        if (!is_array($this->curr)) {
            $this->curr['view']      = UI_SCHEDULER_DEFAULT_VIEW;
            $this->curr['year']      = strftime("%Y");
            $this->curr['month']     = strftime("%m");
            $this->curr['week']      = strftime("%V");
            $this->curr['day']       = strftime("%d");
            $this->curr['hour']      = strftime("%H");
            $this->curr['dayname']   = strftime("%A");
            $this->curr['monthname'] = strftime("%B");
            $this->curr['isToday']   = TRUE;
        }

        $this->Base =& $uiBase;
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->closeUrl  = UI_BROWSER.'?popup[]=_close';

        $this->uiCalendar();
        $this->initXmlRpc();

        //$this->startDaemon();
    }


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    function setClose()
    {
        $this->Base->redirUrl = $this->closeUrl;
    }


    function startDaemon($msg=FALSE)
    {
        if ($this->testDaemon($msg) === TRUE)
            return TRUE;

        exec(UI_SCHEDULER_DAEMON_CMD);
        sleep(5);

        if ($this->testDaemon($msg)===FALSE) {
            if ($msg) $this->Base->_retMsg('Scheduler did not start. Check setting of "UI_SCHEDULER_DAEMON_CMD" in ui_conf.php. File "/tmp/scheduler.log" could be helpful.');
            return FALSE;
        }
    }

    function testDaemon($msg=FALSE)
    {
        exec('ps -A', $output);
        foreach ($output as $l) {
            if (preg_match("/ ".UI_SCHEDULER_DAEMON_NAME."$/", $l)) {
                if ($msg) $this->Base->_retMsg('Scheduler is running.');
                return TRUE;
            }
        }

        return FALSE;
    }


    function set($arr)
    {
        extract($arr);

        if (isset($view))       $this->curr['view']         = $view;
        if (isset($today))      list($year, $month, $day)   = explode("-", strftime("%Y-%m-%d"));
        if (is_numeric($year))  $this->curr['year']         = sprintf('%04d', $year);
        if (is_numeric($month)) $this->curr['month']        = sprintf('%02d', $month);
        if (is_numeric($day))   $this->curr['day']          = sprintf('%02d', $day);
        if (is_numeric($hour))  $this->curr['hour']         = sprintf('%02d', $hour);

        $stampNow    = $this->_datetime2timestamp($this->curr['year'].$this->curr['month'].$this->curr['day'].'T'.$this->curr['hour'].':00:00');
        $stampTarget = $stampNow;

        if ($month==='++')      $stampTarget = strtotime("+1 month", $stampNow);
        if ($month==='--')      $stampTarget = strtotime("-1 month", $stampNow);
        if ($week==='++')       $stampTarget = strtotime("+1 week", $stampNow);
        if ($week==='--')       $stampTarget = strtotime("-1 week", $stampNow);
        if ($day==='++')        $stampTarget = strtotime("+1 day", $stampNow);
        if ($day==='--')        $stampTarget = strtotime("-1 day", $stampNow);

        $this->curr['year']      = strftime("%Y", $stampTarget);
        $this->curr['month']     = strftime("%m", $stampTarget);
        $this->curr['week']      = strftime("%V", $stampTarget);
        $this->curr['day']       = strftime("%d", $stampTarget);
        $this->curr['hour']      = strftime("%H", $stampTarget);
        $this->curr['dayname']   = strftime("%A", $stampTarget);
        $this->curr['monthname'] = strftime("%B", $stampTarget);

        if ($this->curr['year'] === strftime("%Y") && $this->curr['month'] === strftime("%m") && $this->curr['day'] === strftime("%d"))
            $this->curr['isToday'] = TRUE;
        else
            $this->curr['isToday'] = FALSE;
        #print_r($this->curr);
    }


    /**
     *  setScheduleAtTime
     *
     *  set the schedule time given by parameters,
     *  calculate previous and next clip to snap with it
     *
     *  @param input array, contains date and time
     *  @return void
     */

    function setScheduleAtTime($arr)
    {
        extract($arr);

        $this->schedulePrev['hour']     = 0;
        $this->schedulePrev['minute']   = 0;
        $this->schedulePrev['second']   = 0;
        #$thisDay = $this->scheduleAtTime['year']."-".$this->scheduleAtTime['month']."-".$this->scheduleAtTime['day'];
        #$nextDayStamp = strtotime('+1 day', strtotime($thisDay));
        #$this->scheduleNext['year']     = strftime('%Y', $nextDayStamp);
        #$this->scheduleNext['month']    = strftime('%m', $nextDayStamp);;
        #$this->scheduleNext['day']      = strftime('%d', $nextDayStamp);
        $this->scheduleNext['hour']     = 23;
        $this->scheduleNext['minute']   = 59;
        $this->scheduleNext['second']   = 59;

        if (isset($today))              list($year, $month, $day)        = explode("-", strftime("%Y-%m-%d"));
        if (is_numeric($year))          $this->scheduleAtTime['year']    = sprintf('%04d', $year);
        if (is_numeric($month))         $this->scheduleAtTime['month']   = sprintf('%02d', $month);
        if (is_numeric($day))           $this->scheduleAtTime['day']     = sprintf('%02d', $day);
        if (is_numeric($hour))          $this->scheduleAtTime['hour']    = sprintf('%02d', $hour);
        if (is_numeric($minute))        $this->scheduleAtTime['minute']  = sprintf('%02d', $minute);
        if (is_numeric($second))        $this->scheduleAtTime['second']  = sprintf('%02d', $second);

        $this->scheduleAtTime['stamp'] = $this->_datetime2timestamp($this->scheduleAtTime['year'].$this->scheduleAtTime['month'].$this->scheduleAtTime['day'].'T'.
                                                                    $this->scheduleAtTime['hour'].':'.$this->scheduleAtTime['minute'].':'.$this->scheduleAtTime['second']);

        if (is_array($week = $this->getWeekEntrys())) {

            ## search for previous entry
            if (count($week[$this->scheduleAtTime['day']]) >= 1) {
                foreach (array_reverse($week[$this->scheduleAtTime['day']]) as $hourly) {
                    foreach (array_reverse($hourly) as $entry) {
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

            ## search for next entry
            if (count($week[$this->scheduleAtTime['day']]) >= 1) {
                foreach ($week[$this->scheduleAtTime['day']] as $hourly) {
                    foreach ($hourly as $entry) {
                        if ($entry['start_stamp'] >=  $this->scheduleAtTime['stamp']) {
                            list ($this->scheduleNext['hour'], $this->scheduleNext['minute'], $this->scheduleNext['second']) =
                                explode (':', strftime('%H:%M:%S', strtotime('-'.UI_SCHEDULER_PAUSE_PL2PL, strtotime($entry['start']))));
                            break 2;
                        }
                    }
                }
            }
        }

        #print_r($this->schedulePrev);
        #print_r($this->scheduleNext);

    }


    function getWeekEntrys()
    {
        ## build array within all entrys of current week ##
        $this->buildWeek();
        $thisWeekStart = strftime("%Y%m%d", $this->Week[0]['timestamp']);
        $nextWeekStart = strftime("%Y%m%d", $this->Week[6]['timestamp'] + 86400);
        $arr = $this->displayScheduleMethod($thisWeekStart.'T00:00:00', $nextWeekStart.'T00:00:00');
        #print_r($arr);

        if (!count($arr))
            return FALSE;

        foreach ($arr as $key => $val) {
            $items[strftime('%d', $this->_datetime2timestamp($val['start']))][number_format(strftime('%H', $this->_datetime2timestamp($val['start'])))][]= array (
                'id'        => $this->Base->gb->_idFromGunid($val['playlistId']),
                'scheduleid'=> $val['id'],
                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
                'end'       => substr($val['end'],   strpos($val['end'], 'T')+1),
                'start_stamp' => $this->_datetime2timestamp($val['start']),
                'end_stamp' => $this->_datetime2timestamp($val['end']),
                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
                'type'      => 'Playlist'
            );
        }

        #print_r($items);
        return $items;
    }


    function getDayEntrys()
    {
        ## build array within all entrys of current day ##
        $this->buildDay();
        $thisDay = strftime("%Y%m%d", $this->Day[0]['timestamp']);
        $nextDay = strftime("%Y%m%d", $this->Day[0]['timestamp'] + 86400);
        $arr = $this->displayScheduleMethod($thisDay.'T00:00:00', $nextDay.'T00:00:00');
        #print_r($arr);

        if (!count($arr))
            return FALSE;

        foreach ($arr as $key => $val) {
        	$start = $this->_datetime2timestamp($val['start']);
            $end   = $this->_datetime2timestamp($val['end']);
        	$Y = strftime('%Y', $start);
            $m = number_format(strftime('%m', $start));
            $d = number_format(strftime('%d', $start));
            $h = number_format(strftime('%H', $start));
            $M = number_format(strftime('%i', $start));

            ## item starts today 
            if (strftime('%Y%m%d', $start) === $thisDay) {
            	$items[number_format(strftime('%H', $start))]['start'][] = array(
	                'id'        => $this->Base->gb->_idFromGunid($val['playlistId']),
	                'scheduleid'=> $val['id'],
	                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
	                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
	                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
	                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
	                'type'      => 'Playlist',
	                'endstoday' => strftime('%d', $start) === strftime('%d', $end) ? TRUE : FALSE,
                    'endshere'	=> strftime('%H', $start) === strftime('%H', $end) ? TRUE : FALSE
	            );
            }
            
            /* mark the span as in use
            for ($n = number_format(strftime('%H', $start))+1; $n <= number_format(strftime('%H', $end)); $n++) {
            	$items['span'][$n] = TRUE;
            }  
            */
            
            ## item ends today
            if (strftime('%Y%m%d', $end) === $thisDay && strftime('%H', $start) !== strftime('%H', $end)) {
            	$items[number_format(strftime('%H', $end))]['end'][] =
            	array(
	                'id'        => $this->Base->gb->_idFromGunid($val['playlistId']),
	                'scheduleid'=> $val['id'],
	                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
	                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
	                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
	                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
	                'type'      => 'Playlist',
	                'startsyesterday' => strftime('%d', $start) === strftime('%d', $end) ? FALSE : TRUE,
	            );
            }
        }

        #print_r($items);
        return $items;
    }

    /*
    function getDayHourlyEntrys($year, $month, $day)
    {
        $date = $year.$month.$day;
        $arr = $this->displayScheduleMethod($date.'T00:00:00', $date.'T23:59:59.999999');
        if (!count($arr))
            return FALSE;
        foreach ($arr as $key => $val) {
            $items[date('H', $this->_datetime2timestamp($val['start']))][]= array (
                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
            );
        }
        #print_r($items);
        return $items;
    }
    */

    function getDayUsage($year, $month, $day)
    {
        $thisDay = $year.$month.$day;
        $nextDay = strftime("%Y%m%d", strtotime('+1 day', strtotime("$year-$month-$day")));
        $arr = $this->displayScheduleMethod($thisDay.'T00:00:00', $nextDay.'T00:00:00');
        if (!count($arr))
            return FALSE;
        foreach ($arr as $key=>$val) {
            $arr[$key]['title']     = $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE);
            $arr[$key]['creator']   = $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR);
            $arr[$key]['pos']       = $this->_datetime2timestamp($val['start']);
            $arr[$key]['span']      = date('H', $this->_datetime2timestamp($val['end'])) - date('H', $this->_datetime2timestamp($val['start'])) +1;
        }
        #print_r($arr);
        return $arr;
    }

    function getDayUsagePercentage($year, $month, $day)
        {
        if (!$arr = $this->getDayUsage($year, $month, $day))
            return false;

        foreach ($arr as $val) {
            $duration += ($this->_datetime2timestamp($val['end'])-$this->_datetime2timestamp($val['start']))/86400*100;
        }
        return $duration;
    }


    function getDayTimingScale()
    {
        for ($n = 0; $n <= 23; $n++) {
            $scale[] = $n;
        }

        return $scale;

    }


    function getScheduleForm()
    {
        global $ui_fmask;
        #print_r($this->availablePlaylists);
        foreach ($this->availablePlaylists as $val) {
            $ui_fmask['schedule']['gunid_duration']['options'][$val['gunid'].'|'.$val['duration']] = $val['title'];
        }
        #print_r($ui_fmask['schedule']);

        $form = new HTML_QuickForm('schedule', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $ui_fmask['schedule']);
        $settime = array('H' => $this->scheduleAtTime['hour'],
                         'i' => $this->scheduleAtTime['minute'],
                         's' => $this->scheduleAtTime['second']
                   );
        $setdate = array('Y' => $this->scheduleAtTime['year'],
                         'm' => $this->scheduleAtTime['month'],
                         'd' => $this->scheduleAtTime['day']
                   );
        $form->setDefaults(array('time'         => $settime,
                                 'date'         => $setdate,
        ));

        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output = $renderer->toArray();
        #print_r($output);
        return $output;

    }

    function getPLtoSchedule($id)
    {
        if ($id) {
            $this->Base->SCRATCHPAD->addItem($id);
            $this->availablePlaylists[] = array(
                'gunid'     => $this->Base->gb->_gunidFromId($id),
                'title'     => $this->Base->_getMDataValue($id, UI_MDATA_KEY_TITLE),
                'duration'  => $this->Base->_getMDataValue($id, UI_MDATA_KEY_DURATION),
            );
            return TRUE;
        } else {
            return $this->copyPLfromSP();
        }

    }

    function copyPlfromSP()
    {
        foreach ($this->Base->SCRATCHPAD->get() as $val) {
            if ($val['type'] === 'playlist' && $this->Base->gb->playlistIsAvailable($val['id'], $this->Base->sessid) === TRUE && $val['id'] != $this->Base->PLAYLIST->activeId)
                $this->availablePlaylists[] = $val;
        }
        if (!count($this->availablePlaylists))
            return FALSE;

        return TRUE;
    }


    function getNowNextClip($distance=0)
    {
        ## just use methods which work without valid authentification

        $datetime    = strftime('%Y-%m-%dT%H:%M:%S');
        $xmldatetime = str_replace('-', '', $datetime);
        $pl = $this->displayScheduleMethod($xmldatetime, $xmldatetime);

        if (!is_array($pl) || !count($pl)) return FALSE;

        $pl = current($pl);
        $offset = strftime('%H:%M:%S', time() - $this->_datetime2timestamp($pl['start']) - 3600 * strftime('%H', 0));   ##  subtract difference to UTC

        $clip = $this->Base->gb->displayPlaylistClipAtOffset($this->Base->sessid, $pl['playlistId'], $offset, $distance, $_SESSION['langid'], UI_DEFAULT_LANGID);

        if (!$clip['gunid']) return FALSE;

        list($duration['h'],  $duration['m'],  $duration['s'])  = explode(':', $this->Base->gb->_secsToPlTime($this->Base->gb->_plTimeToSecs($clip['elapsed']) + $this->Base->gb->_plTimeToSecs($clip['remaining'])));
        list($elapsed['h'],   $elapsed['m'],   $elapsed['s'])   = explode(':', $clip['elapsed']);
        list($remaining['h'], $remaining['m'], $remaining['s']) = explode(':', $clip['remaining']);
        $duration  = array_map('round', $duration);
        $elapsed   = array_map('round', $elapsed);
        $remaining = array_map('round', $remaining);

        return array(
                'title'     => $clip['title'],
                'duration'  => $duration,
                'elapsed'   => $elapsed,
                'remaining' => $remaining,
                'percentage'=> $this->Base->gb->_plTimeToSecs($clip['elapsed'])
                                    ? 100 * $this->Base->gb->_plTimeToSecs($clip['elapsed']) / ( $this->Base->gb->_plTimeToSecs($clip['elapsed']) + $this->Base->gb->_plTimeToSecs($clip['remaining']))
                                    : 100
               );
    }

    function getNowNextClip4jscom()
    {
        ## just use methods which work without valid authentification

        if ($curr = $this->getNowNextClip()) {
            $next = $this->getNowNextClip(1);
            return array(
                    'title'         => $curr['title'],
                    'elapsed.h'     => $curr['elapsed']['h'],
                    'elapsed.m'     => $curr['elapsed']['m'],
                    'elapsed.s'     => $curr['elapsed']['s'],
                    'duration.h'    => $curr['duration']['h'],
                    'duration.m'    => $curr['duration']['m'],
                    'duration.s'    => $curr['duration']['s'],
                    'next'          => $next ? 1 : 0,
                    'next.title'    => $next ? $next['title'] : "",
                    'next.dur.h'    => $next ? $next['duration']['h'] : 0,
                    'next.dur.m'    => $next ? $next['duration']['m'] : 0,
                    'next.dur.s'    => $next ? $next['duration']['s'] : 0,
                   );
        } else {
            return FALSE;
        }
    }

    function _datetime2timestamp($i)
    {
        $i = str_replace('T', ' ', $i);
        $formatted = $i[0].$i[1].$i[2].$i[3].'-'.$i[4].$i[5].'-'.$i[6].$i[7].strrchr($i, ' ');
        #echo "input: $i formatted:".$formatted;
        return $this->_strtotime($formatted);
    }


    function _strtotime($input)
    {
        ## !! bug in strtotime, does not rightly support datetime-format using T chatracter
        return strtotime(str_replace('T', ' ', $input));
    }

    function _oneOrMore($in)
    {
        return $id < 1 ? ceil($in) : round($in);
    }


    function _scheduledDays($period)
    {
        if ($period==='month') {
            require_once 'Calendar/Month/Weekdays.php';
            $Period = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], $this->firstDayOfWeek);
            $Period->build();
        } elseif ($period==='week') {
            require_once 'Calendar/Week.php';
            $Period = new Calendar_Week ($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->firstDayOfWeek);
            $Period->build();
        } else {
            return array();
        }
        $d = $Period->fetch();
        $corrMonth = $d->thisMonth()<=12 ? sprintf('%02d', $d->thisMonth()) : '01';   ## due to bug in
        $corrYear  = $d->thisMonth()<=12 ? $d->thisYear() : $d->thisYear()+1;         ## Calendar_Month_Weekdays
        $first = array('day'   => sprintf('%02d', $d->thisDay()),
                       'month' => $corrMonth,
                       'year'  => $corrYear
                 );

        while ($l = $Period->fetch()) {
            $d = $l;
        }
        $corrMonth = $d->thisMonth()<=12 ? sprintf('%02d', $d->thisMonth()) : '01';   ## due to bug in
        $corrYear  = $d->thisMonth()<=12 ? $d->thisYear() : $d->thisYear()+1;         ## Calendar_Month_Weekdays
        $last = array('day'   => sprintf('%02d', $d->thisDay()),
                      'month' => $corrMonth,
                      'year'  => $corrYear
                );


        #echo "F:".$first['year'].$first['month'].$first['day']." L:".$last['year'].$last['month'].$last['day'];
        $days = $this->_reciveScheduledDays($first['year'].$first['month'].$first['day'], $last['year'].$last['month'].$last['day']);
        foreach ($days as $val) {
            $selections[] = new Calendar_Day($val['year'], $val['month'], $val['day']);
        }
        return $selections;
    }


    function _reciveScheduledDays($dfrom, $dto)
    {
        $dfrom = $dfrom.'T00:00:00';
        $dto   = $dto.'T23:59:59';
        if (($pArr  = $this->displayScheduleMethod($dfrom, $dto)) === FALSE)
            return array(FALSE);;

        foreach ($pArr as $val) {
            #print_r($val);
            $pStampArr[] = array('start' => $this->_datetime2timestamp($val['start']),
                                 'end'   => $this->_datetime2timestamp($val['end']));
        }
        if (is_array($pStampArr)) {
            #print_r($pStampArr);
            for ($n=$this->_datetime2timestamp($dfrom); $n<=$this->_datetime2timestamp($dto); $n+=86400) {
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
    }


    function _isError($r)
    {
        if (is_array($r['error'])) {
            $this->Base->_retMsg('Error: $1', str_replace("\n", "\\n", addslashes($r['error']['message'])));
            return TRUE;
        }
        return FALSE;
    }

    ## XML-RPC wrapper methods ############################################################################################
    function initXmlRpc()
    {
        include_once dirname(__FILE__).'/ui_schedulerPhpClient.class.php';
        $this->spc =& SchedulerPhpClient::factory($this->Base->dbc, $mdefs, $this->Base->config, FALSE, FALSE);
    }


    function uploadPlaylistMethod(&$formdata)
    {
        #$gunid = $formdata['gunid'];
        #$datetime = $this->curr['year'].$this->curr['month'].$this->curr['day'].'T'.$formdata['time'];

        $gunid = $formdata['playlist'];
        $datetime = $formdata['date']['Y'].sprintf('%02d', $formdata['date']['m']).sprintf('%02d', $formdata['date']['d']).'T'.sprintf('%02d', $formdata['time']['H']).':'.sprintf('%02d', $formdata['time']['i']).':'.sprintf('%02d', $formdata['time']['s']);

        #echo "Schedule Gunid: $gunid  At: ".$datetime;
        $r = $this->spc->UploadPlaylistMethod($this->Base->sessid, $gunid, $datetime);
        #print_r($r);
        if ($this->_isError($r))
            return FALSE;
        if (isset($r['scheduleEntryId']))
            $this->Base->_retMsg('Entry added at $1 with ScheduleId: $2', strftime("%Y-%m-%d %H:%M:%S", $this->_datetime2timestamp($datetime)), $r['scheduleEntryId']);
    }


    function removeFromScheduleMethod($id)
    {
        #echo "Unschedule Gunid: $gunid";
        $r = $this->spc->removeFromScheduleMethod($this->Base->sessid, $id);
        #print_r($r);
        if ($this->_isError($r))
            return FALSE;
        if (UI_VERBOSE) $this->Base->_retMsg('Entry with ScheduleId $1 removed', $id);
    }


    function displayScheduleMethod($from, $to)
    {
        #echo $from.$to;
        $r = $this->spc->displayScheduleMethod($this->Base->sessid, $from, $to);
        if ($this->_isError($r))
            return FALSE;
        return $r;
    }
}
?>
