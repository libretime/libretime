<script language="javascript">
{literal}
function twoDigit(i) {
    i = Math.round(i);
    if(i < 10) i = "0" + i;

    return i;
}


function pre0_myClock(y, m, d, h, i, s, interval) {
    pre0_clock = new Array();
    pre0_clock['interval']  = interval;
    pre0_clock['time']      = new Date(y, m, d, h, i ,s);
    pre0_clock['run']       = setInterval("pre0_incClock();", pre0_clock['interval']);
}

function pre0_incClock() {
    pre0_clock['time'].setTime(pre0_clock['time'].getTime() + pre0_clock['interval']);
    document.getElementById("statusbar_servertime").innerHTML = twoDigit(pre0_clock['time'].getHours()) + ":" + twoDigit(pre0_clock['time'].getMinutes()) + ":" + twoDigit(pre0_clock['time'].getSeconds());
}




function myClock(eh, ei, es, dh, di, ds, next, interval) {
    clock = new Array();
    clock['next']       = next;
    clock['interval']   = interval;
    clock['correction'] = new Date();
    //clock['correction'].setTime(0);
    //clock['corr_h'] = clock['correction'].getHours();

    clock['elapsed']    = new Date();
    clock['duration']   = new Date();
    clock['remaining']  = new Date();
    clock['elapsed'].setTime (Date.UTC(1970, 0, 1, eh, ei, es));
    clock['duration'].setTime(Date.UTC(1970, 0, 1, dh, di, ds));
    clock['run']        = setInterval("incClock();", clock['interval']);
}

function incClock() {
    if (clock['remaining'].getTime() <= clock['interval']*2)  {
        stopClock();
        return;
    }

    clock['elapsed'].setTime(clock['elapsed'].getTime() + clock['interval']);
    clock['remaining'].setTime(clock['duration'].getTime() - clock['elapsed'].getTime());

    document.getElementById("statusbar_elapsed").innerHTML   = twoDigit(clock['elapsed'].getUTCHours())   + ":" + twoDigit(clock['elapsed'].getUTCMinutes())   + ":" + twoDigit(clock['elapsed'].getUTCSeconds());
    document.getElementById("statusbar_remaining").innerHTML = twoDigit(clock['remaining'].getUTCHours()) + ":" + twoDigit(clock['remaining'].getUTCMinutes()) + ":" + twoDigit(clock['remaining'].getUTCSeconds());
    document.getElementById("statusbar_scala").style.width   = (100 / clock['duration'].getTime() * clock['elapsed'].getTime()) + "%";
}

function stopClock() {
    clearInterval(clock['run']);

    if (clock['next'] == 0) {
        // just if no next item to play
        document.getElementById("statusbar_indicator").innerHTML    = '<img src="img/el_offair.gif" alt="off air">';
        document.getElementById("statusbar_whatsplaying").innerHTML = "";
    }
}

{/literal}

pre0_myClock({$smarty.now|date_format:"%Y"|string_format:"%d"}, {$smarty.now|date_format:"%m"|string_format:"%d"}, {$smarty.now|date_format:"%d"|string_format:"%d"},
             {$smarty.now|date_format:"%H"|string_format:"%d"}, {$smarty.now|date_format:"%M"|string_format:"%d"}, {$smarty.now|date_format:"%S"|string_format:"%d"},
             1000);

{if (is_array($_nowplaying.duration))}
    myClock  ({$_nowplaying.elapsed.h|string_format:"%d"}, {$_nowplaying.elapsed.m|string_format:"%d"}, {$_nowplaying.elapsed.s|string_format:"%d"},
              {$_nowplaying.duration.h|string_format:"%d"}, {$_nowplaying.duration.m|string_format:"%d"}, {$_nowplaying.duration.s|string_format:"%d"},
              {if is_array($_nextplaying)}1{else}0{/if}, 333);
{/if}

</script>


