{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

<table border=1>
<tr><th colspan="8">{$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</th></tr>
<tr> <td></td>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <td>{$_Weekday.label.short}</td>
    {/foreach}
</tr>
{foreach from=$SCHEDULER->Month item="_Day"}
    {if $_Day.isFirst}
        <tr>
            <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week={$_Day.week}')">{$_Day.week}</a></th>
    {/if}
            <td>
    {if $_Day.isEmpty}
            &nbsp;
    {elseif $_Day.isSelected}
            <b>{$_Day.day}</b>
    {else}
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}')">{$_Day.day}</a>
    {/if}

             </td>
    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
