{*Smarty template*}

<div id="scratchpad">
<center><b>ScratchPad</b></center>

{if is_array($sp)}
    <table>
    <tr><th></th><th>{tra 0=Name}</th><th>{tra 0=Duration}</th><th>{tra 0=Type}</th></tr>
    {foreach from=$sp item=i}
        <tr>
            <td><input type="checkbox" name="spid[{$row.id}]"></td>
            <td>{$i.name}</td>
            <td>{$i.duration}</td>
            <td>{$i.type} </td>
        </tr>
    {/foreach}
    </table>
{/if}
</div>
