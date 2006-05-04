{include file="popup/header.tpl"}

<center>
##Are you sure to cancel transfer(s)?##
<br><br>
<input type="button" class="button" onClick="window.close()" value="##Cancel##">
<input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=TR.cancelConfirm{$tansferIDs}'" value="##OK##">
</center>

</body>
</html>
