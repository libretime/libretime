{$SCHEDULER->buildWeek()}
{assign var="_divisor" value=180}
{assign var="_minwidth" value=20}
{assign var="_scale" value=$SCHEDULER->getDayTimingScale()}

<table border="1">

<tr>
    <td rowspan="3" valign="top"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=--')"><<</a> </td>

{foreach from=$SCHEDULER->Week item="_Day"}
    <th colspan="2" width="100"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.label.full}</a></th>
{/foreach}

    <td rowspan="3" valign="top"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=%2B%2B')">>></a></td>
</tr>

<tr>
{foreach from=$SCHEDULER->Week item="_Day"}
    <td colspan="2" {if $_Day.isToday} bgcolor="grey"{/if}>
        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')"><b>{$_Day.day}</b></a>
    </td>
{/foreach}
</tr>

<tr>
{foreach from=$SCHEDULER->Week item="_Day"}
    {assign var="_oneday" value=$SCHEDULER->getDayTiming($_Day.year, $_Day.month, $_Day.day)}
    {assign var="_year"  value=$_Day.year}
    {assign var="_month" value=$_Day.month}
    {assign var="_day"   value=$_Day.day}
    {assign var="_hour"  value=$_Day.hour}

    <td valign="top">
        <table border="1" cellspacing="0" cellpadding="0">
        {foreach from=$_scale item="_hour"}
            <tr height="20" style="font-family: monospace;" valign="top">
                    <td bgcolor="grey" onContextmenu="return menu('year={$_year}&month={$_month}&day={$_day}&hour={$_hour}', 'SCHEDULER.addItem')">
                        <div style="padding: 1px">{$_hour|string_format:'%02d'}</div>
                    </td>
            </tr>
        {/foreach}
        </table>
    </td>

    <td>
        <table border="0" cellspacing="0" cellpadding="0">
        {foreach from=$_oneday item="i"}
            {assign var = "_start"   value = $i.entry.start|regex_replace:"/[0-9]+T/":""}
            {assign var = "_end"     value = $i.entry.end|regex_replace:"/[0-9]+T/":""}
            {assign var = "_period"  value = "$_start-$_end"}
            {assign var = "_title"   value = $i.entry.title}
            {assign var = "_creator" value = $i.entry.creator}

            <tr height="{$SCHEDULER->_oneOrMore($i.length/$_divisor)}" style="font-family: monospace;" valign="top">
            {if is_array($i.entry)}
                {if $i.length/$_divisor > $_minwidth}
                    <td bgcolor="#ffcacb" width="70" onContextmenu="return menu('gunid={$i.entry.id}', 'SCHEDULER.removeItem')"><div style="padding: 1px"><small><b>{$_title}</b><br>{$_period}<br>{$_creator}</small></div></td>
                {else}
                    <td bgcolor="#ffcacb" width="70" onContextmenu="return menu('gunid={$i.entry.id}', 'SCHEDULER.removeItem')" onMouseover="mouseoverShow('<small><b>{$_title}</b><br>{$_period}<br>{$_creator}</small>')" onMouseout="mouseoverHide()"></td>
                {/if}
            {else}
                <td bgcolor="#97bacf" width="70"></td>
            {/if}
            </tr>
        {/foreach}
        </table>
    </td>

{/foreach}
</tr>

</table>
