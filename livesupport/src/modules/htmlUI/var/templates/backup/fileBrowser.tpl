{include file="popup/header.tpl"}

<script language='javascript'>
{literal}
if (window.scrollbars.visible == false) {
    window.scrollbars.visible=true;
    location.reload();
}
{/literal}
{if $EXCHANGE->errorMsg}
    alert("{$EXCHANGE->errorMsg|escape:'quotes'}");
{/if}
</script>

<table height="100%" width="100%">
    <tr>
        <td>
            <center>
                <table width="100%" height="100%">
                    <tr><td colspan="4"><h1>##File browser##</h1></td></tr>
                    <tr><td colspan="4" style="white-space: nowrap;">
                    <form name="filebrowser">
                        ##Filename:## <br>
                        <input type='text' name='target' size='50' value='{$EXCHANGE->getPath()}'>
                        <input type='button' class='button' value='##OK##' onClick="opener.location.href='{$UI_BROWSER}?act=BACKUP.setTarget&target='+filebrowser.target.value; window.close()">
                    </form>
                    </td></tr> 
{assign var='currdir' value=$EXCHANGE->listFolder()}

{foreach from=$currdir.subdirs item=entry key=name}
    <tr>
        <td><b><a href="{$UI_BROWSER}?popup[]=BACKUP.setLocation&cd={$name|escape:"url"}">{$name}</b></td>
        <td>{if $entry.r}r{/if}</td>
        <td>{if $entry.w}w{/if}</td>
        <td>{if $entry.x}x{/if}</td>
    </tr>
{/foreach} 


{foreach from=$currdir.files item=entry key=name}
    <tr>
        <td><a href="{$UI_BROWSER}?popup[]=BACKUP.setFile&file={$name|escape:"url"}">{$name}</td>
        <td>{if $entry.r}r{/if}</td>
        <td>{if $entry.w}w{/if}</td>
        <td>{if $entry.x}x{/if}</td>
    </tr>
{/foreach}
                    
                </table>
            </center>
        </td>
    </tr>
</table>

</body>
</html>