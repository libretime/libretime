{$SCHEDULER->buildDay()}
{assign var="_usage" value=$SCHEDULER->getDayUsage($SCHEDULER->curr.year, $SCHEDULER->curr.month, $SCHEDULER->curr.day)}

<table border=1>
{foreach from=$SCHEDULER->Day item="_Hour"}
    {assign var="_hour" value=$_Hour.hour}
    {assign var="_border" value=""}
    <tr>
        <td {include file="scheduler/contextmenu.tpl"}>
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&hour={$_Hour.hour}')">{$_Hour.hour}
        </td>
        <td>
            {foreach from=$_usage item="_entry"}
                {if $_entry.timestamp >= $_Hour.timestamp && $_entry.timestamp < $_Hour.timestamp+3600}
                    {$_border}
                    <b>{$_entry.title}</b>
                    {$_entry.start|regex_replace:"/[0-9]+T/":""|truncate:5:""}-{$_entry.end|regex_replace:"/[0-9]+T/":""|truncate:5:""}
                    {$_entry.creator}
                    {assign var="_border" value="|"}
                {/if}
            {/foreach}
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
