{if $results}
{* has result *}
<meta http-equiv="refresh" content="0; URL=ui_browser.php?act={$_prefix}&trtokid={$trtokid}">
{elseif $notrtok} 
{* the request has'nt trtokid, then no criteria *}
<meta http-equiv="refresh" content="0; URL=ui_browser.php?act={$_prefix}">
{else}
{* simple not recived the result form the hub *}
<html>
<head><meta http-equiv="refresh" content="{$polling_frequency}; URL=ui_browser.php?popup[]={$_prefix}.getResults&trtokid={$trtokid}"></head>
<body><center><img src="img/ls_logo_animated.gif"></center></body>
</html>
{/if}
