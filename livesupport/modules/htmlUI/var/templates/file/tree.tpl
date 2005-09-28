{*Smarty template*}
{if is_array($structure.treedata)}
    <div class="head" style="width:555px; height: 21px;">&nbsp;
</div>
    <div class="container_table" style="width: 555px; height: auto;">
<table>
    <tr class="blue_head">
            <td style="width: 555px;border: 0">##Item##</td>
    </tr>

    {foreach from=$structure.treedata item=o}
    
    <tr class="{cycle values='blue1, blue2'}">
      <td style="border: 0">
        {$structure.treedata.type}
        {if $structure.treedata.tree}
            {str_repeat str='&nbsp;' count=3}
        {else}
            {str_repeat str='&nbsp;&nbsp;' count=$o.level}
        {/if}
        {if $o.type == 'Folder'}
            <a href="{$UI_BROWSER}?act=fileList&id={$o.id}">[{$o.name}]</a>
        {else}
            {$o.name}
        {/if}
        </td>
    </tr>
    {/foreach}
</table>
{/if}
</div>