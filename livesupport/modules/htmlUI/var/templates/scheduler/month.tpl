{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}


		<!-- start scheduler -->
			<div class="content">
			<div class="container_elements">
				<h1>##Monthly View##</h1>
				<table class="scheduler_month">
					<tr>
                        {foreach from=$SCHEDULER->Week item="_Weekday"}
        <th>{$_Weekday.label.full}</th>
    {/foreach}																																							
					</tr>	

{foreach from=$SCHEDULER->Month item="_Day"}
    {if $_Day.isSelected}
        {assign var="percentage" value=$SCHEDULER->getDayUsagePercentage($_Day.year, $_Day.month, $_Day.day)}
    {else}
        {assign var="percentage" value="0"}
    {/if}

    {if $_Day.isFirst}
        <tr>
    {/if}

        <td {if $_Day.isToday} class="today"{/if}>
            <p><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&year={$_Day.year}&month={$_Day.month}&day={$_Day.day}')">{$_Day.day}</a></p>
            <div class="scala">
                <div class="scala_in" style="width: {$percentage}px;"></div> <!-- fullsize 96px, multiple of 24 -->
            </div>
        </td>

    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}
</div>
{*
<table border=1>

<tr>
    {foreach from=$SCHEDULER->Week item="_Weekday"}
        <th>{$_Weekday.label.full}</th>
    {/foreach}
</tr>

{foreach from=$SCHEDULER->Month item="_Day"}
    {if $_Day.isSelected}
        {assign var="percentage" value=$SCHEDULER->getDayUsagePercentage($_Day.year, $_Day.month, $_Day.day)}
    {else}
        {assign var="percentage" value="0"}
    {/if}

    {if $_Day.isFirst}
        <tr>
    {/if}

        <td width="80" {if $_Day.isToday} bgcolor="grey"{/if}>
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&year={$_Day.year}&month={$_Day.month}&day={$_Day.day}')"><b>{$_Day.day}</b>
            <div><img src="img/percentage_red.png" width="{if $percentage>50}{$percentage}{elseif $percentage>0}{$percentage+2}{else}0{/if}%" height="10" border="0"><img src="img/percentage_blue.png" width="{if $percentage>50}{$null-$percentage+100}{elseif $percentage>0}{$NULL-$percentage-2+100}{else}100{/if}%" height="10" border="0"></div>
        </td>

    {if $_Day.isLast}
        </tr>
    {/if}
{/foreach}

</table>
*}