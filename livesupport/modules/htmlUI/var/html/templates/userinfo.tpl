{*Smarty template*}

{if $user.userid}
    Login: {$user.login}
    <br>
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=logout", "name", "350", "100");'>[{tra 0=logout}]</a>
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=signover_1", "name", "350", "160");'>[{tra 0='sign over'}]</a>
{else}
    <a href='javascript: popup("{$UI_BROWSER}?popup[]=login", "name", "350", "160");'>{tra 0=login}</a>
{/if}

