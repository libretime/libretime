{include file="header.tpl"}

<center>
<b>Are you sure to remove this entry?</b>
<br>
<input type="button" onClick="window.close()" value="Cancel">
<input type="button" onClick="location.href='{$UI_HANDLER}?act=SCHEDULER.removeItem&gunid={$gunid}'" value="OK">
</center>

</body>
</html>
