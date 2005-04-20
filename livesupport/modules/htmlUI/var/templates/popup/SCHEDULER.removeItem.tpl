{include file="popup/header.tpl"}

<center>
<b>{tra 0='Are you sure to remove playlist "$1"?' 1=$plname}</b>
<br><br>
<input type="button" class="button" onClick="window.close()" value="Cancel">
<input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=SCHEDULER.removeItem&scheduleId={$scheduleId}'" value="OK">
</center>

</body>
</html>
