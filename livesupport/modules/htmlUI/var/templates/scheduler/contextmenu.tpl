onMouseOver="highlight()"
onMouseOut="darklight()"
onContextmenu="return menu('year={$_year}&month={$_month}&day={$_day}&hour={$_hour}', {$moreContextBefore} 'SCHEDULER.schedule' {$moreContextAfter})"


{assign var="moreContextBefore" value=""}
{assign var="moreContextAfter" value=""}
