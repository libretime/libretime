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
      <td>
        {foreach from=$browseForm.col1 item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
      </td>
      <td>
        {foreach from=$browseForm.col2 item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
      </td>
      <td>
        {foreach from=$browseForm.col3 item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
      </td>
    </tr>

    <tr>
      <td></td>
      <td>
        {foreach from=$browseForm.global item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
      </td>
      <td></td>
    </tr>
</table>

</div>