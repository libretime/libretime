<div id="searchres">
<center>
{if $_results.cnt > 0}
    <table border="0" width="50%">
        <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=title', 'order');">{tra 0=Title}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=creator', 'order');">{tra 0=Creator}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=extent', 'order');">{tra 0=Duration}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=type', 'order');">{tra 0=Type}</a></td>
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
    No match found.
{/if}
</center>
</div>

