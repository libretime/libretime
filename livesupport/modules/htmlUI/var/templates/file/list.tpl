<div class="content">
<!-- start file -->
<div class="container_elements" style="width: 607px;">
<h1>##File Management##</h1>

{if $showTree}
    {include file="file/tree.tpl"}
{/if}

{if $showObjects}
    {include file="file/objects.tpl"}
{/if}

{if $permissions}
    {include file="file/permissions.tpl"}
{/if}

</div>
<!-- end file -->
</div>
