<form name="PL">
<table border="0">
<tr><th colspan="4">active Playlist: {$PL.children.0.children.0.content}</th></tr>
<tr align="center" style="background-color: {cycle values='#eeeeee, #dadada'}"><td></td><td>Title</td><td>Duration</td><td>Type</td></tr>

{foreach from=$PL->getFlat() key='pos' item='i'}
    <!-- {$n++} -->
    <tr onContextmenu="return contextmenu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})}" style="background-color: lightblue">
        <td colspan="4" align="center">{$i.fadein_ms|string_format:"%d"} ms</td>
    </tr>
    <tr onContextmenu="return contextmenu('{$i.attrs.id}',
        {if $i.type|lower == "audioclip"}'listen', '{$i.gunid}', {/if}
        'PL.removeItem')" style="background-color: {cycle values='#eeeeee, #dadada'}">
        <td>
            <input type="checkbox" name="{$i.attrs.id}">
            <font size="+1">
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos-1}')">&uarr;</a>
            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos+1}')">&darr;</a>
            </font>
        </td>
        <td>{$i.title}</td>
        <td>{$i.duration}</td>
        <td>{$i.type}</td>
    </tr>
{/foreach}
    {if $n}
    <tr onContextmenu="return contextmenu('{$i.attrs.id}', 'PL.changeFadeOut')" style="background-color: lightblue">
        <td colspan="4" align="center">{$i.fadeout_ms|string_format:"%d"} ms</td>
    </tr>
    {/if}

<tr style="background-color: {cycle values='#eeeeee, #dadada'}">
    <td><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></td>
    <td align="center" colspan="2"><a href="#" onClick="collector_submit('PL', 'PL.removeItem')">[Remove Selected]</a></th>
    <td align="center" colspan="2"><a href="#" onClick="collector_clearAll('PL', 'PL.removeItem')">[Clear]</a></th>
</tr>

<tr>
    <td><input type="button" value="Save" onClick="hpopup('{$UI_HANDLER}?act=PL.save')"></td>
    <td><input type="button" value="Revert" onClick="hpopup('{$UI_HANDLER}?act=PL.revert')"></td>
    <td><input type="button" value="Release" onClick="hpopup('{$UI_HANDLER}?act=PL.release')"></td>
    <td><input type="button" value="MData" onClick="location.href='{$UI_BROWSER}?act=PL.editMetaData'"></td>
    <td><input type="button" value="Delete" onClick="popup('{$UI_BROWSER}?popup[]=PL.deleteActive', 'PL.deleteActive', 400, 200)"></td>
</tr>
</table>
</form>
