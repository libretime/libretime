{$SCHEDULER->buildWeek()}

Week View

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
        <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}')">{$_Day.day}</td>
    {/if}

    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
