{*Smarty template*}

<div id="path">
    <a href="{$UI_BROWSER}?act=fileList&id={$structure.id}&tree=Y" class="button">[Tree view]</a>&nbsp;&nbsp;|&nbsp;
    {foreach from=$structure.pathdata item=o}
        {if $o.type == 'Folder'}
            <a href="{$UI_BROWSER}?act=fileList&id={$o.id}">[{$o.name}]</a> /
        {else}
            {$o.name}
        {/if}
    {/foreach}
<a href="javascript:newFolder()">[::new&nbsp;folder::]</a>
</div>
