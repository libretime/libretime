{$SCHEDULER->buildDecade()}
{$SCHEDULER->buildYear()}
{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

<!-- start calendar tabs -->
        <div id="tabnavsmall">
            <ul>
            <li><a href="javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day')">##Day##</a></li>
            <li><a href="javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week')">##Week##</a></li>
            <li><a href="javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=month')">##Month##</a></li>
            <li><a href="javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&today=1')">##Today##</a></li>
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
                            <td colspan=6>
            <form name="calendar" style="margin: 2;">
                <select id="month" style="margin-top: 0;font-size:9px;" name="month" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=' +  document.forms['calendar'].month.value)">
                {foreach from=$SCHEDULER->Year item="_Month"}
                    <option value="{$_Month.month}">{tra str=$_Month.label.full}</option>
                {/foreach}
                </select>
                &nbsp;&nbsp;&nbsp;
                <select id="year" style="margin-top: 0;font-size:9px;" name="year" onChange="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&year=' + document.forms['calendar'].year.value)">
                {foreach from=$SCHEDULER->Decade item="_Year"}
                    <option value="{$_Year.year}" {if $_Year.isSelected}selected{/if}>{$_Year.year}</option>
                {/foreach}
                </select>
                <script type="text/javascript">
                     document.forms['calendar'].month.value = '{$SCHEDULER->curr.month}';
                     document.forms['calendar'].year.value  = '{$SCHEDULER->curr.year}';
                </script>
            </form>
                            </td>
                            <td><a href="#" onCLick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&month=%2B%2B')">>></a></td>
                        </tr>
                        <tr class="blue_head">
                            <td class="week">##CW##</td>
                            {foreach from=$SCHEDULER->Week item="_Weekday"}
                                <td>{tra str=$_Weekday.label.short|truncate:2:""}</td>
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
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')" class="full">{$_Day.day}</a>
        {else}
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.day}</a>
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
