{assign var="_PL_activeId" value=$PL->getActiveId()}

onContextmenu="return contextmenu('{$i.id}'
    {$moreContextBefore}
    {if $i.type == 'audioclip'}
        , 'listen', '{$i.gunid}'
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create'
        {/if}
        , 'edit', 'delete'
    {/if}

    {if $i.type == 'webstream'}
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create'
        {/if}
        , 'edit', 'delete'
    {/if}

    {if $i.type == 'playlist'}
        {if $_PL_activeId}
            {if $_PL_activeId == $i.id}
                , 'PL.release'
            {else}
                , 'PL.addItem', 'delete'
            {/if}
        {else}
            , 'PL.activate', 'PL.create', 'delete'
        {/if}
    {/if}

    {if ($i.type == 'Folder')}
        , 'fileList', 'delete'
    {/if}
    {$moreContextAfter}
)"


{assign var="moreContextBefore" value=""}
{assign var="moreContextAfter" value=""}
