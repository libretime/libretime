{assign var="_PL_activeId" value=$PL->getActiveId()}

<!-- start scratch pad -->
{if is_array($SCRATCHPAD)}
<form name="SP">
    <div class="container_elements">
        <h1>##Scratch Pad##</h1>
        <div class="head" style="width:255px; height: 21px;">&nbsp;</div>
        <div class="container_table" style="width:275px;">
            <table style="width:255px;">
            <!-- start table header -->
                <tr class="blue_head">
                    <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('SP')"></td>
                    <td style="width: 95px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');" id="blue_head">##Title##</a></td>
                    <td style="width: 69px"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');" id="blue_head">##Duration##</td>
                    <td style="width: 41px; border: 0"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');" id="blue_head">##Type##</td>
                </tr>
            <!-- end table header -->

                {foreach from=$SCRATCHPAD item=i}
                <!-- start item -->
                <tr class="{cycle values='blue1, blue2'}" {assign var="moreContextBefore" value=", 'SP.removeItem'"} {include file="sub/contextmenu.tpl"}>
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td>
                        {if $_PL_activeId == $i.id}
                            <b>{$i.title|truncate:12}</b>
                        {else}
                            {$i.title|truncate:12}
                        {/if}
                    </td>
                    <td>{$i.duration}</td>
                    <td style="border: 0"><img src="img/{$i.type|lower}.gif" border="0" alt="{$i.type}" /></td>
                </tr>
                {/foreach}
                <!-- end item -->
            </table>
        </div>

        <div class="footer" style="width:255px;">
            <select name="SP_multiaction">
                <option>##Multiple Action:##</option>
                <option onClick="collector_submit('SP', 'SP.removeItem')">##Remove file(s)##</option>
                {if $_PL_activeId}
                    <option onClick="collector_submit('SP', 'PL.addItem')">##Add file(s) to active Playlist##</option>
                {else}
                    <option onClick="collector_submit('SP', 'PL.create')">##New Playlist using this file(s)##</option>
                {/if}
            </select>
            <script type="text/javascript">
                document.forms['SP'].elements['SP_multiaction'].options[0].selected = true;
                //document.forms['SP'].elements['all'].checked = false;
                //collector_switchAll('SP');
            </script>
            <a href="#" onClick="collector_clearAll('SP', 'SP.removeItem')" id="blue_head">##Clear##</a>
        </div>
    </div>
</form>
{/if}

{assign var="_PL_activeId" value=NULL}
<!-- end scratch pad -->

{assign var="_PL_activeId" value=NULL}
