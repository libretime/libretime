{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<div align="center"><b>Simple Playlist Management</b></div>
<br>

{if is_array($PLAYLIST) && count($PLAYLIST)}      {* already activated Playlist *}
    {foreach from=$PLAYLIST item=pl}
        {$pl}
        <br>
    {/foreach}
{else}                      {* no active Playlist *}
    No Playlist
{/if}


</div>
