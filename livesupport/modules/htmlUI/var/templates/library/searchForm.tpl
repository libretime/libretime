{include file="library/searchForm.js.tpl"}
    {foreach from=$searchForm item=dynform}
    <div class="container_elements" style="width: 607px;">
    <h1>##Search##</h1>
        {include file="sub/dynForm_plain.tpl"}
    </div>
    {/foreach}