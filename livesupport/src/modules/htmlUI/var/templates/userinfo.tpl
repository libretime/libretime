
{if $USER.userid}
    <div class="loginname">
    <div id="nav">
    ##Signed in## : {$USER.login}
    &nbsp;
    <input type="button" class="button" value="##logout##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=logout", "name", "420", "50");'/>
    {*
    <input type="button" class="button" value="{##sign over##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=signover_1", "name", "420", "150");'/>
    *}
    &nbsp;
    </div>
    </div>
{else}
    <div id="nav">&nbsp;
    <input type="button" class="button" value="##login##" onClick='javascript: popup("{$UI_BROWSER}?popup[]=login", "name", "420", "150");'/>
    </div>
{/if}

