{$SCHEDULER->buildWeek()}
{assign var="_scale" value=$SCHEDULER->getDayTimingScale()}

<table border="1">

    <tr>
        <td rowspan="26" valign="top"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=--')"><<</a> </td>
        <td></td>
   {foreach from=$SCHEDULER->Week item="_Day"}      {* hier werden die Tagesnamen angezeigt *}
       <th width="100"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')">{$_Day.label.full}</a></th>
   {/foreach}
        <td rowspan="26" valign="top"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&week=%2B%2B')">>></a></td>
    </tr>

    <tr>
        <td></td>
    {foreach from=$SCHEDULER->Week item="_Day"}     {* hier werden die Tagesnummern angezeigt *}
        <td {if $_Day.isToday} bgcolor="grey"{/if}>
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&day={$_Day.day}&month={$_Day.month}&year={$_Day.year}')"><b>{$_Day.day}</b></a>
        </td>
    {/foreach}
    </tr>


    {assign var="_entrys" value=$SCHEDULER->getWeekEntrys()}
    {foreach from=$_scale item="_hour"}

        {assign var="_year"  value=$_Day.year}
        {assign var="_month" value=$_Day.month}

        <tr>                        {* jede Reiehe entspr. einer Stunde *}
            <td>{$_hour}</td>           {* linke spalte mit Uhrzeit *}

        {foreach from=$SCHEDULER->Week item="_day"}
                                        {* jede Zelle entpr. einem Tag (natürlich nur einer h davon) *}
            <td bgcolor="grey" onContextmenu="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={$_hour}', 'SCHEDULER.addItem')">

               {if is_array($_entrys[$_day.day][$_hour])}
                    {foreach from=$_entrys[$_day.day][$_hour] item="i"}    {* hier werden die Einträge welche in der jeweil. h beginnen durchlaufen *}
                        <div style="border-style: dotted">
                            {$i.title}
                            <br>
                            {$i.start}-{$i.end}
                            <br>
                            {$i.creator}
                        </div>
                    {/foreach}
               {/if}

            </td>
        {/foreach}
        </tr>

    {/foreach}


</table>

