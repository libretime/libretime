{*Smarty template*}

<div id="path">

    <a href="{$UI_BROWSER}?id={$structure.id}&tree=Y" class="button">[Tree view]</a>&nbsp;&nbsp;|&nbsp;
    {foreach from=$structure.pathdata item=o}
        <a href="{$UI_BROWSER}?id={urlencode str=$o.id}">[{$o.name}]</a>
        {if ($o.type eq 'Folder')}
            <span class="slash b">/</span>
        {/if}
    {/foreach}
    <!--
    <span style="padding-left:6em">
        <a href="gbHtmlPerms.php?id={$id}" class="button">permissions</a>
    </span>   -->

</div>
