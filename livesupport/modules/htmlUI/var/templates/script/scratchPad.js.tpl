{*Smarty template*}

{literal}
<script type="text/javascript">
function SP_submit()
{
    var href = '{/literal}{$UI_HANDLER}?act=SP.removeItem{literal}';
    var n;

    for (n=0; n < (document.forms['SP'].elements.length-1); n++) {
        if (document.forms['SP'].elements[n].checked) {
            href = href + '&id[]=' + document.forms['SP'].elements[n].name;
        }
    }
    hpopup(href, 'SP');
}

function SP_switchAll()
{
    var n;

    for (n=0; n < document.forms['SP'].elements.length; n++) {
        if (document.forms['SP'].elements[n].type == 'checkbox') {
            document.forms['SP'].elements[n].checked = document.forms['SP'].elements['all'].checked;
        }
    }
}

function SP_clearAll()
{
    if (confirm("{/literal}{tra 0='Are you sure to clear ScratchPad?'}{literal}")) {
        document.forms['SP'].elements['all'].checked = true;
        SP_switchAll();
        SP_submit();
    }
}
</script>
{/literal}
