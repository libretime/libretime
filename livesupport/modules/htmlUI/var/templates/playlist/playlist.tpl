{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<div align="center"><b>Playlist</b></div>
<br>

{foreach from=$playlist item=pl}
    {$pl.id}
    <br>
{/foreach}


</div>
