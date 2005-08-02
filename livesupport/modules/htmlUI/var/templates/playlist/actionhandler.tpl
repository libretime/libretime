style="cursor: pointer"
onClick="return contextmenu('{$i.attrs.id}',
    {if $i.type|lower == 'audioclip'}
        'listen', '{$i.gunid}', '{$i.type}',
    {/if}
    'PL.removeItem'
    )"
