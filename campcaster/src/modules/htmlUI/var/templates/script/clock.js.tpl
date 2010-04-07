<script language="javascript">

{literal}

function myClock(y, m, d, h, i, s, interval) {
    clock = new Array();
    clock['interval']  = interval;
    clock['time']      = new Date(y, m, d, h, i ,s);
    clock['run']       = setInterval("incClock();", clock['interval']);
}

function incClock() {
    clock['time'].setTime(clock['time'].getTime() + clock['interval']);
    document.getElementById("servertime").innerHTML = twoDigit(clock['time'].getHours()) + ":" + twoDigit(clock['time'].getMinutes()) + ":" + twoDigit(clock['time'].getSeconds());
}

{/literal}

myClock({$_now|date_format:"%Y"|string_format:"%d"}, {$_now|date_format:"%m"|string_format:"%d"}, {$_now|date_format:"%d"|string_format:"%d"},
             {$_now|date_format:"%H"|string_format:"%d"}, {$_now|date_format:"%M"|string_format:"%d"}, {$_now|date_format:"%S"|string_format:"%d"},
             1000);

</script>      
