<?php
class uiScheduler extends uiCalendar
{
    function uiScheduler(&$uiBase)
    {
        $this->curr   =& $_SESSION[UI_CALENDAR_SESSNAME]['current'];
        if (!is_array($this->curr)) {
            $this->curr['view']     = 'month';
            $this->curr['year']     = strftime("%Y");
            $this->curr['month']    = strftime("%m");
            $this->curr['week']     = strftime("%V");
            $this->curr['day']      = strftime("%d");
            $this->curr['hour']     = strftime("%H");
            $this->curr['dayname']  = strftime("%A");
            $this->curr['monthname']= strftime("%B");
            $this->curr['isToday']  = TRUE;
        }

        $this->Base =& $uiBase;
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        $this->uiCalendar();
        $this->initXmlRpc();
    }


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function set($arr)
    {
        extract($arr);
        #print_r($arr);
        if (isset($view))  $this->curr['view'] = $view;

        if (is_numeric($year))  $this->curr['year']  = $year;
        if (is_numeric($month)) $this->curr['month'] = sprintf('%02d', $month);
        if (is_numeric($day))   $this->curr['day']   = sprintf('%02d', $day);
        if (is_numeric($hour))  $this->curr['hour']  = sprintf('%02d', $hour);

        $stampNow    = $this->_datetime2timestamp($this->curr['year'].$this->curr['month'].$this->curr['day'].'T'.$this->curr['hour'].':00:00');
        $stampTarget = $stampNow;

        if ($month=='++')
            $stampTarget = strtotime("+1 month", $stampNow);
        if ($month=='--')
            $stampTarget = strtotime("-1 month", $stampNow);
        if ($week=='++')
            $stampTarget = strtotime("+1 week", $stampNow);
        if ($week=='--')
            $stampTarget = strtotime("-1 week", $stampNow);
        if ($day=='++')
            $stampTarget = strtotime("+1 day", $stampNow);
        if ($day=='--')
            $stampTarget = strtotime("-1 day", $stampNow);

        if ($today)
            $stampTarget = time();

        $this->curr['year']     = strftime("%Y", $stampTarget);
        $this->curr['month']    = strftime("%m", $stampTarget);
        $this->curr['week']     = strftime("%V", $stampTarget);
        $this->curr['day']      = strftime("%d", $stampTarget);
        $this->curr['hour']     = strftime("%H", $stampTarget);
        $this->curr['dayname']  = strftime("%A", $stampTarget);
        $this->curr['monthname']= strftime("%B", $stampTarget);

        if ($this->curr['year'] == strftime("%Y") && $this->curr['month'] == strftime("%m") && $this->curr['day'] == strftime("%d"))
            $this->curr['isToday'] = TRUE;
        else
            $this->curr['isToday'] = FALSE;
        #print_r($this->curr);
    }


    function getWeekEntrys()
    {
        ## build array within all entrys of current week ##
        $this->buildWeek();
        $weekStart = strftime("%Y%m%d", $this->Week[0]['timestamp']);
        $weekEnd   = strftime("%Y%m%d", $this->Week[6]['timestamp']);
        $arr = $this->displayScheduleMethod($weekStart.'T00:00:00', $weekEnd.'T23:59:59.999999');
        #print_r($arr);

        if (!count($arr))
            return FALSE;

        foreach ($arr as $key => $val) {
            $items[strftime('%d', $this->_datetime2timestamp($val['start']))][number_format(strftime('%H', $this->_datetime2timestamp($val['start'])))][]= array (
                'scheduleid'=> $val['id'],
                'plid'      => $this->Base->gb->_idFromGunid($val['playlistId']),
                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
            );
        }

        #print_r($items);
        return $items;
    }


