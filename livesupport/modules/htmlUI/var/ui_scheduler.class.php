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
    }


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function set($arr)
    {
        extract($arr);
        if ($view)  $this->curr['view'] = $view;
        if ($day)   $this->curr['day']  = $day;
    }


    function displaySchedule()
    {
        include_once dirname(__FILE__).'/SchedulerPhpClient.class.php';

        // scheduler client instantiation:
        $spc =& SchedulerPhpClient::factory($this->Base->dbc, $mdefs, $this->Base->config);

        // call of chosen function by name according to key values in $mdefs array:
        // (for testing on storageServer XMLRPC I've changes confPrefix in
        //  SchedulerPhpClient constructor from 'scheduler' to 'storage' value)
        $r = $spc->DisplayScheduleMethod($this->Base->sessid, '2005-01-01 00:00:00.000000', '2005-02-01 00:00:00.000000');
        var_dump($r);
    }

}
?>
