{assign var="_PL_activeId" value=$PL->getActiveId()}
{assign var="SCRATCHPAD" value=$SCRATCHPAD->get()}

<!-- start scratch pad -->
<form name="SP">
    <div class="container_elements side_elements">
        <h1>##ScratchPad##</h1>
        <div class="container_table">
            <table id="SP_table">

            <!-- start table header -->
                <tr class="blue_head">
                    <td><input type="checkbox" name="all" onClick="collector_switchAll('SP')"></td>
                    <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reorder&by=title', 'order');">##Title##</a></td>
                    <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reorder&by=title', 'order');">##Length##</td>
                    <td><a href="#" onClick="hpopup('{$UI_HANDLER}?act=SP.reorder&by=title', 'order');">##Type##</td>
                </tr>
            <!-- end table header -->

    {if count($SCRATCHPAD) >= 1}
        {foreach from=$SCRATCHPAD item=i}
                <!-- start item -->
                <tr class="{cycle values='blue1, blue2'}" id="scratchpad_item_{$i.id}">
                    <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                    <td {include file="scratchpad/actionhandler.tpl"}>
                        {if $i.type|lower == "playlist"}

                            {if $i.type == 'playlist' && $PL->isAvailable($i.id) == false}
                                <div class="active_pl">
                            {else}
                                <div>
                            {/if}
                                {$i.title|truncate:14:"...":true}
                                </div>
                        {else}
                            {$i.title|truncate:14:"...":true}
                        {/if}  {* for some reason object call doesn't like usage of array *}
                    </td>
                    {assign var="_duration" value=$i.duration}
                    <td {include file="scratchpad/actionhandler.tpl"}>{niceTime in=$_duration}</td>
                    <td {include file="scratchpad/actionhandler.tpl"}>
                        {if $i.type == 'playlist' && $PL->isAvailable($i.id) == false}
                            <div>
                            <img src="html/img/{$i.type}.png" border="0" alt="{$i.type|capitalize}" />
                            <img src="html/img/ico_lock.png">
                            </div>
                        {else}
                        <img src="html/img/{$i.type}.png" border="0" alt="{$i.type|capitalize}" /> {/if}
                    </td>
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

        <div class="footer">
            <select name="SP_multiaction" onChange="collector_submit('SP', this.value)">
                <option>##Multiple Action:##</option>
                <option value="SP.removeItem">##Remove files##</option>
                {if $_PL_activeId}
                    <option value="PL.addItem">##Add files to open Playlist##</option>
                {else}
                    <option value="PL.create">##New playlist using these files##</option>
                {/if}
            </select>
            <script type="text/javascript">
                // due to browser remembers filled form fields, here this is unwanted behavior
                document.forms['SP'].elements['SP_multiaction'].options[0].selected = true;
                document.forms['SP'].elements['all'].checked = false;
                collector_switchAll('SP');
            </script>
            <a href="#" onClick="collector_clearAll('SP', 'SP.removeItem')">##Clear##</a>
        </div>

    </div>
</form>
<!-- end scratch pad -->

{assign var="_PL_activeId" value=null}
{assign var="_duration"    value=null}