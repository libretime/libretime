{$SCHEDULER->buildDay()}
{assign var="_scale" value=$SCHEDULER->getDayTimingScale()}

<div class="content">
<div class="container_elements">
    <h1>##Daily View##</h1>
    <div class="clearer">&nbsp;</div>
    <p>{$SCHEDULER->curr.monthname} {$SCHEDULER->curr.day}, {$SCHEDULER->curr.year}</p>

    <form name="SCHEDULER">
    <table class="scheduler_day" style="width: 606px;">

        {*    Tag vor/zurück, code um heutigen Tag abzufragen
        <tr>
            <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day=--')"><<</a></th>
            <th colspan="3" {if $SCHEDULER->curr.isToday} bgcolor="grey"{/if}>{$SCHEDULER->curr.dayname}, {$SCHEDULER->curr.year}-{$SCHEDULER->curr.month}-{$SCHEDULER->curr.day}</th>
            <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&day=%2B%2B')">>></a></th>
        </tr>
        *}

        <tr class="blue_head">

            {*  Multi-action checkboxen, die brauchen wir aber höchstens bei den Einträgen um mehere auf einmal zu löschen ?
            <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('SCHEDULER')"></td>
            *}

            <td style="width: 95px">Time</td>
            <td style="width: 481px; border-right: 0;">Show Info</td>
        </tr>

        {assign var="_entrys" value=$SCHEDULER->getDayEntrys()}
        {foreach from=$_scale item="_hour"}
            {assign var="_year"  value=$_Day.year}
            {assign var="_month" value=$_Day.month}

            <tr class="blue2" onContextmenu="return contextmenu('year={$_day.year}&month={$_day.month}&day={$_day.day}&hour={$_hour}', 'SCHEDULER.addItem')">
                {*  <td style="border-left: 1px solid #ccc;"><input type="checkbox" class="checkbox" /></td> *}
                <td>{$_hour|string_format:"%02d"}:00</td>
                <td style="border-right: 1px solid #ccc;">
                {if is_array($_entrys[$_hour])}
                    {foreach from=$_entrys[$_hour] item="i"}    {* hier werden die Einträge welche in der jeweil. h beginnen durchlaufen *}
                        {$i.title}:
                        {$i.start}-{$i.end}
                        {$i.creator}
                {/foreach}
                {/if}
                </td>
            </tr>

        {/foreach}
    </table>
    </form>

    {*  Multiaction buttons
    <div class="container_button" style="float: right; margin-top: 10px;">
        <input type="button" class="button_large" value="Edit Playlist" />
        <input type="button" class="button_large" value="Delete Playlist" />
        <input type="button" class="button_large" value="Add Playlist" />
    </div>
    <div class="clearer">&nbsp;</div>
     *}

</div>
</div>

