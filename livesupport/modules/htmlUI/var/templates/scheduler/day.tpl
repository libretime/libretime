{$SCHEDULER->buildDay()}

Day View

<table border=1>

{foreach from=$SCHEDULER->Day item="_Hour"}

    <tr><td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}')">{$_Hour.hour}</td></tr>

{/foreach}

</table>
