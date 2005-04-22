{include file="popup/header.tpl"}

<center>
{tra 0='Are you sure to delete file "$1"?' 1=$filename}
<br><br>
<input type="button" class="button" onClick="window.close()" value="Cancel">
<input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=delete&id={$id}'" value="OK">
</center>

</body>
</html>
