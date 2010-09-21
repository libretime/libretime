<!-- start playlist editor -->
    <div class="container_elements" style="width: 607px;">

                <div style="width: 574px;">
                    <div style="float: left;"><h1>##Playlist Editor## </h1></div>
                    <div style="float: right;"><h1><a href="{$UI_BROWSER}?act=PL.editMetaData" style="color: #666666">{$PL->title} &nbsp; {niceTime in=$PL->duration} &nbsp;</a></h1></div>
                </div>

                <div class="head" style="width: 574px;">
                    <div class="left">&nbsp;</div>
                    <div class="right">&nbsp;</div>
                    <div class="clearer">&nbsp;</div>
                </div>
                <div class="container_table" style="width: 594px;">
                    <table style="width: 574px;">
                        <form name="PL">
                    <!-- start repeat after 14 columns -->
                        <tr class="blue_head">
                            <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></td>
                            <td style="width: 200px">##Title##</td>
                            <td style="white-space: nowrap">##Clip length##</td>
                            <td>##Cue In##</td>
                            <td>##Cue Out##</td>
                            <td style="width: 200px">##Artist##</td>
                            <td style="width: 30px;">##Type##</td>
                            <td style="width: 30px; border: 0">##Move##</td>
                        </tr>
                    <!-- end repeat after 14 columns -->
                    <!-- start item -->
                    {foreach from=$PL->getActiveArr($PL->activeId) key='pos' item='i'}
                        
                        <!-- fade information -->
                        <tr onClick="return contextmenu('{$i.attrs.id}', {if $i.firstInList == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})" style="background-color: #bbb">
                            <td></td>
                            <td colspan="7" style="border: 0; cursor: pointer">##Fade In## {$i.fadein|string_format:"%d"} ms</td>
                        </tr>
                        <!-- /fade information -->
                        
                        <tr class="{cycle values='blue1, blue2'}">
                            <td><input type="checkbox" class="checkbox" name="{$pos}"/></td>
                            <td {include file="playlist/actionhandler.tpl"}>{$i.track_title}</td>
                            <td {include file="playlist/actionhandler.tpl"} style="text-align: right">
                                {assign var="_playlength" value=$i.cliplength}{niceTime in=$_playlength}
                            </td>
                            <td {include file="playlist/actionhandler.tpl"} style="text-align: right">
                                {assign var="_duration" value=$i.cuein}{niceTime in=$_duration}
                            </td>
                            <td {include file="playlist/actionhandler.tpl"} style="text-align: right">
                                {assign var="_duration" value=$i.cueout}{niceTime in=$_duration}
                            </td>
                            <td {include file="playlist/actionhandler.tpl"}>{$i.artist_name}</td>
                            <td {include file="playlist/actionhandler.tpl"}>
                                <img src="img/{$i.type}.png" border="0" alt="{$i.ftype|capitalize}" {* include file="sub/alttext.tpl" *} />
                            </td>
                            <td style="border: 0">
                                <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id=&oldPos={$pos}&newPos={$pos-1}')"><img src="img/bt_top_xsm.png"    alt="##move up##" vspace=1 hspace=1/></a>
                                <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&oldPos={$pos}&newPos={$pos+1}')"><img src="img/bt_bottom_xsm.png" alt="##move down##" vspace=1 hspace=1/></a>
                            </td>
                        </tr>
                        
                         <!-- fade information -->
                        <tr onClick="return contextmenu('{$i.attrs.id}', 'PL.changeFadeOut')" style="background-color: #bbb">
                            <td></td>
                            <td colspan="7" style="border: 0; cursor: pointer">##Fade Out## {$i.fadeout|string_format:"%d"} ms</td>
                        </tr>
                        <!-- /fade information -->
                    {/foreach}
                        {if isset($pos)}
                        
                       
                        
                        {else}
                            <tr class="{cycle values='blue1, blue2'}">
                                <td style="border: 0" colspan="7" align="center">##Empty playlist##</td>
                            </tr>
                        {/if}
                    <!-- end item -->
                        </form>
                    </table>
                </div>
                <div class="footer" style="width: 569px;">
                {* <input type="button" class="button_large" onClick="collector_submit('PL', '0&popup[]=PL.changeAllTransitions', '{$UI_BROWSER}', 'chgAllTrans', 400, 150)" value="##Change Fades##" /> *}
                    <input type="button" class="button_large" onClick="collector_submit('PL', 'PL.removeItem')"   value="##Remove Selected##" />
                    <input type="button" class="button_large" onClick="collector_clearAll('PL', 'PL.removeItem')" value="##Clear Playlist##" />
                </div>
                <div class="container_button">
                    <input type="button" class="button_large" value="##Delete Playlist##"  onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmDelete',  'PL.deleteActive',   400, 50)">
                </div>
                <div class="container_button">
                    <input type="button" class="button_large" value="##Reorder Playlist##" onClick="popup('{$UI_BROWSER}?popup[]=PL.arrangeItems',   'PL.arrangeItems',   533, 600)">
                    <input type="button" class="button_large" value="##Close Playlist##"   onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmRelease', 'PL.confirmRelease', 400, 50)">
                    <input type="button" class="button_large" value="##Description##"      onClick="location.href='{$UI_BROWSER}?act=PL.editMetaData'">
                </div>
            </div>

<script type="text/javascript">
        document.forms['PL'].elements['all'].checked = false;
        collector_switchAll('PL');
</script>

{assign var="_duration" value=null}

<!-- end playlist editor -->