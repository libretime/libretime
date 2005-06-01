<script language="javascript">
{literal}
function twoDigit(i) {
    i = Math.round(i);
    if(i < 10) i = "0" + i;

    return i;
}


function pre0_myClock(y, m, d, h, i ,s, interval) {
    pre0_clock = new Array();
    pre0_clock['interval']  = interval;
    pre0_clock['time']      = new Date(y, m, d, h, i ,s);
    pre0_clock['run']       = setInterval("pre0_incClock();", pre0_clock['interval']);
}

function pre0_incClock() {
    pre0_clock['time'].setTime(pre0_clock['time'].getTime() + pre0_clock['interval']);
    document.getElementById("servertime").innerHTML = twoDigit(pre0_clock['time'].getHours()) + ":" + twoDigit(pre0_clock['time'].getMinutes()) + ":" + twoDigit(pre0_clock['time'].getSeconds());
}


function elapsed_myClock(y, m, d, h, i, s, interval) {
    elapsed_clock = new Array();
    elapsed_clock['interval']  = interval;
    elapsed_clock['time']      = new Date(y, m, d, h, i, s);
    elapsed_clock['run']       = setInterval("elapsed_incClock();", elapsed_clock['interval']);
}

function elapsed_incClock() {
    elapsed_clock['time'].setTime(elapsed_clock['time'].getTime() + elapsed_clock['interval']);
    document.getElementById("nowplaying_elapsed").innerHTML = twoDigit(elapsed_clock['time'].getHours()) + ":" + twoDigit(elapsed_clock['time'].getMinutes()) + ":" + twoDigit(elapsed_clock['time'].getSeconds());
}


function remaining_myClock(y, m, d, h, i, s, interval) {
    remaining_clock = new Array();
    remaining_clock['interval']  = interval;
    remaining_clock['time']      = new Date(y, m, d, h, i, s);
    remaining_clock['run']       = setInterval("remaining_incClock();", remaining_clock['interval']);
}

function remaining_incClock() {
    remaining_clock['time'].setTime(remaining_clock['time'].getTime() - remaining_clock['interval']);
    document.getElementById("nowplaying_remaining").innerHTML = twoDigit(remaining_clock['time'].getHours()) + ":" + twoDigit(remaining_clock['time'].getMinutes()) + ":" + twoDigit(remaining_clock['time'].getSeconds());

    if (remaining_clock['time'].getHours() == 0 && remaining_clock['time'].getMinutes() == 0 && remaining_clock['time'].getSeconds() == 0) {
        clearInterval(elapsed_clock['run']);
        clearInterval(remaining_clock['run']);
        document.getElementById("onair").innerHTML       = '<img src="img/el_offair.gif" alt="off air">';
        document.getElementById("whatplaying").innerHTML = '';
    }
}

{/literal}

pre0_myClock({$smarty.now|date_format:"%Y, %m, %d, %H, %M, %S"}, 1000);

{if (is_array($_nowplaying.duration))}
    elapsed_myClock  ({$smarty.now|date_format:"%Y, %m, %d"}, {$_nowplaying.elapsed.h},   {$_nowplaying.elapsed.m},   {$_nowplaying.elapsed.s|truncate:2:""},   100);
    remaining_myClock({$smarty.now|date_format:"%Y, %m, %d"}, {$_nowplaying.remaining.h}, {$_nowplaying.remaining.m}, {$_nowplaying.remaining.s|truncate:2:""}, 100);
{/if}

</script>


