<div id="masterpanel">

{include file="statusbar.tpl"}

{if $showMenuTop}
    {include file="menu_top.tpl"}
{/if}

{if $SCRATCHPAD}
    {include file="scratchPad.tpl"}
{/if}

{if $simpleSearchForm}
    {include file="library/simpleSearchForm.tpl"}
{/if}

{if $structure}
    {include file="file/path.tpl"}
{/if}

{if $fileList}
    {include file="file/list.tpl"}
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

{if $editItem}
    {include file="file/edit.tpl"}
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

{if $showScheduler}
    {include file="scheduler/main.tpl"}
{/if}

</div>

