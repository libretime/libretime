{$dynform.javascript}
<!-- start dynForm_sections -->
    <form{$dynform.attributes}>{$dynform.hidden}

    {foreach item=sec key=i from=$dynform.sections}
        <h1>##{$sec.header}##</h1>

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
                    <div class="container_button_upload">
                    {$element.html}
                    </div>
                {/if}

            <!-- normal elements -->
            {else}
                {if $element.type eq "textarea"}
					<div class="container_search">
                        <label>
                        {$element.label}
                        {if $element.required}<font color="red">*</font>{/if}
                        </label>
                    </div>
                {else}
					<div class="container_search">
                        <label>
                        {$element.label}
                        {if $element.required}<font color="red">*</font>{/if}
                        </label>
                    </div>
                {/if}
                    {if $element.error}
                        <label><font color="red">{$element.error}</font></label>
                    {/if}
                    {if $element.type eq "group"}
                        {foreach key=gkey item=gitem from=$element.elements}
                            <label>
                            {$gitem.label}
                            {$gitem.html}{if $gitem.required}<font color="red">*</font>{/if}
                            </label>
                            {if $element.separator}{cycle values=$element.separator}{/if}
                        {/foreach}
                    {else}
                        {$element.html}
                    {/if}
                    <div style="font-size: 80%;">{$element.label_note}</div>
            {/if}
        {/foreach}
    {/foreach}

    {if $dynform.requirednote and not $dynform.frozen}
        <div class="container_search">
            <label>
            {if $element.required}<font color="red">*</font>{/if}
            {$dynform.requirednote}
            </label>
        </div>
    {/if}

    </form>

{*
<p><b>Collected Errors:</b><br />
{foreach key=name item=error from=$dynform.errors}
    <font color="red">{$error}</font> in element [{$name}]<br />
{/foreach}
</p>
*}
<!-- end dynForm_sections -->