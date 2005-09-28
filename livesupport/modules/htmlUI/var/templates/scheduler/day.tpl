{$SCHEDULER->buildDay()}
{assign var="_scale"  value=$SCHEDULER->getDayTimingScale()}   {* get the 24h scale *}
{assign var="_entrys" value=$SCHEDULER->getDayEntrys()}        {* get all entrys on given day from scheduler *}
{assign var="_day"    value=$SCHEDULER->curr}  				{* to have year, month, day in between_additem.tpl *}

<div class="content">
<div class="container_elements">
    <h1>##Daily View##</h1>
    <div class="clearer">&nbsp;</div>
    <p>##{$SCHEDULER->curr.monthname}## {$SCHEDULER->curr.day}, {$SCHEDULER->curr.year}</p>

    <form name="SCHEDULER">
    <table class="scheduler_day" style="width: 606px;">


        <tr class="blue_head">
            <td style="border-left: 1px solid #ccc; width: 95px">##Time##</td>
            <td style="width: 481px; border-right: 0;">##Show Info##</td>
        </tr>

        {foreach from=$_scale item="_hour"}

            {if is_array($_entrys[$_hour])}
                <tr class="blue1">
                	<td style="border-left: 1px solid #ccc; cursor: pointer" {include file="scheduler/day_additem.tpl"}>
                   	{$_hour|string_format:"%02d"}:00
                   </td>
                   <td style="border-right: 1px solid #ccc;">
	                   {if $_entrys[$_hour].end}
	                       {include file="scheduler/between_additem.tpl"}
	                   {/if}

	                   {if $_entrys[$_hour].start}
	                       {foreach from=$_entrys[$_hour].start item="i"}
	                          <div {include file="scheduler/removeitem.tpl"}>
	                              <img src="img/playlist.png" border="0" {include file="sub/alttext.tpl"}>
	                              &nbsp;
	                              <b>{$i.title}</b>
	                              {$i.start}-{$i.end}
	                              {$i.creator}
                                 {if $i.endshere}
                                 	{include file="scheduler/between_additem.tpl"}
                                 {/if}
	                          </div>
	                       {/foreach}
	                   {/if}

	                   {if $_entrys.span[$_hour]}
                          span
	                   {/if}
                   </td>
                </tr>
            {else}
                <tr class="blue2" {include file="scheduler/day_additem.tpl"}>
                    <td style="border-left: 1px solid #ccc;">{$_hour|string_format:"%02d"}:00</td>
                    <td style="border-right: 1px solid #ccc;"></td>
                </tr>
            {/if}
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