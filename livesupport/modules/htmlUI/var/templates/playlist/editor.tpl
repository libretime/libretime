<tr><th colspan="4">active Playlist: {$PL.children.0.children.0.content}</th></tr>
<tr align="center" style="background-color: {cycle values='#eeeeee, #dadada'}"><td></td><td>Title</td><td>Duration</td><td>Type</td></tr>

{PL->getFlat assign='FLAT'}
{foreach from=$FLAT item='i'}
    <!-- {$n++} -->
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})}" style="background-color: lightblue">
        <td colspan="4" align="center">{$i.fadein_ms} ms</td>
    </tr>
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', 'PL.removeItem')" style="background-color: {cycle values='#eeeeee, #dadada'}">
        <td><input type="checkbox" name="{$i.attrs.id}"></td>
        <td>{$i.title}</td>
        <td>{$i.duration}</td>
        <td>{$i.type}</td>
    </tr>
{/foreach}
    {if $n}
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', 'PL.changeFadeOut')" style="background-color: lightblue">
        <td colspan="4" align="center">{$i.fadeout_ms} ms</td>
    </tr>
    {/if}

<tr style="background-color: {cycle values='#eeeeee, #dadada'}">
    <td><input type="checkbox" name="all" onClick="form_switchAll('PL')"></th>
    <td align="center" colspan="2"><a href="#" onClick="form_submit('PL')">[Remove Selected]</a></th>
    <td align="center" colspan="2"><a href="#" onClick="form_clearAll('PL')">[Clear]</a></th>
</tr>

<tr>
    <td><input type="button" value="Save Changes" onClick="hpopup('{$UI_HANDLER}?act=PL.save')"></td>
    <td colspan="2"><input type="button" value="Revert all Changes" onClick="hpopup('{$UI_HANDLER}?act=PL.revert')"></td>
    <td><input type="button" value="Delete" onClick="hpopup('{$UI_HANDLER}?act=PL.delete')"></td>
</tr>
