{$SCHEDULER->buildDecade()}
{$SCHEDULER->buildYear()}
{$SCHEDULER->buildMonth()}
{$SCHEDULER->buildWeek()}

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
        {if $_Day.isScheduled}
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
