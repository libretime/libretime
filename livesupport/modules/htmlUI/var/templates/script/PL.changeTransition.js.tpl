<script type="text/javascript">
{literal}
function pl_switchUp()
{
    if (Number(document.forms['PL_changeTransition'].elements['duration'].value) < 5000) {
        document.forms['PL_changeTransition'].elements['duration'].value  = Number(document.forms['PL_changeTransition'].elements['duration'].value) + 100;
    } else {
        alert('Maximun reached');
    }
}

function pl_switchDown()
{
    if (Number(document.forms['PL_changeTransition'].elements['duration'].value) > 99) {
        document.forms['PL_changeTransition'].elements['duration'].value = Number(document.forms['PL_changeTransition'].elements['duration'].value) - 100;
    }
    /* else {
        if (document.forms['PL_changeTransition'].elements['type'][0].checked) document.forms['PL_changeTransition'].elements['type'][1].checked = true;
        else document.forms['PL_changeTransition'].elements['type'][0].checked = true;
    }  */
}

var pl_loop;

function pl_start(direction)
{
    pl_loop = setInterval("pl_switch"+ direction + "()", 100);
}

function pl_stop()
{
    clearInterval(pl_loop);
}

{/literal}
</script>
