{*Smarty template*}

<div class="standardFrame">
{include file="sub/x.tpl"}

<center>
<form name="PL">
<table border="1">
<tr><th colspan="4">Simple Playlist Management</th></tr>

{PL->get assign='PL'}
{if is_array($PL)}      {* already activated Playlist *}

        <tr><th colspan="4">active Playlist: {$PL.children.0.children.0.content}</th></tr>
        <tr align="center"><td></td><td>Title</td><td>Duration</td><td>Type</td></tr>

        {PL->getFlat assign='FLAT'}
        {foreach from=$FLAT item='i'}
            {uiBrowser->_niceTime p1=$i.playlength assign='nicelength'}
            <tr onMouseOver="highlight()" onMouseOut="darklight()" onContextmenu="return menu('{$i.id}', 'PL.removeItem')">
                <td><input type="checkbox" name="{$i.id}"></td>
                <td>{$i.title}</td>
                <td>{$nicelength}</td>
                <td>{$i.type}</td>
            </tr>
        {/foreach}
        <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
            <td><input type="checkbox" name="all" onClick="form_switchAll('PL')"></th>
            <td align="center" colspan="2"><a href="#" onClick="form_submit('PL')">[Remove Selected]</a></th>
            <td align="center" colspan="2"><a href="#" onClick="form_clearAll('PL')">[Clear]</a></th>
        </tr>
     </table>

{else}                      {* no active Playlist *}
    No active Playlist!
    <br>
    <input type="button" value="Make new Playlist" onClick="hpopup('{$UI_HANDLER}?act=PL.create')">
{/if}

</table>
</form>
</div>
</div>
