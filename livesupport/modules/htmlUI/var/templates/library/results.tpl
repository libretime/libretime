{if $_results.cnt > 0}
    <form name="SEARCHRESULTS">
    <div class="head" style="width:535px; height: 21px;">&nbsp;</div>
    <div class="container_table"  style="width: 555px; height: auto;">

            <table style="width: 535px;">
                <tr class="blue_head">
                    <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('SEARCHRESULTS')"></td>
                    <td style="width: 160px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=title', 'order');" id="blue_head">##Title##</a></td>
                    <td style="width: 134px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=creator', 'order');" id="blue_head">##Creator##</a></td>
                    <td style="width: 89px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=extent', 'order');" id="blue_head">##Duration##</a></td>
                    <td style="width: 37px; border: 0"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=type', 'order');" id="blue_head">##Type##</a></td>
                </tr>
                {foreach from=$_results.items item=i}
            <!-- start item -->
                <tr class="background-color: {cycle values='blue1, blue2'}"  {assign var="moreContextBefore" value=", 'SP.addItem'"}{include file="sub/contextmenu.tpl"}>
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td>
                        {if $PLAYLIST.id == $i.id}
                            <b>{$i.title|truncate:30}</b>
                        {else}
                            {$i.title|truncate:30}
                        {/if}
                    </td>
                    <td>{$i.creator}</td>
                    <td>{$i.duration}</td>
                    <td style="border: 0"><img src="img/{$i.type|lower}.gif" border="0" alt="{$i.type}" /></td>
                </tr>
            <!-- end item -->
                {/foreach}
            </table>

    </div>
    <div class="footer" style="width: 535px;">

         <div class="counter">
            {if $_results.prev}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev', 'pager')" id="blue_head">##previous##</a>{/if}

            {foreach from=$_results.pagination item=p key=k}
                <a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}', 'pager')" id="blue_head">{$p}</a>
            {/foreach}

            {if $_results.next}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next', 'pager')" id="blue_head">##next##</a>{/if}
            &nbsp;&nbsp;
            ##Count##:&nbsp;{$_results.cnt}&nbsp;&nbsp;
            ##Page##:&nbsp;&nbsp;{$_results.page+1}&nbsp;&nbsp;
            ##Range##:&nbsp;{$_criteria.offset+1}-{if ($_criteria.offset+$_criteria.limit)>$_results.cnt}{$_results.cnt}{else}{$_criteria.offset+$_criteria.limit}{/if}
         </div>
        {if $_PL_activeId}
            <input type="button" class="button" value="##To Playlist##" onClick="collector_submit('SEARCHRESULTS', 'PL.addItem')">
        {else}
            <input type="button" class="button" value="##New Playlist##" onClick="collector_submit('SEARCHRESULTS', 'PL.create')">
        {/if}
    </div>
   </form>

{else}
    ##No match found.##
{/if}