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
                        <script type="text/javascript">
                            document.forms['SP'].elements['all'].checked = false;
                        </script>
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
                <option onClick="collector_submit('SP', 'SP.removeItem')">##Remove##</option>
                {if $_PL_activeId}
                    <option onClick="collector_submit('SP', 'PL.addItem')">##Add to Playlist##</option>
                {else}
                    <option onClick="collector_submit('SP', 'PL.create')">##New Playlist using Item##</option>
                {/if}
            </select>
            <script type="text/javascript">
                document.forms['SP'].elements['SP_multiaction'].options[0].selected=true;
            </script>
            <a href="#" onClick="collector_clearAll('SP', 'SP.removeItem')" id="blue_head">##Clear##</a>
        </div>
    </div>
</form>
{/if}

{assign var="_PL_activeId" value=NULL}
<!-- end scratch pad -->



{*
<!-- old template -->
{assign var="_PL_activeId" value=$PL->getActiveId()}

<div id="scratchpad">
<center><b>ScratchPad</b>
{if is_array($SCRATCHPAD)}
    <form name="SP">
        <input type="hidden" name="act">
        <table>
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <th></th>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=title', 'order');">[Title]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=duration', 'order');">[Duration]</a></td>
                <td align="center"><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reOrder&by=type', 'order');">[Type]</a></td>
                <td align="center">Remove</td>
            </tr>

            {foreach from=$SCRATCHPAD item=i}
                <tr style="background-color: {cycle values='#eeeeee, #dadada'}" {assign var="moreContextBefore" value=", 'SP.removeItem'"} {include file="sub/contextmenu.tpl"}>
                    <td><input type="checkbox" name="{$i.id}"></td>
                    <td>
                        {if $_PL_activeId == $i.id}
                            <b>{$i.title|truncate:30}</b>
                        {else}
                            {$i.title|truncate:30}
                        {/if}
                    </td>
                    <td>{$i.duration}</td>
                    <td>{$i.type} </td>
                    <th><a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.removeItem&id={$i.id}', 'SP')">X</th>
                </tr>
            {/foreach}
            <tr style="background-color: {cycle values='#eeeeee, #dadada'}">
                <td><input type="checkbox" name="all" onClick="collector_switchAll('SP')"></th>
                <td align="center" colspan="2">
                    <select name="SP_multiaction">
                        <option>Multiple Action:</option>
                        <option onClick="collector_submit('SP', 'SP.removeItem')">Remove</option>
                        {if $_PL_activeId}
                            <option onClick="collector_submit('SP', 'PL.addItem')">Add to Playlist</option>
                        {else}
                            <option onClick="collector_submit('SP', 'PL.create')">New Playlist using Item</option>
                        {/if}
                    </select>
                    <script type="text/javascript">
                        document.forms['SP'].elements['SP_multiaction'].options[0].selected=true;
                    </script>
                </th>
                <td align="center" colspan="2"><a href="#" onClick="collector_clearAll('SP', 'SP.removeItem')">[Clear]</a></th>
            </tr>
        </table>
    </form>
{/if}
</div>
</center>
*}
