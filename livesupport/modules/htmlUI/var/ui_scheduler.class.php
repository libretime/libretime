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
        if ($month) $this->curr['month']= $month;
        if ($day)   $this->curr['day']  = $day;
        if ($hour)  $this->curr['hour'] = $hour;
    }


    function _datetime2timestamp($i)
    {
        $formatted = $i[0].$i[1].$i[2].$i[3].'-'.$i[6].$i[7].'-'.$i[4].$i[5].strrchr($i, 'T');
        #echo $formatted;
        return strtotime($formatted);
    }


    function getDayUsagePercentage($year, $month, $day)
    {
        $duration = 0;
        $date = $year.$month.$day;
        $arr = $this->displayScheduleMethod($date.'T00:00:00', $date.'T23:59:59.999999');
        if (!count($arr)) {
            return FALSE;
        }
        foreach ($arr as $val) {
            $duration =+ $this->_datetime2timestamp($val['end'])-$this->_datetime2timestamp($val['start']);

        }
        return $duration/86400*100;
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
        $datetime = $formdate['datetime'];

        $r = $this->spc->UploadPlaylistMethod($this->Base->sessid, $gunid, '20050308T04:44:00.000000'.UI_TIMEZONE);
        var_dump($r);
    }


    function displayScheduleMethod($from, $to)
    {
        $r = $this->spc->displayScheduleMethod($this->Base->sessid, $from, $to);
        return $r;
    }
}
?>
