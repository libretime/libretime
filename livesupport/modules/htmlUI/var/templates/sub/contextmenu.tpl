{assign var="_PL_activeId" value=$PL->getActiveId()}

onMouseOver="highlight()"
onMouseOut="darklight()"
onContextmenu="return menu('{$i.id}'
    {$moreContextBefore}
    {if ($i.type == 'audioclip' || $i.type == 'webstream')}
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create'
        {/if}
        , 'edit', 'delete'
    {/if}

    {if ($i.type == 'playlist')}
        {if $_PL_activeId}
            {if $_PL_activeId == $i.id}
                , 'PL.release'
            {else}
                , 'PL.addItem', 'delete'
            {/if}
        {else}
            , 'PL.activate', 'PL.create', 'edit', 'delete'
        {/if}
    {/if}

    {if ($i.type == 'Folder')}
        , 'fileList', 'delete'
    {/if}
    {$moreContextAfter}
)"


{assign var="moreContextBefore" value=""}
{assign var="moreContextAfter" value=""}
