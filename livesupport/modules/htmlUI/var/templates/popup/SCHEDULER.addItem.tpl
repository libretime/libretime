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

<script type="text/javascript">
{literal}
function SCHEDULE_submit()
{
    document.forms["schedule"].elements["playlist"].value = SCHEDULE_selectedGunid();
    document.forms["schedule"].submit();
}

function SCHEDULE_snap2Hour()
{
{/literal}
    document.forms["schedule"].elements["time[H]"].value = "{$SCHEDULER->scheduleAt.hour|string_format:'%d'}";
    document.forms["schedule"].elements["time[i]"].value = "0";
    document.forms["schedule"].elements["time[s]"].value = "0";
{literal}
}

function SCHEDULE_snap2Prev()
{
{/literal}
    document.forms["schedule"].elements["time[H]"].value = "{$SCHEDULER->schedulePrev.hour|string_format:'%d'}";
    document.forms["schedule"].elements["time[i]"].value = "{$SCHEDULER->schedulePrev.minute|string_format:'%d'}";
    document.forms["schedule"].elements["time[s]"].value = "{$SCHEDULER->schedulePrev.second|string_format:'%d'}";
{literal}
}

function SCHEDULE_snap2Next()
{
{/literal}
    var beginD = new Date();
    var colon = ":";
    var duration  = SCHEDULE_selectedDuration();
    var nextD     = new Date("january 01, 1970 {$SCHEDULER->scheduleNext.hour}:{$SCHEDULER->scheduleNext.minute}:{$SCHEDULER->scheduleNext.second}");
    var durationD = new Date("january 01, 1970 " + SCHEDULE_selectedDuration());
    //alert(durationD.getTime());
    beginD.setTime(nextD.getTime() - durationD.getTime() - 3600000);

    //alert(nextD.toLocaleString());
    //alert(durationD.toLocaleString());
    //alert(beginD.toLocaleString());

    document.forms["schedule"].elements["time[H]"].value = beginD.getHours();
    document.forms["schedule"].elements["time[i]"].value = beginD.getMinutes();
    document.forms["schedule"].elements["time[s]"].value = beginD.getSeconds();
{literal}
}

function SCHEDULE_selectedDuration()
{
    var arr = document.forms["schedule"].elements["gunid_duration"].value.split("|");
    return arr[1].slice(0, 8);
}

function SCHEDULE_selectedGunid()
{
    var arr = document.forms["schedule"].elements["gunid_duration"].value.split("|");
    return arr[0];
}
{/literal}
</script>



</body>
</html>
