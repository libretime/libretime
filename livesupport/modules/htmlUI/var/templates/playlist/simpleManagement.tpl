<div class="standardFrame">
{include file="sub/x.tpl"}

<center>
<h4>Simple Playlist Management</h4>

{if is_array($PL->get())}           {* already activated Playlist *}
    {if $PL_editMetaData}
        {include file="playlist/metadata.tpl"}
    {else}
        {include file="playlist/editor.tpl"}
    {/if}
{else}                              {* no active Playlist *}
    {if $PL->reportLookedPL()}
        <input type="button" value="Unlook crashed Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.unlook')">
    {else}
        <input type="button" value="New empty Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.create')">
    {/if}
{/if}

</center>
</div>
