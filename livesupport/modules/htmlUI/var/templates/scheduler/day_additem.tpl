style="cursor: pointer"

{getHour time=$i.end assign="_endH"}

{if $_endH == $_hour}
    onClick="return contextmenu('year={$SCHEDULER->curr.year}&month={$SCHEDULER->curr.month}&day={$SCHEDULER->curr.day}&hour={getHour time=$i.end}&minute={getMinute time=$i.end}&second={getSecond time=$i.end plus=1}', 'SCHEDULER.addItem')"
{else}
    onClick="return contextmenu('year={$SCHEDULER->curr.year}&month={$SCHEDULER->curr.month}&day={$SCHEDULER->curr.day}&hour={$_hour}&minute=0&second=0', 'SCHEDULER.addItem')"
{/if}

{assign var="_endH" value=null}
