{UIBROWSER->getMDataArr id=$i.id assign="_metaarr"}

onMouseover = "showalttext('<div style=&quot;font-size: 120%; font-weight: bold&quot;>##{$i.type|lower|capitalize}##: {$_metaarr.metadata.Title} {if $PL->isUSedBy($i.id) != false}##(used by {$PL->isUSedBy($i.id)})##{/if}</div>{foreach from=$_metaarr.metadata key=_key item=_item}{if $_key != 'Title'}{$_key}: {$_item}<br>{/if}{/foreach}')"
onMouseout  = "hidealttext()"

{assign var="_metaarr" value=null}
