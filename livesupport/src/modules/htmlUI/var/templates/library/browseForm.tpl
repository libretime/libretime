{assign var="_style_select" value=" style='width: 180px;'"}
{assign var="_style_textarea" value=" class='area_browse'"}

                <table style="margin:0; padding:0">
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

		<!-- end browsing -->
{assign var="_style_select" value=""}
{assign var="_style_textarea" value=""}