{include file="sub/mouseover.tpl"}
onClick="hidealttextnow(); return contextmenu('{$i.attrs.id}', {if $i.type|lower == "audioclip"}'listen', '{$i.gunid}', {/if} 'PL.removeItem')"
