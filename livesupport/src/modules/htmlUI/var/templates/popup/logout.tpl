{include file="popup/header.tpl"}

<center>
##Are you sure to logout?##
<br><br>
<input type="button" class="button" value="{tra 0=Cancel}" onclick="javascript: window.close()">
<input type="button" class="button" value="{tra 0=OK}" onclick="javascript: location.href='{$UI_HANDLER}?act={$logouttype}'">&nbsp;
</center>


</body>
</html>
