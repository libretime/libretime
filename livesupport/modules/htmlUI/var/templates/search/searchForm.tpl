{*Smarty template*}

{include file="script/search.js.tpl"}


{literal}
<style type="text/css">
    .dynformelement {
        width : 800px;
    }
</style>
{/literal}
<div id="searchform">
{include file="sub/x.tpl"}
  <center>
    {foreach from=$searchForm item=dynform}
        {include file="sub/dynForm_plain.tpl"}
    {/foreach}
  </center>
</div>

