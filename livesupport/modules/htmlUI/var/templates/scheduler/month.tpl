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
                <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&year={$_Day.year}&month={$_Day.month}&day={$_Day.day}')">
                <p>{$_Day.day}</p>
                <div class="scala">
                    <div class="scala_in" style="width: {$percentage}px;"></div> <!-- fullsize 96px, multiple of 24 -->
                </div>
                </a>
            </td>

        {if $_Day.isLast}
        </tr>
        {/if}
    {/foreach}
</div>
</div>
