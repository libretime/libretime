{$SCHEDULER->buildDecade()}
{$SCHEDULER->buildYear()}
{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

<!-- start calendar tabs -->
        <div id="tabnavsmall">
            <ul>
            <li><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day')">##Day##</a></li>
            <li><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week')">##Week##</a></li>
            <li><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=month')">##Month##</a></li>
            <!-- <input type="button" onClick="popup('{$UI_BROWSER}?popup[]=SCHEDULER.schedule', 'Schedule', 600, 400)" value="Schedule">  -->
            <li><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&today=1')">##Today##</a></li>
            </ul>
        </div>
<!-- end calendar tabs -->
<!-- start calendar navigation -->
<div class="container_elements">
    <h1>##Scheduler Navigation##</h1>
            <div class="container_calender_overview">
                <div class="calender_overview">
                    <table class="calender_overview_table">
                        <tr>
                            <td><a href="#" onCLick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=--')"><<</a></td>
                            <td colspan=4>
            <form name="calendar_month" style="margin: 2;">
                <select id="month" style="margin-top: 0;font-size:9px;" name="month" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=' +  document.forms['calendar_month'].month.value)">
                {foreach from=$SCHEDULER->Year item="_Month"}
                    <option value="{$_Month.month}">{$_Month.label.full}</option>
                {/foreach}
                </select>
            </form>
                            </td>
                            <td colspan=2>
            <form name="calendar_year" style="margin: 2;">
                <select id="year" style="margin-top: 0;font-size:9px;" name="year" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&year=' + document.forms['calendar_year'].year.value)">
                {foreach from=$SCHEDULER->Decade item="_Year"}
                    <option value="{$_Year.year}" {if $_Year.isSelected}selected{/if}>{$_Year.year}</option>
                {/foreach}
                </select>
                <script type="text/javascript">
                     document.forms['calendar_month'].month.value = '{$SCHEDULER->curr.month}';
                     document.forms['calendar_year'].year.value   = '{$SCHEDULER->curr.year}';
                </script>
            </form>
                            </td>
                            <td><a href="#" onCLick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=%2B%2B')">>></a></td>
                        </tr>
                        <tr class="blue_head">
                            <td class="week">##CW##</td>
                            {foreach from=$SCHEDULER->Week item="_Weekday"}
                                <td>{$_Weekday.label.short|truncate:2:""}</td>
                            {/foreach}
                        </tr>
                        <tr>
{foreach from=$SCHEDULER->Month item="_Day"}
                            <!-- calendar week first -->
                            {if $_Day.isFirst}
                            <tr>
                                <td class="week"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week&day={$_Day.day}&month={$_Day.month}')">{$_Day.week}</a></td>
                            {/if}
                            <!-- check for different kind of day displays -->

        {if $_Day.isEmpty}
                    <td class="not_this_month">
        {elseif $_Day.isToday}
                    <td class="today">
        {elseif $_Day.isCurrent}
                    <td class="current">
        {else}
                    <td class="nothing">
        {/if}
        {if $_Day.isSelected}
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')" class="full">{$_Day.day}</a>
        {else}
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.day}</a>
        {/if}

                    </td>
        {if $_Day.isLast}
            </tr>
        {/if}
{/foreach}
                    </table>
                </div>
            </div>
</div>
<!-- end calendar navigation -->



{*
<table border=1>
    <tr><th colspan="8">{$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</th></tr>

    <tr>
        <th><a href="#" onCLick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=--')"><<</a></th>
        <th colspan="4">
            <form name="calendar_month">
                <select name="month" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=' +  document.forms['calendar_month'].month.value)">
                {foreach from=$SCHEDULER->Year item="_Month"}
                    <option value="{$_Month.month}">{$_Month.label.full}</option>
                {/foreach}
                </select>
            </form>
        </th>
        <th colspan="2">
            <form name="calendar_year">
                <select name="year" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&year=' + document.forms['calendar_year'].year.value)">
                {foreach from=$SCHEDULER->Decade item="_Year"}
                    <option value="{$_Year.year}" {if $_Year.isSelected}selected{/if}>{$_Year.year}</option>
                {/foreach}
                </select>
            </form>
            <script type="text/javascript">
                 document.forms['calendar_month'].month.value = '{$SCHEDULER->curr.month}';
                 document.forms['calendar_year'].year.value   = '{$SCHEDULER->curr.year}';
            </script>
        </th>
        <th><a href="#" onCLick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=%2B%2B')">>></a></th>
    </tr>

    {foreach from=$SCHEDULER->Month item="_Day"}
        {if $_Day.isFirst}
            <tr>
                <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week&day={$_Day.day}&month={$_Day.month}')">{$_Day.week}</a></th>
        {/if}
                <td>

        {if $_Day.isEmpty}
                    <div>
        {elseif $_Day.isToday}
                    <div style="background-color: grey">
        {elseif $_Day.isCurrent}
                    <div style="background-color: lightblue">
        {else}
                    <div style="background-color: white">
        {/if}
        {if $_Day.isSelected}
                        <b><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.day}</a></b>
        {else}
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.day}</a>
        {/if}

                    </div>
                 </td>
        {if $_Day.isLast}
            </tr>
        {/if}
    {/foreach}

</table>
*}
