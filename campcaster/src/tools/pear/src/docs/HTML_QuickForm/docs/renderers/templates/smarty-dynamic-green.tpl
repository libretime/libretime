<!--  $Id: smarty-dynamic-green.tpl,v 1.1 2003/04/30 19:23:35 avb Exp $ -->

<tr>
    <td align="right" valign="top" class="green"><b>{$element.label}:</b></td>
    <td valign="top" align="left" class="green">
    {if $element.error}<font color="red">{$element.error}</font><br />{/if}
    {$element.html}{if $element.required}<font color="red">*</font>{/if}
    </td>
</tr>
