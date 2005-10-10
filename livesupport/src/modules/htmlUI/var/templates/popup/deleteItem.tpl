{include file="popup/header.tpl"}

<center>

{if $filecount}
    {tra str='Are you sure to delete $1 selected files?' 1=$filecount}
    <br><br>
    <input type="button" class="button" onClick="window.close()" value="Cancel">
    <input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=delete{$idstr}'" value="OK">
{else}
    {tra str='Are you sure to delete file "$1"?' 1=$filename}
    <br><br>
    <input type="button" class="button" onClick="window.close()" value="Cancel">
    <input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=delete&id={$_REQUEST.id}'" value="OK">
{/if}

</center>

</body>
</html>
