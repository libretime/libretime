{*Smarty template*}

{if $USER.userid}
    <div class="loginname">##Signed in## : {$USER.login}</div>
    <input type="button" class="button" value="{tra 0=logout}" onClick='javascript: popup("{$UI_BROWSER}?popup[]=logout", "name", "350", "150");'/>
    {*
    <input type="button" class="button" value="{tra 0='sign over'}" onClick='javascript: popup("{$UI_BROWSER}?popup[]=signover_1", "name", "350", "150");'/>
    *}
{else}
    <input type="button" class="button" value="{tra 0=login}" onClick='javascript: popup("{$UI_BROWSER}?popup[]=login", "name", "350", "150");'/>
{/if}
