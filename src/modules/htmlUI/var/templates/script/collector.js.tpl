{literal}
<script type="text/javascript">

function collector_submit(formname, action, script, name, width, height)
{
    var href = '';
    var n;
    
    {/literal}
    if (!script) var script = '{$UI_HANDLER}';
    {literal}
    
    for (n = 0; n < (document.forms[formname].elements.length); n++) {
        if (document.forms[formname].elements[n].checked && document.forms[formname].elements[n].name != 'all') {
            href = href + '&id[]=' + document.forms[formname].elements[n].name;
        }
    }

    if (href == '') return false;
    
    
    if (action == 'delete') {
                {/literal}
                popup('{$UI_BROWSER}?popup[]=deleteItem' + href, 'deleteItem', 400, 100);
                return;
                {literal}
    }
    if (name)   popup (script + '?act=' + action + href, popup, width, height);
    else        hpopup(script + '?act=' + action + href);
}

function collector_switchAll(formname)
{
    var n;

    for (n = 0; n < document.forms[formname].elements.length; n++) {
        if (document.forms[formname].elements[n].type == 'checkbox') {
            document.forms[formname].elements[n].checked = document.forms[formname].elements['all'].checked;
        }
    }
}

function collector_clearAll(formname, action)
{
    if (confirm("{/literal}{tra 0='Are you sure to remove all Items?'}{literal}")) {
        document.forms[formname].elements['all'].checked = true;
        collector_switchAll(formname);
        collector_submit(formname, action);
    }
}
</script>
{/literal}
