{$SCHEDULER->copyPlFromSP()}

<form name="schedule_it">
    <select name="gunid">
        {foreach from=$SCHEDULER->playlists item="_pl"}
            <option value="{$_pl.gunid}">{$_pl.title}</option>
        {/foreach}
    </select>
    <input type="hidden" name="sc_last">
    <input value="00" type="text" size="2" name="hour" onClick="sc_act(this)" onChange="sc_checkrange(); sc_twodigits()" onBlur="sc_check_int(this)"> :
    <input value="00" type="text" size="2" name="min"  onClick="sc_act(this)" onChange="sc_checkrange(); sc_twodigits()" onBlur="sc_check_int(this)"> :
    <input value="00" type="text" size="2" name="sec"  onClick="sc_act(this)" onChange="sc_checkrange(); sc_twodigits()" onBlur="sc_check_int(this)">
    <a href="#" onMouseDown="sc_start('-')" onMouseUp="sc_stop()" onMouseOut="sc_stop()">-</a>
    <a href="#" onMouseDown="sc_start('+')" onMouseUp="sc_stop()" onMouseOut="sc_stop()">+</a>

    <br>
    <input type="button" value="schedule" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.uploadPlaylistMethod&gunid='+schedule_it.gunid.value+'&time='+schedule_it.hour.value+':'+schedule_it.min.value+':'+schedule_it.sec.value)">
</form>



<script type="text/javascript">
{literal}
var sc_loop;

function sc_start(direction)
{
    sc_loop = setInterval("sc_change('" + direction + "')", 80);
}

function sc_stop()
{
    clearInterval(sc_loop);
}

function sc_act(element)
{
   document.forms['schedule_it'].elements['sc_last'].value = element.name;
   element.select();
}

function sc_change(direction)
{
    if (document.forms['schedule_it'].elements['sc_last'].value) {
        if (direction == '+') {
            document.forms['schedule_it'].elements[document.forms['schedule_it'].elements['sc_last'].value].value++;
        }

        if (direction == '-') {
            document.forms['schedule_it'].elements[document.forms['schedule_it'].elements['sc_last'].value].value--;
        }

        sc_checkrange();
        sc_twodigits();

        document.forms['schedule_it'].elements[document.forms['schedule_it'].elements['sc_last'].value].select();
    }
}

function sc_checkrange()
{
    // switch out of range
    if (document.forms['schedule_it'].elements['sec'].value  >= 60) {
        document.forms['schedule_it'].elements['sec'].value = 0;
        document.forms['schedule_it'].elements['min'].value++;
    }
    if (document.forms['schedule_it'].elements['min'].value  >= 60) {
        document.forms['schedule_it'].elements['min'].value = 0;
        document.forms['schedule_it'].elements['hour'].value++;
    }
    if (document.forms['schedule_it'].elements['hour'].value >= 24) {
        document.forms['schedule_it'].elements['hour'].value = 0;
    }

    if (document.forms['schedule_it'].elements['sec'].value  < 0) {
        document.forms['schedule_it'].elements['sec'].value = 59;
        document.forms['schedule_it'].elements['min'].value--;
    }
    if (document.forms['schedule_it'].elements['min'].value  < 0) {
        document.forms['schedule_it'].elements['min'].value = 59;
        document.forms['schedule_it'].elements['hour'].value--;
    }
    if (document.forms['schedule_it'].elements['hour'].value < 0) {
        document.forms['schedule_it'].elements['hour'].value = 23;
    }
}

function sc_twodigits()
{
    if (document.forms['schedule_it'].elements['hour'].value < 10) document.forms['schedule_it'].elements['hour'].value = '0' + Math.round(document.forms['schedule_it'].elements['hour'].value);
    if (document.forms['schedule_it'].elements['min'].value  < 10) document.forms['schedule_it'].elements['min'].value  = '0' + Math.round(document.forms['schedule_it'].elements['min'].value);
    if (document.forms['schedule_it'].elements['sec'].value  < 10) document.forms['schedule_it'].elements['sec'].value  = '0' + Math.round(document.forms['schedule_it'].elements['sec'].value);
}

function sc_check_int(element)
{
    var regex = /(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/;
    if (element.value != '' && !regex.test(element.value)) {
        alert('must be numeric');
        element.value='00';
        element.focus();
        element.select();
        return false;
    }
    return true;
}
{/literal}
</script>
