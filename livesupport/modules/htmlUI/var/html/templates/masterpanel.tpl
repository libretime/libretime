{*Smarty template*}
<div id="masterpanel">

{include file="statusbar.tpl"}

{if $showMenuTop}
    {include file="menu_top.tpl"}
{/if}

{if $showPath}
    {include file="path.tpl"}
{/if}

{if $showTree}
    {include file="tree.tpl"}
{/if}

{if $showObjects}
    {include file="objects.tpl"}
{/if}

{if $showPermissions}
    {include file="permissions.tpl"}
{/if}

{if $showNewFileForm}
    {include file="newfileform.tpl"}
{/if}

{if ($showSearchForm || $showSearchRes)}
    {include file="search.tpl"}
{/if}

{if $showSubjects}
    {include file="subjects.tpl"}
{/if}

{if $showFile}
    {include file="filedata.tpl"}
{/if}

{if $showMetaDataForm}
    {include file="metadataform.tpl"}
{/if}

{if $showSystemPrefs}
    {include file="systemPrefs.tpl"}
{/if}

{if $showUploadForm}
    {include file="uploadform.tpl"}
{/if}

</div>

