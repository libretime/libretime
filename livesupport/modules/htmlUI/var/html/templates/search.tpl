{*Smarty template*}

{include file="script/search.js.tpl"}

{if $showSearchForm}
    {literal}
    <style type="text/css">
        .dynformelement {
            width : 800px;
        }
    </style>
    {/literal}
    <div id="searchform">
      <center>
        {foreach from=$searchform item=dynform}
            {include file="form_parts/dynForm_plain.tpl"}
        {/foreach}
      </center>
    </div>
{/if}

{if $showSearchRes}
    <div id="searchres">

    {if (count($searchres.search))}
        {foreach from=$searchres.search item=s}
            <div style="background-color: {cycle values='#eeeeee, #dadada'}">{$s.gunid}
                <a href="{$UI_BROWSER}?act=getMdata&id={$s.par_id}">[XML]</a>
                <a href="{$UI_BROWSER}?act=editMetaDataValues&id={$s.par_id}">[Form]</a>
            </div>
        {/foreach}
    {else}
        No match found.
    {/if}

    </div>
{/if}