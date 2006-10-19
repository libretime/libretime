{$dynform.javascript}

<form{$dynform.attributes}>{$dynform.hidden}

{foreach item=element from=$dynform.elements}
    {if $element.style}
        {include file="smarty-dynamic-`$element.style`.tpl}
    {/if}
    {*
    NOTE: Another way ist to have smarty template code in
    $element.style. In this case you can do:

    {if $element.style}
        {eval var=$element.style}
    {/if}
    *}


    {if $element.type eq 'static'}     
        {$element.html}

    {else}
        <div class="container_browse">
            {if $element.label}
                <label for='category_1'>{$element.label}
                {*if $element.required}<font color="red">*</font>{/if*}
                </label>
            {/if}
            {if $element.error}<font color="red">{$element.error}</font><br />{/if}
            {if $element.type eq "group"}
                {foreach key=gkey item=gitem from=$element.elements}
                    {$gitem.label}{$gitem.html}
                    {if $gitem.required}<font color="red">*</font>{/if}
                    {if $element.separator}{cycle values=$element.separator}
                    {/if}
                {/foreach}
            {else}
                {$element.html}
            {/if}
            <!--div style="font-size: 80%;">{$element.label_note}</div-->
        </div>
    {/if}
{/foreach}

    {if $dynform.requirednote and not $dynform.frozen}
        {*<div class='dynformelement'>
            {$dynform.requirednote}
        </div>*}
    {/if}
</form>

<!--
&nbsp;
<p><b>Collected Errors:</b><br />
{foreach key=name item=error from=$dynform.errors}
    <font color="red">{$error}</font> in element [{$name}]<br />
{/foreach}
</p> -->
