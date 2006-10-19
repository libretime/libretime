{include file="header.tpl"}
{include file="masterpanel.tpl"}
{include file="footer.tpl"}

{if $DEBUG}
    <a href="javascript: hpopup('{$UI_HANDLER}?act=SESSION.CLEAR')">clear session</a>
{/if}
