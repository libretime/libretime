{*Smarty template*}

<div id="scratchpad">
<center><b>ScratchPad</b>

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
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}"
                    onMouseOver="highlight()" onMouseOut="darklight()"
                    onContextmenu="return menu('{$i.id}'
                        {if $i.type == ('audioclip' || 'webstream')}
                            ,'PL.addItem', 'PL.newUsingItem', 'SP.removeItem', 'delete'
                        {/if}
                        )"
                >
                    <td><input type="checkbox" name="{$i.id}"></td>
                    <td>{$i.title}</a></td>
                    <td>{$i.duration}</td>
                    <td>{$i.type} </td>
                    <th><a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.removeItem&id={$i.id}', 'SP')">X</th>
                </tr>
            {/foreach}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <td><input type="checkbox" name="all" onClick="SP_switchAll()"></th>
                <td align="center" colspan="2"><a href="#" onClick="SP_submit()">[Remove Selected]</a></th>
                <td align="center" colspan="2"><a href="#" onClick="SP_clearAll()">[Clear]</a></th>
            </tr>
        </table>
    </form>
{/if}
</div>
</center>
