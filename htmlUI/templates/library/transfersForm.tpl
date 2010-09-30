{assign var="_PL_activeId" value=$PL->getActiveId()}

    <form name="SEARCHRESULTS">
    <div class="head" style="width:535px; height: 21px;">&nbsp;</div>
    <div class="container_table"  style="width: 555px; height: auto;">

            <table style="width: 535px;">
                <tr class="blue_head">
                    <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('SEARCHRESULTS')"></td>
                    <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=title', 'order');" id="blue_head">##Title##</a></td>
                    <td style="width: 120px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=state', 'order');" id="blue_head">##State##</a></td>
                    <td style="width: 41px; border: 0; text-align: center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reorder&by=type', 'order');" id="blue_head">##Type##</a></td>
                </tr>
				{if $_results.cnt > 0}
                {foreach from=$_results.items item=i}
            <!-- start item -->
                <tr class="background-color: {cycle values='blue1, blue2'}">
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td {include file="library/transportsActionhandler.tpl"} style="cursor: pointer">
                        {* if $PLAYLIST.id == $i.id}
                            <b>{$i.title|truncate:30}</b>
                        {else *}
                            {$i.title|truncate:30}
                        {* /if *}
                    </td>
                    <td {include file="library/transportsActionhandler.tpl"} style="cursor: pointer">
                    {$i.state} ({$i.realsize}/{$i.expectedsize})
                    </td>
                    <td {include file="library/transportsActionhandler.tpl"} style="border: 0; text-align: center; cursor: pointer">
                        <img src="img/{$i.trtype|lower}.png" border="0" alt="{$i.trtype|lower|capitalize}"
                        {if false}
                        {include file="sub/alttext.tpl"}
                        {/if}
                        />
                    </td>
                </tr>
            <!-- end item -->
                {/foreach}
				{else}
	            <tr class="background-color: {cycle values='blue1, blue2'}">
	                <td colspan="4" style="cursor: pointer; border: 0; text-align: center;">##empty##</td>
	            </tr>
				{/if}
            </table>

    </div>
    <div class="footer" style="width: 530px;">

         <div class="counter">
            {* {if $_results.prev}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev', 'pager')" id="blue_head">##previous##</a>{/if}  *}

            {foreach from=$_results.pagination item=p key=k}
                {if $k != $_results.page+1}
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}', 'pager')" id="blue_head">{$p}</a>
                {else}
                    {$p}
                {/if}
            {/foreach}

            {* {if $_results.next}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next', 'pager')" id="blue_head">##next##</a>{/if}  *}
            &nbsp;&nbsp;
            ##Range##:&nbsp;{$_results.trShowInfo.offset+1}-{if ($_results.trShowInfo.offset+$_results.trShowInfo.limit)>$_results.cnt}{$_results.cnt}{else}{$_results.trShowInfo.offset+$_results.trShowInfo.limit}{/if}&nbsp;
            ##Count##:&nbsp;{$_results.cnt}&nbsp;
            {* ##Page##:&nbsp;&nbsp;{$_results.page+1}&nbsp;&nbsp; *}

         </div>

         <select name="SEARCHRESULTS_multiaction" onChange="collector_submit('SEARCHRESULTS', this.value)">
                <option>##Multiple Action:##</option>
                <option value="TR.pause">##Pause transfer##</option>
                <option value="TR.resume">##Resume transfer##</option>
                <option value="TR.cancel">##Cancel transfer##</option>
         </select>
         <script type="text/javascript">
            // due to browser remembers filled form fields, here this is unwanted behavior
            document.forms['SEARCHRESULTS'].elements['SEARCHRESULTS_multiaction'].options[0].selected = true;
            document.forms['SEARCHRESULTS'].elements['all'].checked = false;
            collector_switchAll('SEARCHRESULTS')
         </script>

    </div>
   </form>

{assign var="_PL_activeId" value=null}
{assign var="_duration"    value=null}
