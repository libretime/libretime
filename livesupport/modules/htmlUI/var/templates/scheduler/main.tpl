<div class="standardFrame">
{include file="sub/x.tpl"}

<input type="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day')" value="Day">
<input type="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=week')" value="Week">
<input type="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=month')" value="Month">
<input type="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=schedule')" value="Schedule">

{include file="scheduler/calendar.tpl"}

{assign var="view" value=$SCHEDULER->curr.view}
{include file="scheduler/$view.tpl"}

</div>
