{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<div align="center"><b>Simple Playlist Management</b></div>
<br>

{PL->get assign='PL'}
{if is_array($PL)}      {* already activated Playlist *}
    active Playlist: {$PL.children.0.children.0.content}
    <br>
    {foreach from=$PL item=pl}
        {$pl}
        <br>
    {/foreach}
{else}                      {* no active Playlist *}
    No active Playlist!
    <br>
    <input type="button" value="Make new Playlist" onClick="location.href='{$UI_BRWOSER}?act=PL.simpleManagement&createNew=1'">
{/if}


</div>
