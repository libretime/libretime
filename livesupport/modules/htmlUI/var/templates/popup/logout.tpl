{include file="popup/header.tpl"}

<center>
<b>{tra 0='Are you sure to logout $1?' 1=$USER.login}</b>
<br><br>
<input type="button" class="button" value="{tra 0=Cancel}" onclick="javascript: window.close()">
<input type="button" class="button" value="{tra 0=OK}" onclick="javascript: location.href='{$UI_HANDLER}?act={$logouttype}'">&nbsp;
</center>


</body>
</html>
