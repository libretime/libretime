{*Smarty template*}
<div id="masterpanel">

{include file="statusbar.tpl"}

{if $showMenuTop}
    {include file="menu_top.tpl"}
{/if}

{if $ScratchPad}
    {include file="ScratchPad.tpl"}
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

{if $permissions}
    {include file="permissions.tpl"}
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

{if $editMetaData}
    {include file="editMetaData.tpl"}
{/if}

{if $systemPrefs}
    {include file="systemPrefs.tpl"}
{/if}

{if $uploadform}
    {include file="uploadform.tpl"}
{/if}

</div>

