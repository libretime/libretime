{if !$SCHEDULER->getSchedulerTime()}
    <script language="javascript">
        alert("{$SCHEDULER->getErrorMsg()|escape:"quotes"}");
    </script>
    {$SCHEDULER->setErrorMsg(false)}
{/if}

<!-- start navigation tabs -->
<div id="tabnavsmall">
    <ul>
    <li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=day&today=1">##Today##</a></li>
    <li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=day">##Day##</a></li>
    <li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=week">##Week##</a></li>
    <li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=month">##Month##</a></li>
    {* if Alib::CheckPerm($SUBJECTS->Base->userid, 'schedulerStatus') *}
        <li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=status">##Status##</a></li>
    {* /if *}
    {* if $SUBJECTS->isMemberOf('Backup') *}
    {*<li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=export">##Export##</a></li>*}
    {* /if *}
    {* if $SUBJECTS->isMemberOf('Restore') *}
    {*<li><a href="{$UI_HANDLER}?act=SCHEDULER.set&view=import">##Import##</a></li>*}
    {* /if *}
    </ul>
</div>
<!-- end navigation tabs -->

{assign var="view" value=$SCHEDULER->curr.view}
{include file="scheduler/$view.tpl"}
