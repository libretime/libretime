{include file="scheduler/mouseOver.js.tpl"}

{assign var="view" value=$SCHEDULER->curr.view}
{include file="scheduler/$view.tpl"}
