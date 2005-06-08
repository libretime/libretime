onClick="return contextmenu('{$i.id}'
    , 'SP.addItem'

    {if $i.type|lower == 'audioclip'}
        , 'listen', '{$i.gunid}'
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create'
        {/if}
        , 'edit', 'delete'
    {/if}

    {if $i.type|lower == 'webstream'}
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create'
        {/if}
        , 'edit', 'delete'
    {/if}

    {if $i.type|lower == 'playlist'}
        {if $_PL_activeId}
            {if $_PL_activeId == $i.id}
                , 'PL.release'
            {else}
                , 'SCHEDULER.addPL', 'PL.addItem', 'delete'
            {/if}
        {else}
            , 'SCHEDULER.addPL', 'PL.activate', 'PL.create', 'delete'
        {/if}
    {/if}
)"

