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
    {include file="sub/x.tpl"}
      <center>
        {foreach from=$searchform item=dynform}
            {include file="sub/dynForm_plain.tpl"}
        {/foreach}
      </center>
    </div>
{/if}

{if $showSearchRes}
    <div id="searchres">
    <center>
    {if is_array($searchres)}
        <table>
          <tr><th>{tra 0=Title}</th><th>{tra 0=Duration}</th><th></th></tr>
            {foreach from=$searchres item=s}
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                    <td>{$s.title}</td>
                    <td>{$s.duration}</td>
                    <td>
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.addItem&id={$s.id}', '2PL')">[PL]</a>
                        <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$s.id}', '2SP')">[SP]</a>
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