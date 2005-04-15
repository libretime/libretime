{UIBROWSER->getMDataArr id=$i.id assign="_metaarr"}
onMouseover="showalttext('<h3>##{$i.type|lower|capitalize}## {if $_PL_activeId == $i.id}##(activated)##{/if}</h3>{foreach from=$_metaarr.metadata key=_key item=_item}{$_key}: {$_item}<br>{/foreach}')"
onMouseout="hidealttext()"
{assign var="_metaarr" value=NULL} 
