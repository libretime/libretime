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

        <a href="{$UI_BROWSER}?id={$o.id}">[{$o.name}]</a><br>
        </div>
    {/foreach}
{/if}

</div>
