{include file="library/searchForm.js.tpl"}
        
<div id="searchform">
{include file="sub/x.tpl"}
  <center>
    {foreach from=$searchForm item=dynform}
        {include file="sub/dynForm_plain.tpl"}
    {/foreach}
  </center>
</div>

