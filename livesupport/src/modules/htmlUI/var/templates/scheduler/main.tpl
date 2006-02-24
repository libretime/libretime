{if $SCHEDULER->getErrorMsg()}
    <script language="javascript">
        alert("{$SCHEDULER->getErrorMsg()|escape:"quotes"}");
    </script>
    {$SCHEDULER->setErrorMsg(false)}
{/if}

{assign var="view" value=$SCHEDULER->curr.view}
{include file="scheduler/$view.tpl"}
