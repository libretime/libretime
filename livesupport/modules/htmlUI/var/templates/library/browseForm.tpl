{assign var="_style_select" value=" style='width: 180px;'"}
{assign var="_style_textarea" value=" class='area_browse'"}

		<!-- start playlist editor -->
			<div class="container_elements" style="width: 607px;">
				<h1>Browse</h1>
                <table>
                <tr>
                <td style="border:0">
                    {foreach from=$browseForm.col1 item=dynform}
                        {include file="library/dynForm_browse.tpl"}
                    {/foreach}	
                </td><td style="border:0">
                    {foreach from=$browseForm.col2 item=dynform}
                        {include file="library/dynForm_browse.tpl"}
                    {/foreach}
                </td><td style="border:0">
                    {foreach from=$browseForm.col3 item=dynform}
                        {include file="library/dynForm_browse.tpl"}
                    {/foreach}
                </td>
                </tr>
                </table>
                    {foreach from=$browseForm.global item=dynform}
                        {include file="sub/dynForm_plain.tpl"}
                    {/foreach}		
			</div>
		<!-- end playlist editor -->
{assign var="_style_select" value=""}
{assign var="_style_textarea" value=""}
{*
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
*}