{if $USER.userid}
    <div class="loginname">##Signed in## : {$USER.login}</div>
    <input type="button" class="button" value="##logout##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=logout", "name", "420", "50");'/>
    {*
    <input type="button" class="button" value="{##sign over##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=signover_1", "name", "420", "150");'/>
    *}
{else}
    <input type="button" class="button" value="##login##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=login", "name", "420", "150");'/>
{/if}
