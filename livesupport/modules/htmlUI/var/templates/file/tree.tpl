{*Smarty template*}

<div id="tree">

{if is_array($structure.treedata)}
    {foreach from=$structure.treedata item=o}
        <div style="background-color: {cycle values="#eeeeee,#dadada"}">
        {$structure.treedata.type}
        {if $structure.treedata.tree}
            {str_repeat str='&nbsp;' count=3}
        {else}
            {str_repeat str='&nbsp;&nbsp;' count=$o.level}
        {/if}
        {if $o.type == 'Folder'}
            <a href="{$UI_BROWSER}?act=fileBrowse&id={$o.id}">[{$o.name}]</a>
        {else}
            {$o.name}
        {/if}
        <br>
        </div>
    {/foreach}
{/if}

</div>