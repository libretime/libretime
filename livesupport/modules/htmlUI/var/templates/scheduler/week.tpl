{$SCHEDULER->buildWeek()}
{assign var="_divisor" value=180}
{assign var="_minwidth" value=20}

<table border="1">

<tr>
    <td rowspan="2"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=--')"><<</a> </td>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <th>{$_Weekday.label.full}</th>
    {/foreach}
    <td rowspan="2"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=%2B%2B')">>></a></td>
</tr>

<tr>
{foreach from=$SCHEDULER->Week item="_Day"}
    <td valign="top">
        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')"><b>{$_Day.day}</b></a>

        {assign var="_oneday" value=$SCHEDULER->getDayTiming($_Day.year, $_Day.month, $_Day.day)}
        <table border="0" cellspacing="0" cellpadding="0">
        {foreach from=$_oneday item="i"}
            <tr height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}" style="font-family: monospace" valign="top">
            {if is_array($i.entry)}
                <td bgcolor="#ffcacb" width="100" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}" onMouseover="" onMouseout="">
                {if $i.length/$_divisor > $_minwidth}
                <small>
                <b>{$i.entry.title}</b>
                <br>
                {$i.entry.start|regex_replace:"/[0-9]+T/":""|truncate:5:""}-{$i.entry.end|regex_replace:"/[0-9]+T/":""|truncate:5:""}
                <br>
                {$i.entry.creator}
                </small>
                {/if}
                </td>
            {else}
                <td bgcolor="#97bacf" width="100" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}">
                </td>
            {/if}
            </tr>
        {/foreach}
        </table>

    </td>
{/foreach}
</tr>

</table>
