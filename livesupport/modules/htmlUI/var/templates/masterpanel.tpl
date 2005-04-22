{include file="statusbar.tpl"}

{if $showMenuTop}
    {include file="menu.tpl"}
{/if}


{if $USER.userid}    {* somebody logged in? *}

    {*
    {if $structure}
        {include file="file/path.tpl"}
    {/if}
    *}

    {if $showScheduler}
    <table  style="margin:0px;padding:0px;" border="0"><tr><td valign="top" style="margin:0px;padding:0px;border:0">
        <div class="content">
        {include file="scheduler/calendar.tpl"}
        {if $SCRATCHPAD}
            {include file="scratchpad/main.tpl"}
        {/if}
        </div>
        </td><td valign="top" style="margin:0px;padding:0px;border:0">
        {include file="scheduler/main.tpl"}
        </td></tr></table>
    {else}

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
            {include file="file/data.tpl"}
        {/if}

        {if $editItem}
            {include file="file/edit.tpl"}
        {/if}

        {if $changeStationPrefs}
            {include file="stationprefs.tpl"}
        {/if}

        {if $PL_simpleManagement}
            {include file="playlist/main.tpl"}
        {/if}

        <div class="content">
        {if $simpleSearchForm}
            {include file="library/simpleSearchForm.tpl"}
        {/if}

        {if $SCRATCHPAD}
            {include file="scratchpad/main.tpl"}
        {/if}
        </div>
    {/if}

{/if}
