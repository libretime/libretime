{SEARCH->getResult assign=_results}

{if $_results.cnt > 0}
    <table border="0" width="50%">
        {foreach from=$_results.items item=i}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}" {assign var="moreContextBefore" value=", 'SP.addItem'"}{include file="sub/contextmenu.tpl"}>
                <td align="center">
                    {if $PLAYLIST.id == $i.id}
                        <b>{$i.title|truncate:30}</b>
                    {else}
                        {$i.title|truncate:30}
                    {/if}
                </td>
                <td align="center">{$i.type}</td>
                <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.uploadPlaylistMethod&gunid={$i.gunid}')">{$i.gunid}</td>
            </tr>
        {/foreach}
    </table>
{else}
    No match found.
{/if}
