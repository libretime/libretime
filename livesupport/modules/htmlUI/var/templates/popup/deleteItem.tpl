{include file="popup/header.tpl"} 

<center>
<b>Are you sure to delete this Item?</b>
<br>
<input type="button" onClick="window.close()" value="Cancel">
<input type="button" onClick="location.href='{$UI_HANDLER}?act=delete&id={$id}'" value="OK">
</center>

</body>
</html>
