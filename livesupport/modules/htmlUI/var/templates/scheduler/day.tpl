{$SCHEDULER->buildDay()}
{assign var="_scale" value=$SCHEDULER->getDayTimingScale()}


<table border=1>
    <tr>
        <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day=--')"><<</a></th>
        <th colspan="3" {if $SCHEDULER->curr.isToday} bgcolor="grey"{/if}>{$SCHEDULER->curr.dayname}, {$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</th>
        <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day=%2B%2B')">>></a></th>
    </tr>

    {assign var="_entrys" value=$SCHEDULER->getDayEntrys()}
    {foreach from=$_scale item="_hour"}

        {assign var="_year"  value=$_Day.year}
        {assign var="_month" value=$_Day.month}

        <tr>                        {* jede Reiehe entspr. einer Stunde *}
            <td>{$_hour}</td>           {* linke spalte mit Uhrzeit *}


            <td bgcolor="grey" onContextmenu="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={$_hour}', 'SCHEDULER.addItem')">

               {if is_array($_entrys[$_hour])}
                    {foreach from=$_entrys[$_hour] item="i"}    {* hier werden die Einträge welche in der jeweil. h beginnen durchlaufen *}
                        <div style="border-style: dotted">
                            {$i.title}
                            <br>
                            {$i.start}-{$i.end}
                            <br>
                            {$i.creator}
                        </div>
                    {/foreach}
               {/if}

            </td>

        </tr>

    {/foreach}
</table>










{*      timing-ansich

{assign var="_divisor" value=180}
{assign var="_minwidth" value=10}


<table border=1>
<tr>
    <td valign="top">
        <b>{$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</b></a>

        {assign var="_oneday" value=$SCHEDULER->getDayTiming($SCHEDULER->curr.year, $SCHEDULER->curr.month, $SCHEDULER->curr.day)}
        <table border="0" cellspacing="0" cellpadding="0">
        {foreach from=$_oneday item="i"}
            <tr height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}" style="font-family: monospace" valign="top">
            {if is_array($i.entry)}
                <td bgcolor="#ffcacb" width="600" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}" {include file="scheduler/contextmenu.tpl"}>
                {if $i.length/$_divisor > $_minwidth}
                <small>
                <b>{$i.entry.title}</b>
                {$i.entry.start|regex_replace:"/[0-9]+T/":""}-{$i.entry.end|regex_replace:"/[0-9]+T/":""}
                {$i.entry.creator}
                </small>
                {/if}
                </td>
            {else}
                <td bgcolor="#97bacf" width="600" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}">
                </td>
            {/if}
            </tr>
        {/foreach}
        </table>

    </td>
</tr>
</table>
*}
