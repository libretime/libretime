{UIBROWSER->getMDataArr id=$i.id assign="_metaarr"}

onMouseover="showalttext('{foreach from=$_metaarr.metadata key=_key item=_item}{$_key}: {$_item}<br>{/foreach}')"
onMouseout="hidealttext()"
onClick="return contextmenu('{$i.attrs.id}', {if $i.type|lower == "audioclip"}'listen', '{$i.gunid}', {/if} 'PL.removeItem')"

{assign var="_metaarr" value=NULL}
