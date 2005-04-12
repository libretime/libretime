{UIBROWSER->getMDataArr id=$i.id assign="_metaarr"}

onMouseover="showalttext('{foreach from=$_metaarr.metadata key=_key item=_item}{$_key}: {$_item}<br>{/foreach}')"
onMouseout="hidealttext()"
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

{assign var="_metaarr" value=NULL} 
