{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

<table border=1>
<tr><th colspan="8">{$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</th></tr>
<tr> <td>&nbsp;</td>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <td>{$_Weekday.label.short}</td>
    {/foreach}
</tr>
{foreach from=$SCHEDULER->Month item="_Day"}
    {if $_Day.isFirst}
        <tr>
            <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week&day={$_Day.day}&month={$_Day.month}')">{$_Day.week}</a></th>
    {/if}
            <td>

    {if $_Day.isEmpty}
                <div>
    {elseif $_Day.isSelected}
                <div style="background-color: lightblue">
    {else}
                <div style="background-color: white">
    {/if}
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}')">{$_Day.day}</a>
                </div>
             </td>
    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
