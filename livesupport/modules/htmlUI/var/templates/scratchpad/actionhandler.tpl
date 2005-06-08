onClick="return contextmenu('{$i.id}'
    , 'SP.removeItem'

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
            {elseif $PL->isAvailable($i.id) == true}
                , 'SCHEDULER.addPL', 'PL.addItem', 'PL.activate', 'delete'
            {/if}
        {elseif  $PL->isAvailable($i.id) == true}
            , 'SCHEDULER.addPL', 'PL.activate', 'PL.create', 'delete'
        {/if}
    {/if}
)"