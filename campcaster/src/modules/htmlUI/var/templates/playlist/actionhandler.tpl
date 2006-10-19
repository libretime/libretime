style="cursor: pointer"
onClick="return contextmenu('{$i.attrs.id}',

    {if $i.type|lower == 'webstream'}
        'listen', '{$i.gunid}',
        {* 'PL.changeItemPlaylength', *}   
    {/if}
    
    {if $i.type|lower == 'audioclip'}
        'listen', '{$i.gunid}',
        {* 'PL.changeItemPlaylength', *}   
    {/if}
        
    'PL.removeItem'
    )"
