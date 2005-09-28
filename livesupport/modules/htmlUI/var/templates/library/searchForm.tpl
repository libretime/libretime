{include file="library/searchForm.js.tpl"}
    {foreach from=$searchForm item=dynform}
        {include file="sub/dynForm_plain.tpl"}
    <!-- end search -->
    {/foreach}