{*Smarty template*}

<div id="scratchpad">
<center><b>ScratchPad</b></center>

{if is_array($ScratchPad)}
    <form name="SP">
        <input type="hidden" name="act">
        <table>
            <tr>
                <th></th>
                <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');">{tra 0=Title}</a></th>
                <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=duration', 'order');">{tra 0=Duration}</a></th>
                <th><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=type', 'order');">{tra 0=Type}</a></th>
                <th>Remove</th>
            </tr>

            {foreach from=$ScratchPad item=i}
                <tr>
                    <td><input type="checkbox" name="{$i.id}"></td>
                    <td>{$i.title}</td>
                    <td>{$i.duration}</td>
                    <td>{$i.type} </td>
                    <th><a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.removeItem&SPid={$i.id}', 'SP')">X</th>
                </tr>
            {/foreach}
            <tr>
                <td><input type="checkbox" name="all" onClick="SP_switchAll()"></td>
                <td colspan="2"><a href="#" onClick="SP_submit()">[Remove Selected]</a></td>
                <td colspan="2"><a href="#" onClick="SP_clearAll()">[Clear]</a></td>
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
            href = href + '&SPid[]=' + document.forms['SP'].elements[n].name;
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
