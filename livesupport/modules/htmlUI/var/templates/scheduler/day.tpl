{$SCHEDULER->buildDay()}

<table border=1>

{foreach from=$SCHEDULER->Day item="_Hour"}
    {assign var="_hour" value=$_Hour.hour}
    <tr>
        <td {include file="scheduler/contextmenu.tpl"}>
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.set&hour={$_Hour.hour}')">{$_Hour.hour}
        </td>

    </tr>

{/foreach}

</table>
