{getHour time=$i.end assign="_endH"}

{if $_endH == $_hour}
    onClick="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={getHour time=$i.end}&minute={getMinute time=$i.end}&second={getSecond time=$i.end}', 'SCHEDULER.addItem')"
{else}
    onClick="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={$_hour}&minute=0&second=0', 'SCHEDULER.addItem')"
{/if}

{assign var="_endH" value=null}
