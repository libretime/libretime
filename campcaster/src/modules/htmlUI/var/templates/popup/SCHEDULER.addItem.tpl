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

function SCHEDULE_snap2Next()
{
{/literal}
    // Get the absolute "next item" time, meaning how many milliseconds from
    // the beginning of the day that the next item starts.
    var nextItemTime = new Date("january 01, 1970 {$SCHEDULER->scheduleNext.hour}:{$SCHEDULER->scheduleNext.minute}:{$SCHEDULER->scheduleNext.second}");

    // Get the absolute duration of the playlist in Date format (milliseconds)
    var duration = new Date("january 01, 1970 " + SCHEDULE_selectedDuration());

    // Get the date of the "next item".
    var beginDate = new Date({$SCHEDULER->scheduleNext.year},
                             {$SCHEDULER->scheduleNext.month},
                             {$SCHEDULER->scheduleNext.day});

    // Calculate the final time by starting with the "next item" date (midnight),
    // add in milliseconds to the starting time of the "next item",
    // subtract the duration of the selected playlist.
    beginDate.setTime(beginDate.getTime() + nextItemTime.getTime() - duration.getTime());
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
