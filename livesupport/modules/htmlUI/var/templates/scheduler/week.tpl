{$SCHEDULER->buildWeek()}

<table border=1>

<tr>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <td>{$_Weekday.label.full}</td>
    {/foreach}
</tr>

{foreach from=$SCHEDULER->Week item="_Day"}
    {if $_Day.isFirst}
        <tr>
    {/if}

    {if $_Day.isEmpty}
        <td>&nbsp;</td>
    {else}
        <td valign="top">
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')"><b>{$_Day.day}</b></a>
            {assign var="_oneday" value=$SCHEDULER->getDayUsage($_Day.year, $_Day.month, $_Day.day)}
            {if is_array($_oneday)}
                <table border="1" style="font-family : monospace">
                {foreach from=$_oneday item="i"}
                    <tr><td>
                    Start:{$i.start|regex_replace:"/[0-9]+T/":""}
                    <br>
                    End:&nbsp;&nbsp;{$i.end|regex_replace:"/[0-9]+T/":""}
                    </td></tr>
                {/foreach}
                </table>
            {/if}
        </td>
    {/if}

    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
