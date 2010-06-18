{include file="popup/header.tpl"}

{if $SCHEDULER->getPlaylistToSchedule($_REQUEST.playlistId)}
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
    document.forms["schedule"].elements["time[H]"].value = "{$SCHEDULER->scheduleAtTime.hour|string_format:'%d'}";
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

/**
 * Take a string as input in the form "HH:MM:SS" and return
 * the number of milliseconds this represents from the time
 * "00:00:00".
 *
 * @param string p_timeString
 * @return int
 */
function SCHEDULE_timeToMilliseconds(p_timeString)
{
{/literal}
    if (p_timeString.length != 8) return 0;

    var arr = p_timeString.split(":");
    if (arr.length != 3) return 0;

    // hours
    milliseconds = arr[0]*60*60*1000;
    // minutes
    milliseconds += arr[1]*60*1000;
    // seconds
    milliseconds += arr[2]*1000;
    return milliseconds;
{literal}
}

function SCHEDULE_snap2Next()
{
{/literal}
    // Get the number of milliseconds from the beginning of the day
    // that the next item in the schedule starts.
    var nextItemTime = SCHEDULE_timeToMilliseconds("{$SCHEDULER->scheduleNext.hour}:{$SCHEDULER->scheduleNext.minute}:{$SCHEDULER->scheduleNext.second}");

    // Get the duration of the item to be scheduled in milliseconds.
    var duration = SCHEDULE_timeToMilliseconds(SCHEDULE_selectedDuration());

    // Get the date of the "next item" (time is set to midnight).
    var beginDate = new Date({$SCHEDULER->scheduleNext.year},
                             {$SCHEDULER->scheduleNext.month},
                             {$SCHEDULER->scheduleNext.day});
    //debugBeginDate = ""+beginDate;

    // Calculate the final time by starting with the "next item" date at midnight,
    // add in milliseconds midnight to "next item" start time,
    // subtract the duration of the selected playlist.
    beginDate.setTime(beginDate.getTime() + nextItemTime - duration);
    //alert("next item time: "+nextItemTime+"\n"
    //      +"duration: "+(duration)+"\n"
    //      +"debug begin date: "+debugBeginDate+"\n"
    //      +"begin date: "+beginDate+"\n");

    document.forms["schedule"].elements["time[H]"].value = beginDate.getHours();
    document.forms["schedule"].elements["time[i]"].value = beginDate.getMinutes();
    document.forms["schedule"].elements["time[s]"].value = beginDate.getSeconds();
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
