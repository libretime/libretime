<!--  $Id: smarty-dynamic-fancygroup.tpl,v 1.2 2005/02/08 11:39:31 sebastian Exp $ -->

<tr>
    <td valign="top" align="right">
        <b{if $element.error} style="color: Red;"{/if}>{if $element.required}<font color="red">*</font>{/if}{$element.label}:</b>
    </td>
    <td>
    <table cellspacing="2" border="0">
        {foreach key=gkey item=gitem from=$element.elements}
        <tr>
            {if $gitem.type eq "radio"}
            <td colspan="2" class="green">
                {$gitem.html}                         
            </td>
            {else}
            <td class="green" align="right">
                {if $gitem.required}<font color="red">*</font>{/if}
                {$gitem.label}
            </td>
            <td class="green">
                {$gitem.html}                         
            </td>            
            {/if}
        </tr>
        {/foreach}
    </table>
    </td>
</tr>
