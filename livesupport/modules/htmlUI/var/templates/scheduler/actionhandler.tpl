{UIBROWSER->getMDataArr id=$i.plid assign="_metaarr"}

onMouseover="showalttext('{foreach from=$_metaarr.metadata key=_key item=_item}{$_key}: {$_item}<br>{/foreach}')"   *}
onMouseout="hidealttext()"
onClick="return contextmenu('scheduleId={$i.scheduleid}', 'SCHEDULER.removeItem')"

{assign var="_metaarr" value=NULL}
