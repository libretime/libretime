<?php
class uiScheduler extends uiCalendar
{
    function uiScheduler(&$uiBase)
    {
        $this->curr   =& $_SESSION[UI_CALENDAR_SESSNAME]['current'];
        if (!is_array($this->curr)) {
            $this->curr['view']     = 'month';
            $this->curr['year']     = date("Y");
            $this->curr['month']    = date("m");
            $this->curr['day']      = date('d');
            $this->curr['hour']     = date('H');
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
        if ($view)  $this->curr['view'] = $view;
        if ($year)  $this->curr['year'] = $year;
        if ($day)   $this->curr['day']  = $this->Base->_twoDigits($day);
        if ($hour)  $this->curr['hour'] = $this->Base->_twoDigits($hour);
        if (is_numeric($month))
                    $this->curr['month'] = $this->Base->_twoDigits($month);

        $stampNow    = $this->_datetime2timestamp($this->curr['year'].$this->curr['month'].$this->curr['day']);
        $stampTarget = $stampNow;

        if ($month=='++')
            $stampTarget = strtotime("+1 month", $stampNow);
        if ($month=='--')
            $stampTarget = strtotime("-1 month", $stampNow);

        if ($week=='++')
            $stampTarget = strtotime("+1 week", $stampNow);
        if ($week=='--')
            $stampTarget = strtotime("-1 week", $stampNow);

        $this->curr['year']     = strftime("%Y", $stampTarget);
        $this->curr['month']    = strftime("%m", $stampTarget);
        $this->curr['day']      = strftime("%d", $stampTarget);
    }


    function _datetime2timestamp($i)
    {
        $formatted = $i[0].$i[1].$i[2].$i[3].'-'.$i[4].$i[5].'-'.$i[6].$i[7].strrchr($i, 'T');
        #echo "iiiii: $i ffff:".$formatted;
        return strtotime($formatted);
    }


    function getDayUsage($year, $month, $day)
    {
        $date = $year.$month.$day;
        $arr = $this->displayScheduleMethod($date.'T00:00:00', $date.'T23:59:59.999999');
        if (!count($arr))
            return FALSE;
        #print_r($arr);
        return $arr;
    }

    function getDayUsagePercentage($year, $month, $day)
        {
        #echo "date: ".$year.$month.$day."<br>";
        if (isset($this->_duration[$year.$month.$day]))
            return $this->_duration[$year.$month.$day];

        $this->_duration[$year.$month.$day] = 0;
        if (!$arr = $this->getDayUsage($year, $month, $day))
            return false;

        foreach ($arr as $val) {
            #print_r($val);
            $this->_duration[$year.$month.$day] += ($this->_datetime2timestamp($val['end'])-$this->_datetime2timestamp($val['start']))/86400*100;
        }
        #echo "duration: ".$this->_duration[$year.$month.$day]."<br>";
        return $this->_duration[$year.$month.$day];
    }


    function _scheduledDays($period)
    {
        if ($period=='month') {
            require_once 'Calendar/Month/Weekdays.php';
            $Period = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], $this->firstDayOfWeek);
            $Period->build();
        }
        if ($period=='week') {
            require_once 'Calendar/Week.php';
            $Period = new Calendar_Week ($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->firstDayOfWeek);
            $Period->build();
        }
        $d = $Period->fetch();
        $corrMonth = $d->thisMonth()<=12 ? $this->Base->_twoDigits($d->thisMonth()) : '01';   ## due to bug in
        $corrYear  = $d->thisMonth()<=12 ? $d->thisYear() : $d->thisYear()+1;                  ## Calendar_Month_Weekdays
        $first = array('day'   => $this->Base->_twoDigits($d->thisDay()),
                       'month' => $corrMonth,
                       'year'  => $corrYear
                 );

        while ($l = $Period->fetch()) {
            $d = $l;
        }
        $corrMonth = $d->thisMonth()<=12 ? $this->Base->_twoDigits($d->thisMonth()) : '01';   ## due to bug in
        $corrYear  = $d->thisMonth()<=12 ? $d->thisYear() : $d->thisYear()+1;                  ## Calendar_Month_Weekdays
        $last = array('day'   => $this->Base->_twoDigits($d->thisDay()),
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
        $pArr  = $this->displayScheduleMethod($dfrom, $dto);
        #print_r($pArr);
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


    function copyPlFromSP()
    {
        foreach ($this->Base->SCRATCHPAD->get() as $val) {
            if (strtolower($val['type'])=='playlist' && $val['id']!=$this->Base->PLAYLIST->activeId)
                $this->playlists[] = $val;
        }
    }

    ## XML-RPC methods ############################################################################################
    function initXmlRpc()
    {
        include_once dirname(__FILE__).'/SchedulerPhpClient.class.php';
        $this->spc =& SchedulerPhpClient::factory($this->Base->dbc, $mdefs, $this->Base->config);
    }


    function uploadPlaylistMethod(&$formdata)
    {
        $gunid = $formdata['gunid'];
        $datetime = $this->curr['year'].$this->curr['month'].$this->curr['day'].'T'.$formdata['time'];
        #echo "Schedule Gunid: $gunid  At: ".$datetime;
        $r = $this->spc->UploadPlaylistMethod($this->Base->sessid, $gunid, $datetime.UI_TIMEZONE);
        #print_r($r);
        if (is_array($r['error']))
            $this->Base->_retMsg('Error: $1', $r['error']['message']);
        if (isset($r['scheduleEntryId']))
            $this->Base->_retMsg('ScheduleId: $1', $r['scheduleEntryId']);
    }


    function displayScheduleMethod($from, $to)
    {
        #echo $from.$to;
        $r = $this->spc->displayScheduleMethod($this->Base->sessid, $from, $to);
        return $r;
    }
}
?>
