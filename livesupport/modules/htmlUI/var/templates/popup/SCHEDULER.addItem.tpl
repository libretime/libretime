{include file="popup/header.tpl"}

<center>
{if $SCHEDULER->_copyPlFromSP()}
    {assign var="dynform" value=$SCHEDULER->getScheduleForm()}
    {include file="sub/dynForm_plain.tpl}
{else}
    ##You need to have at least one inactive playlist on ScratchPad to schedule it.##
{/if}
</center>


</body>
</html>
