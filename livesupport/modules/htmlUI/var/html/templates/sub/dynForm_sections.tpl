{$dynform.javascript}

<table border="0" class="maintable">
    <form{$dynform.attributes}>{$dynform.hidden}

    {foreach item=sec key=i from=$dynform.sections}
        <tr>
            <td class="header" colspan="2">
            <b>{$sec.header}</b></td>
        </tr>

        {foreach item=element from=$sec.elements}

            <!-- elements with alternative layout in external template file-->
            {if $element.style}
                {include file="smarty-dynamic-`$element.style`.tpl}

            {*
            NOTE: Another way ist to have smarty template code in
            $element.style. In this case you can do:

            {if $element.style}
                {eval var=$element.style}
            *}

            <!-- submit or reset button (don't display on frozen forms) -->
            {elseif $element.type eq "submit" or $element.type eq "reset"}
                {if not $dynform.frozen}
                <tr>
                    <td>&nbsp;</td>
                    <td align="left">{$element.html}</td>
                </tr>
                {/if}

            <!-- normal elements -->
            {else}
                <tr>
                {if $element.type eq "textarea"}
                    <td colspan="2">
                        {if $element.required}<font color="red">*</font>{/if}<b>{$element.label}</b><br />
                {else}
                    <td align="right" valign="top">
                        {if $element.required}<font color="red">*</font>{/if}<b>{$element.label}:</b></td>
                    <td>
                {/if}
                    {if $element.error}<font color="red">{$element.error}</font><br />{/if}
                    {if $element.type eq "group"}
                        {foreach key=gkey item=gitem from=$element.elements}
                            {$gitem.label}
                            {$gitem.html}{if $gitem.required}<font color="red">*</font>{/if}
                            {if $element.separator}{cycle values=$element.separator}{/if}
                        {/foreach}
                    {else}
                        {$element.html}
                    {/if}
                    <div style="font-size: 80%;">{$element.label_note}</div>
                    </td>
                </tr>

            {/if}
        {/foreach}
    {/foreach}

    {if $dynform.requirednote and not $dynform.frozen}
    <tr>
        <td>&nbsp;</td>
        <td valign="top">{$dynform.requirednote}</td>
    </tr>
    {/if}

    </form>
</table>

&nbsp;
<p><b>Collected Errors:</b><br />
{foreach key=name item=error from=$dynform.errors}
    <font color="red">{$error}</font> in element [{$name}]<br />
{/foreach}
</p>
