{*Smarty template*}

<div id="scratchpad">
<center><b>ScratchPad</b></center>

{if is_array($SCRATCHPAD)}
    <form name="SP">
        <input type="hidden" name="act">
        <table>
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <th></th>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');">[{tra 0=Title}]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=duration', 'order');">[{tra 0=Duration}]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=type', 'order');">[{tra 0=Type}]</a></td>
                <td align="center">Remove</td>
            </tr>

            {foreach from=$SCRATCHPAD item=i}
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                    <td><input type="checkbox" name="{$i.id}"></td>
                    <td>
                        <a href="#" onContextmenu="return contextmenu('{$i.id}', '{$i.type}')">{$i.title}</a>
                    </td>
                    <td>{$i.duration}</td>
                    <td>{$i.type} </td>
                    <th><a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.removeItem&id={$i.id}', 'SP')">X</th>
                </tr>
            {/foreach}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <td><input type="checkbox" name="all" onClick="SP_switchAll()"></th>
                <td align="center" colspan="2"><a href="#" onClick="SP_submit()">[Remove Selected]</a></th>
                <td align="center" colspan="2"><a href="#" onClick="SP_clearAll()">[Clear]</a></th>
            </tr>
        </table>
    </form>
{/if}
</div>

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
