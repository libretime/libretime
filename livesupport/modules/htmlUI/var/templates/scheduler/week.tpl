{$SCHEDULER->buildWeek()}
{assign var="_scale" value=$SCHEDULER->getDayTimingScale()}

<div class="content" style="width: auto;">
<!-- start scheduler -->
    <div class="container_elements" style="width: 790px;">
        <div class="head_scheduler" style=""><h1>##Weekly View##</h1></div>

        {*
        <div class="container_button_scheduler">
            <input type="button" class="button_large" value="Start Scheduler" />
            <input type="button" class="button_large" value="Stop Scheduler" />
        </div>
        *}

        <div class="clearer">&nbsp;</div>
        <p>{$SCHEDULER->curr.week}. ##calendar week## {$SCHEDULER->curr.year}</p>

        <table class="scheduler_week">
            <tr>
                {* Link Woche zurück <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=--')"><<</a> *}
                <th class="firstrow" style="border-left: 1px solid #ccc"></th>
            {foreach from=$SCHEDULER->Week item="_Day"}      {* hier werden die Tagesnamen angezeigt *}
                <th class="date"></th>
                <th class="day"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.label.full}</a></th>
            {/foreach}
                {* Link Woche vor <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=%2B%2B')">>></a> *}
            </tr>

            <tr>
                <td class="firstrow_secondcol" style="border-left: 1px solid #ccc"></td>
            {foreach from=$SCHEDULER->Week item="_Day"}     {* hier werden die Tagesnummern angezeigt *}
                <td class="date_secondcol">
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')"><b>{$_Day.day}</b></a>
                </td>
                <td class="day_secondcol"></td>
            {/foreach}
            </tr>

        {assign var="_entrys" value=$SCHEDULER->getWeekEntrys()}
        {foreach from=$_scale item="_hour"}
            {assign var="_year"  value=$_Day.year}
            {assign var="_month" value=$_Day.month}

            <tr>
                <td class="firstrow" style="border-left: 1px solid #ccc">{$_hour}</td>
            {foreach from=$SCHEDULER->Week item="_day"}
            {if is_array($_entrys[$_day.day][$_hour])}
                <td class="date_full" {include file="scheduler/context_week.tpl"}></td>
                <td class="day_full">
                {foreach from=$_entrys[$_day.day][$_hour] item="i"}
                    <div {include file="scheduler/actionhandler.tpl"}>
                    <h2>{$i.title|truncate:12}</h2>
                    <p>{$i.start|truncate:5:""} - {$i.end|truncate:5:""}</p>
                    <p>{$i.creator}</p>
                    </div>
                    {* <div style="background-color: #FF6F1F; height: 3px" onClick="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={getHour time=$i.end}&minute={getMinute time=$i.end}&second={getSecond time=$i.end}', 'SCHEDULER.addItem')"></div> *}
                {/foreach}
                </td>
            {else}
                <td class="date" {include file="scheduler/context_week.tpl"}></td>
                <td class="day"  {include file="scheduler/context_week.tpl"}></td>
            {/if}
            {/foreach}
            </tr>
        {/foreach}

        </table>
        </div>
        <div class="clearer">&nbsp;</div>
    </div>
<!-- end playlist editor -->
</div>
