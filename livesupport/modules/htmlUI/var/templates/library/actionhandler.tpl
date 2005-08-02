onClick="return contextmenu('{$i.id}'
    , 'SP.addItem'

    {if $i.type|lower == 'audioclip'}
        , 'listen', '{$i.gunid}', '##audioclip##'
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create', '{$i.title|truncate:20|escape:'html'}'
        {/if}
        , 'edit',   '##audioclip##'
        , 'delete', '##audioclip##'
    {/if}

    {if $i.type|lower == 'webstream'}
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create', '{$i.title|truncate:20}'
        {/if}
        , 'edit',   '##webstream##'
        , 'delete', '##webstream##'
    {/if}

    {if $i.type|lower == 'playlist'}
        {if $_PL_activeId}
            {if $_PL_activeId == $i.id}
                , 'PL.release'
            {else}
                , 'SCHEDULER.addPL', 'PL.addItem', 'delete'
            {/if}
        {else}
            , 'SCHEDULER.addPL', 'PL.activate'
            , 'PL.create', '{$i.title|truncate:20}'
            , 'delete', '##playlist##'
        {/if}
    {/if}
)"

