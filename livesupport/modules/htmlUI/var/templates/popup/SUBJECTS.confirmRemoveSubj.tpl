{include file="popup/header.tpl"}

<center>
{tra 0='Are you sure to remove "$1"?' 1=$_REQUEST.login}
<br><br>
<input type="button" class="button" onClick="window.close()" value="Cancel">
<input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=SUBJECTS.removeSubj&login={urlencode str=$_REQUEST.login}'" value="OK">
</center>

</body>
</html>
