<!-- start playlist editor -->
    <div class="container_elements" style="width: 607px;">
            <form name="PL">
                <h1>##Playlist Editor##</h1>
                <div class="head" style="width: 574px;">
                    <div class="left">&nbsp;</div>
                    <div class="right">&nbsp;</div>
                    <div class="clearer"></div>
                </div>
                <div class="container_table" style="width: 594px;">
                    <table style="width: 574px;">

                    <!-- start repeat after 14 columns -->
                        <tr class="blue_head">
                            <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></td>
                                <script type="text/javascript">
                                    document.forms['PL'].elements['all'].checked = false;
                                </script>
                            <td style="width: 155px">##Name##</td>
                            <td style="width: 69px">##Duration##</td>
                            <td style="width: 178px">##Artist##</td>
                            <td style="width: 107px;">##Type##</td>
                            <td style="width: 30px; border: 0">##Move##</td>
                        </tr>
                    <!-- end repeat after 14 columns -->
                    <!-- start item -->
                    {foreach from=$PL->getFlat() key='pos' item='i'}
                        <!-- {$n++} -->
                        <!-- fade information -->
                        <tr onContextmenu="return contextmenu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})}" style="background-color: #bbb">
                            <td></td>
                            <td colspan="5" style="border: 0">##Fade## {$i.fadein_ms|string_format:"%d"} ms</td>
                        </tr>
                        <tr class="{cycle values='blue1, blue2'} " onContextmenu="return contextmenu('{$i.attrs.id}',
                          {if $i.type|lower == "audioclip"}'listen', '{$i.gunid}', {/if}
                          'PL.removeItem')">
                            <td><input type="checkbox" class="checkbox" name="{$i.attrs.id}"/></td>
                            <td>{$i.title}</td>
                            <td>{$i.duration}</td>
                            <td>{$i.creator}</td>
                            <td>{$i.type}</td>
                            <td style="border: 0">
                            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos-1}')"><img src="img/bt_top_xsm.gif" alt="##move up##" vspace=1 hspace=1/></a>
                            <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos+1}')"><img src="img/bt_bottom_xsm.gif" alt="##move down##" vspace=1 hspace=1/></a>
                            </td>
                        </tr>
                    {/foreach}
                        {if $n}
                        <!-- fade information -->
                        <tr onContextmenu="return contextmenu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeFadeOut'{/if})}" style="background-color: #bbb">
                            <td></td>
                            <td colspan="5" style="border: 0">##Fade## {$i.fadeout_ms|string_format:"%d"} ms</td>
                        </tr>
                        {else}
                            <tr class="{cycle values='blue1, blue2'}">
                                <td style="border: 0" colspan="6" align="center">##No Entry##</td>
                            </tr>
                        {/if}
                    <!-- end item -->
                    </table>
                </div>
                <div class="footer" style="width: 569px;">
                    <input type="button" class="button_large" onClick="collector_submit('PL', 'PL.removeItem')" value="##Remove Selected##" />
                    <input type="button" class="button_large" onClick="collector_clearAll('PL', 'PL.removeItem')" value="##Clear Playlist##" />
                </div>
                <div class="container_button">
                    <input type="button" class="button_large" value="##Save Playlist##" onClick="hpopup('{$UI_HANDLER}?act=PL.save')">
                    <input type="button" class="button_large" value="##Revert to Saved##" onClick="hpopup('{$UI_HANDLER}?act=PL.revert')">
                    <input type="button" class="button_large" value="##Delete Playlist##" onClick="popup('{$UI_BROWSER}?popup[]=PL.deleteActive', 'PL.deleteActive', 400, 200)">
                </div>
                <div class="container_button">
                    <input type="button" class="button_large" value="##Save and Close##" onClick="hpopup('{$UI_HANDLER}?act=PL.release')">
                    <input type="button" class="button_large" value="##Metadata##" onClick="location.href='{$UI_BROWSER}?act=PL.editMetaData'">
                </div>
            </div>
            </form>
        <!-- end playlist editor -->


{*
<form name="PL">
<table border="0">
<tr><th colspan="4">active Playlist: {$PL.children.0.children.0.content}</th></tr>
<tr align="center" style="background-color: {cycle values='#eeeeee, #dadada'}"><td></td><td>Title</td><td>Duration</td><td>Type</td></tr>

{foreach from=$PL->getFlat() key='pos' item='i'}
    <!-- {$n++} -->
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})}" style="background-color: lightblue">
        <td colspan="4" align="center">{$i.fadein_ms|string_format:"%d"} ms</td>
    </tr>
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', 'PL.removeItem')" style="background-color: {cycle values='#eeeeee, #dadada'}">
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
    <tr onMouseOver="highlight()"
        onMouseOut="darklight()"
        onContextmenu="return menu('{$i.attrs.id}', 'PL.changeFadeOut')" style="background-color: lightblue">
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
*}
