<div class="container_elements" style="width: 607px;">

    <div style="width: 574px;">
       <div style="float: left;"><h1>##Playlist Editor## </h1></div>
       <div style="float: right;"><h1><a href="{$UI_BROWSER}?act=PL.editMetaData" style="color: #666666">{$PL->title} &nbsp; {getHour time=$PL->duration}##h##&nbsp;{getMinute time=$PL->duration}##m##&nbsp;{getSecond time=$PL->duration}##s##</a></h1></div>
    </div>

    <div class="head" style="width: 574px;">
        <div class="left">&nbsp;</div>
        <div class="right">&nbsp;</div>
        <div class="clearer">&nbsp;</div>
    </div>

<form name="PL" action="{$UI_HANDLER}" method="get">
    <input type="hidden" name="act" value="PL.reOrder"

    <div class="container_table" style="width: 594px;">
        <table style="width: 574px;">

        <!-- start repeat after 14 columns -->
           <tr class="blue_head">
               <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></td>
                   <script type="text/javascript">
                       document.forms['PL'].elements['all'].checked = false;
                   </script>
               <td>##Title##</td>
               <td style="width: 50px;">##Duration##</td>
               <td style="width: 200px">##Artist##</td>
               <td style="width: 35px;">##Type##</td>
               <td style="width: 35px; border: 0">##Move##</td>
           </tr>
        <!-- end repeat after 14 columns -->
        </table>


        <div id="draglist_container">

           {foreach from=$PL->getFlat($PL->activeId) key='pos' item='i'}
           <!-- start item -->
           <div style="position: relative; left: 0px; top: 0px;">
               <!-- {$n++} -->

               <table style="width: 574px;">
                   <!-- fade information -->
                   <tr onClick="return contextmenu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeTransition'{/if})" style="background-color: #bbb">
                      <td style="width: 30px"></td>
                      <td colspan="5" style="border: 0; cursor: pointer">##Fade## {$i.fadein_ms|string_format:"%d"} ms</td>
                   </tr>
                   <!-- end fade information -->

                   <!-- clip information -->
                   <tr class="{cycle values='blue1, blue2'}">
                      <td style="width: 30px"><input type="checkbox" class="checkbox" name="{$i.attrs.id}"/></td>
                      <td {include file="playlist/actionhandler.tpl"}>{$i.title}</td>
                      <td style="width: 50px;" {include file="playlist/actionhandler.tpl"} style="text-align: right">
                          {assign var="_duration" value=$i.duration}{niceTime in=$_duration}
                      </td>
                      <td style="width: 200px" {include file="playlist/actionhandler.tpl"}>{$i.creator}</td>
                      <td  style="width: 35px" {include file="playlist/actionhandler.tpl"}>
                          <img src="img/{$i.type}.png" border="0" alt="{$i.type|capitalize}" {include file="sub/mouseover.tpl"} />
                      </td>
                      <td  style="width: 35px; border: 0">
                          <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos-1}')"><img src="img/bt_top_xsm.png"    alt="##move up##" vspace=1 hspace=1/></a>
                          <a href="#" onClick="hpopup('{$UI_HANDLER}?act=PL.moveItem&id={$i.attrs.id}&pos={$pos+1}')"><img src="img/bt_bottom_xsm.png" alt="##move down##" vspace=1 hspace=1/></a>
                      </td>
                   </tr>
                   <!-- end clip information -->
               </table>

               <input type="hidden" name="pl_items[{$i.attrs.id}]">
           </div>
           <!-- end item -->
           {/foreach}
        </div>

        <table style="width: 574px;">
            {if $n}
                <!-- last item fadeout information -->
                <tr onClick="return contextmenu('{$i.attrs.id}', {if $n == 1}'PL.changeFadeIn'{else}'PL.changeFadeOut'{/if})" style="background-color: #bbb">
                   <td style="width: 30px"></td>
                   <td style="border: 0; cursor: pointer">##Fade## {$i.fadeout_ms|string_format:"%d"} ms</td>
                </tr>
            {else}
                <!-- empty playlist -->
               <tr class="{cycle values='blue1, blue2'}">
                   <td style="border: 0" colspan="6" align="center">##No Entry##</td>
               </tr>
           {/if}


       </table>
   </div>

   <div class="footer" style="width: 569px;">
       <input type="button" class="button_large" onClick="collector_submit('PL', '0&popup[]=PL.changeAllTransitions', '{$UI_BROWSER}', 'chgAllTrans', 400, 150)" value="##Change Fades##" />
       <input type="button" class="button_large" onClick="collector_submit('PL', 'PL.removeItem')"   value="##Remove Selected##" />
       <input type="button" class="button_large" onClick="collector_clearAll('PL', 'PL.removeItem')" value="##Clear Playlist##" />
   </div>

   <div class="container_button">
       <input type="button" class="button_large" value="##Save Playlist##"   onClick="hpopup('{$UI_HANDLER}?act=PL.save')">
       <input type="button" class="button_large" value="##Revert to Saved##" onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmRevert', 'PL.revertChanges', 400, 50)">
       <input type="button" class="button_large" value="##Delete Playlist##" onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmDelete', 'PL.deleteActive', 400, 50)">
   </div>

   <div class="container_button">
       <INPUT TYPE="button" class="button_large" VALUE="##Save Reorder##"    onClick="javascript:draglist_manager.do_submit('PL','draglist_container')">
       <input type="button" class="button_large" value="##Close Playlist##"  onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmRelease', 'PL.confirmRelease', 400, 50)">
       <input type="button" class="button_large" value="##Description##"     onClick="location.href='{$UI_BROWSER}?act=PL.editMetaData'">
   </div>

</form>
</div>

{assign var="_duration"    value=null}

<script language="JavaScript" src="assets/dom-drag.js" type="text/javascript"></script>
<script language="JavaScript" src="assets/draglist.js" type="text/javascript"></script>

<script language="JavaScript">

// a bit ugly. draglist.js assumes the existence of a global
// dragListIndex array.

var dragListIndex = new Array();

// manager classes.

draglist_manager = new fv_dragList('draglist_container');

// queries all top level <divs> under the draglist_container
// div and sets up dragging.

draglist_manager.setup();

// queries all top level <span>'s under the draglist_container_horz
// div and sets up horizontal dragging.


// addes the newly created dragList to the list of draglists on
// the page (i.e. we can have more than one on a page)

addDragList( draglist_manager );

</script>