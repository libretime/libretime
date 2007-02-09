{assign var="_PL_activeId" value=$PL->getActiveId()}
{if $isHub}
	{assign var="action_handler" value="library/hub_actionhandler.tpl"}
{else}
	{assign var="action_handler" value="library/actionhandler.tpl"}
{/if}

{if $_results.cnt > 0}
    <form name="SEARCHRESULTS">
    <div class="head" style="width:600px; height: 21px;">&nbsp;</div>
    <div class="container_table" style="width: 600px; height: auto;">

            <table style="width: 600px;">
                <tr class="blue_head">
                    <td style="width: 20px"><input type="checkbox" name="all" onClick="collector_switchAll('SEARCHRESULTS')"></td>
                    <td style="width: 200px;"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:title', 'order');" id="blue_head">##Title##</a></td>
                    <td style="width: 195px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:creator', 'order');" id="blue_head">##Creator##</a></td>
                    <td style="width: 195px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:source', 'order');" id="blue_head">##Album##</a></td>
                    <td style="width: 25px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=ls:track_num', 'order');" id="blue_head">##Track##</a></td>
                    <td><a href="#"  onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dcterms:extent', 'order');" id="blue_head">##Duration##</a></td>
                    <td style="width: 41px; border: 0; text-align: center">{*<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=type', 'order');" id="blue_head">*}##Type##{*</a>*}</td>
                </tr>
                {foreach from=$_results.items item=i}
            <!-- start item -->
                <tr class="background-color: {cycle values='blue1, blue2'}">
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td {include file=$action_handler} style="cursor: pointer">
                        {if $PLAYLIST.id == $i.id}
                            <b>{$i.title|truncate:30:"...":true}</b>
                        {else}
                            {$i.title|truncate:30:"...":true}
                        {/if}
                    </td>
                    <td {include file=$action_handler} style="cursor: pointer">{$i.creator}</td>
                    <td {include file=$action_handler} style="cursor: pointer">{$i.source}</td>
                    <td {include file=$action_handler} style="cursor: pointer" align="center">{$i.track_num}</td>
                    <td {include file=$action_handler} style="text-align: right; cursor: pointer">{assign var="_duration" value=$i.duration}{niceTime in=$_duration}</td>
                    <td {include file=$action_handler} style="border: 0; text-align: center; cursor: pointer">
                        <img src="img/{$i.type|lower}.png" border="0" alt="{$i.type|lower|capitalize}" {* include file="sub/alttext.tpl" *} />
                    </td>
                </tr>
            <!-- end item -->
                {/foreach}
            </table>

    </div>
    <div class="footer" style="width: 595px;">

         <div class="counter">
            {* {if $_results.prev}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev', 'pager')" id="blue_head">##previous##</a>{/if}  *}

            {foreach from=$_results.pagination item=p key=k}
                {if $k != $_results.page+1}
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}', 'pager')" id="blue_head" class="pagination_number"><span class="pagination_number">{$p}</span></a>
                {else}
                    <span class="pagination_number">{$p}</span>
                {/if}
            {/foreach}

            {* {if $_results.next}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next', 'pager')" id="blue_head">##next##</a>{/if}  *}
            &nbsp;&nbsp;
            ##Range##:&nbsp;{$_criteria.offset+1}-{if ($_criteria.offset+$_criteria.limit)>$_results.cnt}{$_results.cnt}{else}{$_criteria.offset+$_criteria.limit}{/if}&nbsp;
            ##Count##:&nbsp;{$_results.cnt}&nbsp;
            {* ##Page##:&nbsp;&nbsp;{$_results.page+1}&nbsp;&nbsp; *}

         </div>

         <select name="SEARCHRESULTS_multiaction" onChange="collector_submit('SEARCHRESULTS', this.value)">
                <option>##Multiple Action:##</option>
                <option value="SP.addItem">##Add files to ScratchPad##</option>
                {if $_PL_activeId}
                    <option value="PL.addItem">##Add files to open Playlist##</option>
                {else}
                    <option value="PL.create">##New Playlist using this files##</option>
                {/if}
                <option value="delete">##Delete files##</option>
         </select>
         <script type="text/javascript">
            // due to browser remembers filled form fields, here this is unwanted behavior
            document.forms['SEARCHRESULTS'].elements['SEARCHRESULTS_multiaction'].options[0].selected = true;
            document.forms['SEARCHRESULTS'].elements['all'].checked = false;
            collector_switchAll('SEARCHRESULTS')
         </script>

    </div>
   </form>

{else}
    ##No match found.##
{/if}

{assign var="_PL_activeId" value=null}
{assign var="_duration"    value=null}
