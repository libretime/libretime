{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

<table border=1>

<tr>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <th>{$_Weekday.label.full}</th>
    {/foreach}
</tr>

{foreach from=$SCHEDULER->Month item="_Day"}
    {assign var="percentage" value=$SCHEDULER->getDayUsagePercentage($_Day.year, $_Day.month, $_Day.day)}

    {if $_Day.isFirst}
        <tr>
    {/if}

    {if $_Day.isEmpty}
        <td>&nbsp;</td>
    {else}
        <td width="80">
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}')">{$_Day.day}
            <div><img src="img/percentage_red.png" width="{if $percentage>50}{$percentage}{elseif $percentage>0}{$percentage+2}{else}0{/if}%" height="10" border="0"><img src="img/percentage_blue.png" width="{if $percentage>50}{$null-$percentage+100}{elseif $percentage>0}{$NULL-$percentage-2+100}{else}100{/if}%" height="10" border="0"></div>
        </td>
    {/if}

    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
