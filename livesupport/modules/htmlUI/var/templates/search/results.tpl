{*Smarty template*}

<div id="searchres">
<center>

{if is_array($searchres)}
    <table border="0" width="50%">
        <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SEARCH.reOrder&by=title', 'order');">{tra 0=Title}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SEARCH.reOrder&by=creator', 'order');">{tra 0=Creator}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SEARCH.reOrder&by=extent', 'order');">{tra 0=Duration}</a></td>
            <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SEARCH.reOrder&by=type', 'order');">{tra 0=Type}</a></td>
        </tr>
        {foreach from=$searchres item=s}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <td align="center">{$s.title}</td>
                <td align="center">{$s.creator}</td>
                <td align="center">{$s.duration}</td>
                <td align="center">{$s.type}</td>
                <!--
                <td>
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.addItem&id={$s.id}', '2PL')">[PL]</a>
                    <a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$s.id}', '2SP')">[SP]</a>
                </td>
                -->
            </tr>
        {/foreach}
    </table>
{else}
    No match found.
{/if}

</center>
</div>

