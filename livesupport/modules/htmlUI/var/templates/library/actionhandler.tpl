onMouseover="showAlt('Some info about file ID {$i.id}')" 
onMouseout="hideAlt()"
onClick="return contextmenu('{$i.id}'
    , 'SP.addItem'

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
)"
