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
    <center>
    {if ( is_array($searchres.search))}
        <table>
          <tr><th>{tra 0=Title}</th><th>{tra 0=Duration}</th><th></th></tr>
            {foreach from=$searchres.search item=s}
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                    <td>{$s.title}</td>
                    <td>{$s.duration}</td>
                    <td><a href="{$UI_BROWSER}?act=getMdata&id={$s.id}">[XML]</a>
                        <a href="{$UI_BROWSER}?act=editMetaDataValues&id={$s.id}">[Form]</a>
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=add2SP&id={$s.id}', '2SP')">[SP]</a>
                    </td>
                  </tr>
                </div>
            {/foreach}
        </table>
    {else}
        No match found.
    {/if}
    </center>
    </div>
{/if}