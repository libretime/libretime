{assign var="_PL_activeId" value=$PL->getActiveId()}
{if $isHub}
	{assign var="action_handler" value="library/hub_actionhandler.tpl"}
{else}
	{assign var="action_handler" value="library/actionhandler.tpl"}
{/if}

{if $_results.cnt > 0}
    <form name="SEARCHRESULTS">
    <div class="container_table">
            <table id="search_results">
                <tr class="blue_head">
                    <td><input type="checkbox" name="all" onClick="collector_switchAll('SEARCHRESULTS')"></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:title" >##Title##</a></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:creator" >##Creator##</a></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dc:source" >##Album##</a></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=ls:track_num" >##Track##</a></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=dcterms:extent" >##Length##</a></td>
                    <td><a href="{$UI_HANDLER}?act={$_act_prefix}.reorder&by=type" >##Type##</a></td>
                </tr>
                {foreach from=$_results.items item=i}
            <!-- start item -->
                <tr class="{cycle values='blue1, blue2'}">
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td {include file=$action_handler}>
                        {if $i.type == 'playlist' && $PL->isAvailable($i.id) == false}
                            <b>{$i.title|truncate:30:"...":true}</b>
                        {else}
                            {$i.title|truncate:30:"...":true}
                        {/if}
                    </td>
                    <td {include file=$action_handler}>
                    {$i.creator}
                    {if $i.type == 'playlist' && $PL->isAvailable($i.id) == false}
                    	(editing: {$PL->isUsedBy($i.id)})
                    {/if}
                    </td>
                    <td {include file=$action_handler}>{$i.source}</td>
                    <td {include file=$action_handler}>{$i.track_num}</td>
                    <td {include file=$action_handler}>{assign var="_duration" value=$i.duration}{niceTime in=$_duration}</td>
                    <td {include file=$action_handler}>
                    	{if $i.type == 'playlist' && $PL->isAvailable($i.id) == false}
                            <div>
                        	<img src="html/img/{$i.type|lower}.png" border="0" alt="{$i.type|lower|capitalize}"/>
                        	<img src="html/img/ico_lock.png">
                        	</div>
                        {else}
                        	<img src="html/img/{$i.type|lower}.png" border="0" alt="{$i.type|lower|capitalize}"/>
                        {/if}
                    </td>
                </tr>
            <!-- end item -->
                {/foreach}
            </table>

    </div>
    <div class="footer">

         <div class="counter">
            {* {if $_results.prev}<a href="{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev" id="blue_head">##previous##</a>{/if}  *}

            {foreach from=$_results.pagination item=p key=k}
                {if $k != $_results.page+1}
                    <a href="{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}" id="blue_head" class="pagination_number"><span class="pagination_number">{$p}</span></a>
                {else}
                    <span class="pagination_number">{$p}</span>
                {/if}
            {/foreach}

            {* {if $_results.next}<a href="{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next" id="blue_head">##next##</a>{/if}  *}
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
