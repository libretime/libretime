onMouseOver="highlight()"
onMouseOut="darklight()"
onContextmenu="return menu('{$i.id}'
    {$moreContextBefore}
    {if ($i.type == 'audioclip' || $i.type == 'webstream')}
        {if $PLid}
            ,'PL.addItem',
        {else}
            'PL.newUsingItem',
        {/if}
        'delete'
    {/if}

    {if ($i.type == 'playlist')}
        {if $PLid}
            {if $PLid == $i.id}
                ,'PL.release'
            {else}
                ,'PL.addItem', 'delete'
            {/if}
        {else}
            ,'PL.activate', 'PL.newUsingItem', 'delete'
        {/if}
    {/if}
    {$moreContextAfter}
)"


{assign var="moreContextBefore" value=""}
{assign var="moreContextAfter" value=""}
