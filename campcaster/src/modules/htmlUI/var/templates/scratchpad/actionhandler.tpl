onClick="return contextmenu('{$i.id}'
    , 'SP.removeItem'
    
    {if $i.type|lower == 'audioclip'}
        , 'listen', '{$i.gunid}'
        
        {if $_PL_activeId}
            , 'PL.addItem'
        {else}
            , 'PL.create' 
        {/if}
        
        , 'edit'
        , 'delete'
    {/if}

    {if $i.type|lower == 'webstream'}
        , 'listen', '{$i.gunid}'
        
        {if $_PL_activeId}
            {if $i.duration == '00:00:00.000000'}
                , 'PL.addStream'
            {else}
                , 'PL.addItem'
                {/if}    
        {else}
            , 'PL.create'
        {/if}
        
        , 'edit'
        , 'delete'
    {/if}

    {if $i.type|lower == 'playlist'}
        {if $_PL_activeId}
            {if $_PL_activeId == $i.id}
                , 'PL.release'
            {elseif $PL->isAvailable($i.id) == true}
                , 'SCHEDULER.addPL'
                , 'PL.addItem'
                , 'PL.activate'
                , 'PL.delete'
            {/if}
        {elseif  $PL->isAvailable($i.id) == true}
            , 'SCHEDULER.addPL'
            , 'PL.activate'
            , 'PL.create'
            , 'delete'
            , 'PL.export'
        {/if}
    {/if}
    
    , 'TR.upload2Hub'
)"