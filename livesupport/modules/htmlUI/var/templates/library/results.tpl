{*Smarty template*}

<div id="searchres">
<center>
{if $searchResults.count > 0}
    <table border="0" width="50%">
        <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=title', 'order');">{tra 0=Title}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=creator', 'order');">{tra 0=Creator}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=extent', 'order');">{tra 0=Duration}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.reOrder&by=type', 'order');">{tra 0=Type}</a></td>
        </tr>
        {foreach from=$searchResults.items item=i}
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
                {if $searchResults.prev}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=prev', 'pager')">back</a>{/if}
            </td>
            <td>count: {$searchResults.count}</td>
            <td>
                go:
                {foreach from=$searchResults.pages item=p key=k}
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page={$k}', 'pager')">{$p}</a>
                {/foreach}
            </td>
            <td align="right">
                {if $searchResults.next}<a href="#" onClick="hpopup('{$UI_HANDLER}?act={$_act_prefix}.setOffset&page=next', 'pager')">forward</a>{/if}
            </td>
        </tr>
        <tr><td colspan="4">
    </table>
{else}
    No match found.
{/if}
</center>
</div>

