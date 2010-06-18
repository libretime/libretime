{include file="popup/header.tpl"}
{include file="script/alttext.js.tpl"}

<div class="container_elements" style="width: 500px">
    <div>
        <div style="float: left;"><h1>##Reorder Playlist## </h1></div>
        <div style="float: right;"><h1>{$PL->title} &nbsp; {getHour time=$PL->duration}##h##&nbsp;{getMinute time=$PL->duration}##m##&nbsp;{getSecond time=$PL->duration}##s##</h1></div>
    </div>

    <br><br>
    {$UI_PL_DRAG_INTRO}

    <form name="PL" action="{$UI_HANDLER}">
        <input type="hidden" name="act" value="PL.reArrange">
         <div>
        <table style="width: 500px;">
           <tr class="blue_head">
               <td>##Title##</td>
               <td style="width: 50px;">##Duration##</td>
               <td style="width: 150px">##Artist##</td>
               <td style="width: 35px; border:0">##Type##</td>
           </tr>
        </table>

          </div>
        <div id="draglist_container">

           {foreach from=$PL->getFlat($PL->activeId) key='pos' item='i'}
           <!-- start item -->
           <div style="position: relative; left: 0px; top: 0px;">
               <!-- {$n++} -->

               <table style="width: 500px;">
                   <!-- clip information -->
                   <tr><td style="border:0" colspan="5"></tr>
                   <tr class="blue1" style="cursor: move;">
                      <td>{$i.title}</td>
                      <td style="width: 50px;">{assign var="_duration" value=$i.duration}{niceTime in=$_duration}</td>
                      <td style="width: 150px">{$i.creator}</td>
                      <td style="width: 35px; border:0"><img src="img/{$i.type}.png" border="0" alt="{$i.type|capitalize}" {include file="sub/alttext.tpl"} /></td>
                   </tr>
                   <!-- end clip information -->
               </table>

               <input type="hidden" name="pl_items[{$i.attrs.id}]">
           </div>
           <!-- end item -->
           {/foreach}

        </div>

        <br>

        <div style="width: 500px;">
           <div style="float: left;"><input type="button" class="button_large" value="##Cancel##" onClick="window.close()"> </div>
           <div style="float: right;"><INPUT TYPE="button" class="button_large" VALUE="##Save Playlist##" onClick="javascript:draglist_manager.do_submit('PL','draglist_container')"></div>
        </div>

    </form>
    <br>
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

</body>
</html>