<script type="text/javascript">
{literal}
function switchUp()
{
    if (Number(document.forms['PL_changeTransition'].elements['duration'].value) < 5000) {
        document.forms['PL_changeTransition'].elements['duration'].value  = Number(document.forms['PL_changeTransition'].elements['duration'].value) + 100;
    } else {
        alert('Maximun reached');
    }
}

function switchDown()
{
    if (Number(document.forms['PL_changeTransition'].elements['duration'].value) > 99) {
        document.forms['PL_changeTransition'].elements['duration'].value = Number(document.forms['PL_changeTransition'].elements['duration'].value) - 100;
    }
    /* else {
        if (document.forms['PL_changeTransition'].elements['type'][0].checked) document.forms['PL_changeTransition'].elements['type'][1].checked = true;
        else document.forms['PL_changeTransition'].elements['type'][0].checked = true;
    }  */
}
{/literal}
</script>
