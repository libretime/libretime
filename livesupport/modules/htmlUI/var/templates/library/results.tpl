{if $_results.cnt > 0}
    <div class="head" style="width:535px; height: 21px;">&nbsp;</div>
    <div class="container_table" style="width: 555px; height: auto;">
            <table style="width: 535px;">
                <tr class="blue_head">
                    <td style="width: 95px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=title', 'order');" id="blue_head">##Title##</a></td>
                    <td style="width: 69px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=creator', 'order');" id="blue_head">##Creator##</a></td>
                    <td style="width: 89px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=extent', 'order');" id="blue_head">##Duration##</a></td>
                    <td style="width: 197px; border: 0"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=type', 'order');" id="blue_head">##Type##</a></td>
                </tr>
                {foreach from=$_results.items item=i}
            <!-- start item -->
                <tr class="background-color: {cycle values='blue1, blue2'}"  {assign var="moreContextBefore" value=", 'SP.addItem'"}{include file="sub/contextmenu.tpl"}>
                    <td>
                                    {if $PLAYLIST.id == $i.id}
                                        <b>{$i.title|truncate:30}</b>
                                    {else}
                                        {$i.title|truncate:30}
                                    {/if}
                                </td>
                    <td>{$i.creator}</td>
                    <td>{$i.duration}</td>
                    <td style="border: 0">{$i.type}</td>
                </tr>
            <!-- end item -->
                {/foreach}
        </table>
    </div>
    <div class="footer" style="width: 530px;">
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
        <input type="button" class="button_small" value="##Go##" />
    </div>

{else}
    ##No match found.##
{/if}


{*
<div id="searchres">
<center>
{if $_results.cnt > 0}
    <table border="0" width="50%">
        <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=title', 'order');">##Title##</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=creator', 'order');">##Creator##</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=extent', 'order');">##Duration##</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=type', 'order');">##Type##</a></td>
        </tr>
        {foreach from=$_results.items item=i}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}" {assign var="moreContextBefore" value=", 'SP.addItem'"}{include file="sub/contextmenu.tpl"}>
                <td align="center">
                    {if $PLAYLIST.id == $i.id}
                        <b>{$i.title|truncate:30}</b>
                    {else}
                        {$i.title|truncate:30}
                    {/if}
                </td>
                <td align="center">{$i.creator}</td>
                <td align="center">{$i.duration}</td>
                <td align="center">{$i.type}</td>
            </tr>
        {/foreach}
        <tr>
            <td>
                {if $_results.prev}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev', 'pager')">backward</a>{/if}
            </td>
            <td>
                Count:&nbsp;{$_results.cnt}

                Page:&nbsp;&nbsp;{$_results.page+1}

                Range:&nbsp;{$_criteria.offset+1}-{if ($_criteria.offset+$_criteria.limit)>$_results.cnt}{$_results.cnt}{else}{$_criteria.offset+$_criteria.limit}{/if}
            </td>
            <td>
                go:
                {foreach from=$_results.pagination item=p key=k}
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}', 'pager')">{$p}</a>
                {/foreach}
            </td>
            <td align="right">
                {if $_results.next}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next', 'pager')">forward</a>{/if}
            </td>
        </tr>
        <tr><td colspan="4">
    </table>
{else}
    ##No match found.##
{/if}

</div>

*}
