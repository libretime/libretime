{*Smarty template*}

{if $USER.userid}
    Login: {$USER.login}
    <br>
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=logout", "name", "350", "100");'>[logout]</a>
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=signover_1", "name", "350", "160");'>[sign over]</a>
{else}
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=login", "name", "350", "160");'>[login]</a>
{/if}

