{*Smarty template*}
&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="{$UI_BROWSER}?act=fileList&id={$structure.id}&tree=Y">##Tree view##</a>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    ##Folder Structure##:
    {foreach from=$structure.pathdata item=o}
        {if $o.type == 'Folder'}
            <a href="{$UI_BROWSER}?act=fileList&id={$o.id}">##{$o.name}##</a> /
        {else}
            ##{$o.name}##
        {/if}
    {/foreach}
<a href="javascript:newFolder()">##new&nbsp;folder##</a>