    function getDayEntrys()
    {
        ## build array within all entrys of current day ##
        $this->buildDay();
        $day = strftime("%Y%m%d", $this->Day[0]['timestamp']);
        $arr = $this->displayScheduleMethod($day.'T00:00:00', $day.'T23:59:59.999999');
        #print_r($arr);

        if (!count($arr))
            return FALSE;

        foreach ($arr as $key => $val) {
            $items[number_format(strftime('%H', $this->_datetime2timestamp($val['start'])))][]= array (
                'plid'      => $this->Base->gb->_idFromGunid($val['playlistId']),
                'scheduleid'=> $val['id'],
                'start'     => substr($val['start'], strpos($val['start'], 'T')+1),
                'end'       => substr($val['end'],   strpos($val['end'], 'T') + 1),
                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_TITLE),
                'creator'   => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($val['playlistId']), UI_MDATA_KEY_CREATOR),
            );
        }

        #print_r($items);
        return $items;
    }

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

    function getDayUsage($year, $month, $day)
    {
        $date = $year.$month.$day;
        $arr = $this->displayScheduleMethod($date.'T00:00:00', $date.'T23:59:59.999999');
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


    function getDayTiming($year, $month, $day)
    {
        #echo $year.$month.$day;
        $day_start = $this->_datetime2timestamp($year.$month.$day.'T00:00:00');
        $day_end   = $this->_datetime2timestamp($year.$month.$day.'T23:59:59');

        if (!$arr = $this->getDayUsage($year, $month, $day))
            return array(array(                                         ## empty day
                        'type'      => 'gap',
                        'length'    => $day_end - $day_start
                   ));

        $curr = current($arr);
        if ($this->_strtotime($curr['start']) > $day_start)             ## insert gap if first entry start after 00:00:00
            $list[] = array(
                        'type'      => 'gap',
                        #'pos'       => 0,
                        'length'    => $this->_strtotime($curr['start']) - $day_start
                      );

        while ($curr = current($arr)) {
            $list[] = array(
                        'type'      => 'entry',
                        #'pos'       => $this->_strtotime($curr['start']) - $day_start,
                        'length'    => $this->_strtotime($curr['end']) - $this->_strtotime($curr['start']),
                        'entry'     => $curr
                      );

            if ($next = next($arr)) {
                if ($this->_strtotime($next['start']) > $this->_strtotime($curr['end'])+1)  ## insert gap between entrys
                    $list[] = array(
                                'type'      => 'gap',
                                #'pos'       => $this->_strtotime($curr['start'])-$day_start,
                                'length'    => $this->_strtotime($next['start']) - $this->_strtotime($curr['end']),
                              );
            }
            else {
                if ($this->_strtotime($curr['end']) < $day_end)        ## insert gap if prev entry was not until midnight
                    $list[] = array(
                                'type'      => 'gap',
                                #'pos'       => $this->_strtotime($curr['end']) - $day_start,
                                'length'    => $day_end - $this->_strtotime($curr['end']),
                              );
            }

        }
        #print_r($list);
        return $list;
    }


    function getDayTimingScale()
    {
        for ($n = 0; $n <= 23; $n++) {
            $scale[] = $n;
        }
        #print_r($scale);
        return $scale;

    }


    function getScheduleForm()
    {
        global $ui_fmask;
        foreach ($this->playlists as $val)
            $ui_fmask['schedule']['playlist']['options'][$val['gunid']] = $val['title'];
        #print_r($ui_fmask);
        $form = new HTML_QuickForm('schedule', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $ui_fmask['schedule']);
        $settime = array('H' => $this->curr['hour']);
        $setdate = array('Y' => $this->curr['year'],
                         'm' => $this->curr['month'],
                         'd' => $this->curr['day']);
        $form->setDefaults(array('time'     => $settime,
                                 'date'     => $setdate,
                                 'playlist' => $setplaylist));
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output = $renderer->toArray();
        #print_r($output);
        return $output;

    }


    function getNowNextClip($distance=0)
    {
        $datetime    = strftime('%Y-%m-%dT%H:%M:%S');
        $xmldatetime = str_replace('-', '', $datetime);
        $pl = $this->displayScheduleMethod($xmldatetime, $xmldatetime);
        if(!is_array($pl) || !count($pl))
            return FALSE;

        $pl = current($pl);
        $offset = strftime('%H:%M:%S', $this->_strtotime($datetime) - $this->_datetime2timestamp($pl['start']) - UI_TIMEZONEOFFSET);
        $clip = $this->Base->gb->displayPlaylistClipAtOffset($this->Base->sessid, $pl['playlistId'], $offset, $distance);
        if(!$clip['gunid'])
            return FALSE;
        return array(
                'title'     => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($clip['gunid']), UI_MDATA_KEY_TITLE),
                'duration'  => $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($clip['gunid']), UI_MDATA_KEY_DURATION),
                'elapsed'   => $clip['elapsed'],
                'remaining' => $clip['remaining'],
                'percentage'=> 100 * $this->Base->gb->_plTimeToSecs($clip['elapsed']) / ( $this->Base->gb->_plTimeToSecs($clip['elapsed']) + $this->Base->gb->_plTimeToSecs($clip['remaining']))
               );
    }


    function _copyPlFromSP()
    {
        foreach ($this->Base->SCRATCHPAD->get() as $val) {
            if (strtolower($val['type'])=='playlist' && $val['id']!=$this->Base->PLAYLIST->activeId)
                $this->playlists[] = $val;
        }
        if (!count($this->playlists))
            return FALSE;
        return TRUE;
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
        if ($period=='month') {
            require_once 'Calendar/Month/Weekdays.php';
            $Period = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], $this->firstDayOfWeek);
            $Period->build();
        } elseif ($period=='week') {
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
        include_once dirname(__FILE__).'/SchedulerPhpClient.class.php';
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
            $this->Base->_retMsg('Entry added at $1 with ScheduleId: $2', $datetime, $r['scheduleEntryId']);
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
