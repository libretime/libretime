<div class="content">

{if $PL_editMetaData}
        {include file="playlist/metadata.tpl"}
{elseif is_array($PL->get())}           {* already activated Playlist *}
    {include file="playlist/editor.tpl"}
{else}                                  {* no active Playlist *}

<div class="container_elements" style="width: 607px;">
<h1>##Playlist Editor##</h1>
    <p>&nbsp;</p>
    {if $PL->reportLookedPL()}
        <input type="button" value="##Open last Playlist##" onClick="hpopup('{$UI_HANDLER}?act=PL.unlook')">
    {else}
        <input type="button" value="##New empty Playlist##" onClick="hpopup('{$UI_HANDLER}?act=PL.create')">
    {/if}
</div>
{/if}
</div>