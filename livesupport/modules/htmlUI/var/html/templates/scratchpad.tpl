{*Smarty template*}

<div id="scratchpad">
<center><b>ScratchPad</b></center>

{if is_array($sp)}
    <table>
    <tr><th></th><th>{tra 0=Name}</th><th>{tra 0=Duration}</th><th>{tra 0=Type}</th><th>Del</th></tr>
    {foreach from=$sp item=i}
        <tr>
            <td><input type="checkbox" name="spid[{$i.id}]"></td>
            <td>{$i.title}</td>
            <td>{$i.duration}</td>
            <td>{$i.type} </td>
            <th><a href="#" onclick="popup('{$UI_HANDLER}?act=remFromSP&id={$i.id}', 'remFromSP', 1, 1)">X</th>
        </tr>
    {/foreach}
    <tr><td></td><td colspan="2">[Edit]</td><td colspan="2">[Delete]</td></tr>
    </table>
{/if}
</div>
