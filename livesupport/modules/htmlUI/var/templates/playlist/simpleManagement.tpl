{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<center>
<form name="PL">
<table border="0">
<tr><th colspan="4">Simple Playlist Management</th></tr>

{PL->get assign='PL'}
{if is_array($PL)}          {* already activated Playlist *}
    {include file="playlist/editor.tpl"}
{else}                      {* no active Playlist *}
    <tr><td colspan="4">No active Playlist!</td></tr>
    <tr><td colspan="4"><input type="button" value="Create empty Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.create')"></td></tr>
{/if}

</table>
</form>
</div>
</div>
