<tr><th colspan="4">active Playlist: {$PL.children.0.children.0.content}</th></tr>
<tr align="center" style="background-color: {cycle values='#eeeeee, #dadada'}"><td></td><td>Title</td><td>Duration</td><td>Type</td></tr>

{PL->getFlat assign='FLAT'}
{foreach from=$FLAT item='i'}
    {* {uiBrowser->_niceTime p1=$i.playlength assign='nicelength'} *}
    <tr onMouseOver="highlight()" onMouseOut="darklight()" onContextmenu="return menu('{$i.attrs.id}', 'PL.removeItem')" style="background-color: {cycle values='#eeeeee, #dadada'}">
        <td><input type="checkbox" name="{$i.attrs.id}"></td>
        <td>{$i.title}</td>
        <td>{$i.duration}</td>
        <td>{$i.type}</td>
    </tr>
{/foreach}
<tr style="background-color: {cycle values='#eeeeee, #dadada'}">
    <td><input type="checkbox" name="all" onClick="form_switchAll('PL')"></th>
    <td align="center" colspan="2"><a href="#" onClick="form_submit('PL')">[Remove Selected]</a></th>
    <td align="center" colspan="2"><a href="#" onClick="form_clearAll('PL')">[Clear]</a></th>
</tr>

<tr>
    <td colspan="2"><input type="button" value="Save & Release" onClick="hpopup('{$UI_HANDLER}?act=PL.release')"></td>
    <td colspan="2"><input type="button" value="Revert all Changes" onClick="hpopup('{$UI_HANDLER}?act=PL.revert')"></td>
</tr>
