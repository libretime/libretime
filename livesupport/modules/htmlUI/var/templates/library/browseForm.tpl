{*Smarty template*}

{* {include file="script/search.js.tpl"}   *}


{literal}
<style type="text/css">
    .dynformelement {
        width : 250px;
    }
</style>
{/literal}
<div id="searchform">
{include file="sub/x.tpl"}

<table>
    <tr>

    {foreach from=$browseForm item=form}
    <td>
        {foreach from=$form item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
    </td>
    {/foreach}

    </tr>
</table>

</div>