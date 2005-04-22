{include file="popup/header.tpl"}

{if $SCHEDULER->copyPlFromSP()}
    {assign var="dynform" value=$SCHEDULER->getScheduleForm()}
    <table height="100%" width="100%">
        <tr>
            <td style="border: 0">
                <center>
                    <table width="100%" height="100%">
                        <tr><td style="border: 0">
                            {include file="sub/dynForm_plain.tpl}
                        </td></tr>
                    </table>
                </center>
            </td>
        </tr>
    </table>
{else}
    <center>
    ##You need to have at least one non-open playlist on ScratchPad to schedule it.##
    </center>
{/if}



</body>
</html>
