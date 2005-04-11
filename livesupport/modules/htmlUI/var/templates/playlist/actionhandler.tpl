onMouseover="showAlt('Some info about file ID {$i.id}')"
onMouseout="hideAlt()"
onClick="return contextmenu('{$i.attrs.id}', {if $i.type|lower == "audioclip"}'listen', '{$i.gunid}', {/if} 'PL.removeItem')"
