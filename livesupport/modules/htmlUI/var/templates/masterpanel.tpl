{*Smarty template*}

<div id="masterpanel">

{include file="statusbar.tpl"}

{if $showMenuTop}
    {include file="menu_top.tpl"}
{/if}

{if $SCRATCHPAD}
    {include file="scratchPad.tpl"}
{/if}

{if $structure}
    {include file="fileBrowse/path.tpl"}
{/if}

{if $fileBrowse}
    {include file="fileBrowse/fileBrowse.tpl"}
{/if}

{if $showLibrary}
    {include file="library/main.tpl"}
{/if}

{if $showSubjects}
    {include file="subjects.tpl"}
{/if}

{if $showFile}
    {include file="filedata.tpl"}
{/if}

{if $uploadform}
    {include file="uploadform.tpl"}
{/if}

{if $editMetaData}
    {include file="editMetaData.tpl"}
{/if}

{if $changeStationPrefs}
    {include file="changeStationPrefs.tpl"}
{/if}

{if $PL_simpleManagement}
    {include file="playlist/simpleManagement.tpl"}
{/if}


</div>

