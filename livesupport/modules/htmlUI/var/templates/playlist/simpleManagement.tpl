{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<center>
<form name="PL">
<table border="0">
<tr><th colspan="4">Simple Playlist Management</th></tr>

{PL->get assign='PL'}
{PL->reportLookedPL assign="_looked"}

{if is_array($PL)}          {* already activated Playlist *}
    {include file="playlist/editor.tpl"}
{else}                      {* no active Playlist *}

    <tr>
        <td colspan="4">
        {if $_looked}
            <input type="button" value="Unlook crashed Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.unlook')">
        {else}
            <input type="button" value="New empty Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.create')">
        {/if}
        </td>
    </tr>
{/if}

</table>
</form>
</div>
</div>
