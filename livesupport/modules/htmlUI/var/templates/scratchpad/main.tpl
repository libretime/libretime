{assign var="_PL_activeId" value=$PL->getActiveId()}
{assign var="SCRATCHPAD"   value=$SCRATCHPAD->get()}

<!-- start scratch pad -->
<form name="SP">
    <div class="container_elements">
        <h1>##ScratchPad##</h1>
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

    {if count($SCRATCHPAD) >= 1}
        {foreach from=$SCRATCHPAD item=i}
                <!-- start item -->
                <tr class="{cycle values='blue1, blue2'}">
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td {include file="scratchpad/actionhandler.tpl"}>
                        {if $i.type === "playlist"}
                            {if $PL->isAvailable($i.id) === FALSE}
                                <div style="text-decoration : line-through">
                            {else}
                                <div>
                            {/if}
                            {if $_PL_activeId === $i.id}
                                <div style="font-weight : bold">
                            {else}
                                <div>
                            {/if}
                                {$i.title|truncate:12}
                            </div></div>
                        {else}
                            {$i.title|truncate:12}
                        {/if}
                    </td>
                    <td {include file="scratchpad/actionhandler.tpl"}>{$i.duration}</td>
                    <td {include file="scratchpad/actionhandler.tpl"} style="border: 0"><img src="img/{$i.type}.gif" border="0" alt="{$i.type|capitalize}" /></td>
                </tr>
                <!-- end item -->
        {/foreach}
    {else}
        <tr class="blue1">
            <td style="border: 0" colspan="4" align="center">##empty##</td>
        </tr>
    {/if}
            </table>
        </div>

        <div class="footer" style="width:250px;">
            <select name="SP_multiaction" onChange="collector_submit('SP', this.value)">
                <option>##Multiple Action:##</option>
                <option value="SP.removeItem">##Remove file(s)##</option>
                {if $_PL_activeId}
                    <option value="PL.addItem">##Add file(s) to active Playlist##</option>
                {else}
                    <option value="PL.create">##New Playlist using this file(s)##</option>
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
<!-- end scratch pad -->

{assign var="_PL_activeId" value=NULL}
