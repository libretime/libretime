{include file="popup/header.tpl"}

<center>
<b>##Are you sure to discard all changes?##</b>
<br><br>
<input type="button" class="button" onClick="window.close()" value="Cancel">
<input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=PL.revert'" value="OK">
</center>

</body>
</html>
