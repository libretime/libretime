{$SCHEDULER->buildWeek()}
{assign var="_divisor" value=180}
{assign var="_minwidth" value=20}

<table border="1">

<tr>
    <td rowspan="2"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=--')"><<</a> </td>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <td>{$_Weekday.label.full}</td>
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
                    <td bgcolor="pink" width="80" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}">
                    {if $i.length/$_divisor > $_minwidth}
                    <small>
                    Start:{$i.entry.start|regex_replace:"/[0-9]+T/":""}
                    <br>
                    End:&nbsp;&nbsp;{$i.entry.end|regex_replace:"/[0-9]+T/":""}
                    </small>
                    {/if}
                    </td>
                {else}
                    <td bgcolor="lightblue" width="80" height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}">
                    </td>
                {/if}
                </tr>
            {/foreach}

            </table>

    </td>
{/foreach}
</tr>

</table>
