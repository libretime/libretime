{PL->getActiveId assign=_PL_activeId}

<div id="scratchpad">
<center><b>%%ScratchPad%%</b>
{if is_array($SCRATCHPAD)}
    <form name="SP">
        <input type="hidden" name="act">
        <table>
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <th></th>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');">[{tra 0=Title}]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=duration', 'order');">[{tra 0=Duration}]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=type', 'order');">[{tra 0=Type}]</a></td>
                <td align="center">Remove</td>
            </tr>

            {foreach from=$SCRATCHPAD item=i}
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}" {assign var="moreContextBefore" value=", 'SP.removeItem'"} {include file="sub/contextmenu.tpl"}>
                    <td><input type="checkbox" name="{$i.id}"></td>
                    <td>
                        {if $_PL_activeId == $i.id}
                            <b>{$i.title|truncate:30}</b>
                        {else}
                            {$i.title|truncate:30}
                        {/if}
                    </td>
                    <td>{$i.duration}</td>
                    <td>{$i.type} </td>
                    <th><a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.removeItem&id={$i.id}', 'SP')">X</th>
                </tr>
            {/foreach}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <td><input type="checkbox" name="all" onClick="form_switchAll('SP')"></th>
                <td align="center" colspan="2">
                    <select>
                        <option selected>Multiple Action:</option>
                        <option onClick="form_submit('SP', 'SP.removeItem')">Remove</option>
                        {if $_PL_activeId}
                            <option onClick="form_submit('SP', 'PL.addItem')">Add to Playlist</option>
                        {else}
                            <option onClick="form_submit('SP', 'PL.create')">New Playlist using Item</option>
                        {/if}
                    </select>
                </th>
                <td align="center" colspan="2"><a href="#" onClick="form_clearAll('SP', 'SP.removeItem')">[Clear]</a></th>
            </tr>
        </table>
    </form>
{/if}
</div>
</center>
