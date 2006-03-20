{include file="popup/header.tpl"}

<script language='javascript'>
{literal}
if (window.scrollbars.visible == false) {
    window.scrollbars.visible=true;
    location.reload();
}
{/literal}
</script>

<table height="100%" width="100%">
    <tr>
        <td>
            <center>
                <table width="100%" height="100%">
                    <tr><td colspan=""><h1>##File browser##</h1></td></tr>
                    <tr>
                        <td colspan="6" style="white-space: nowrap;">
                            <form name="filebrowser" onSubmit="opener.location.href='{$UI_BROWSER}?act=BACKUP.setTarget&target='+filebrowser.target.value; window.close()">
                                ##Filename:## <br>
                                <input type='text' name='target' size='50' value='{$EXCHANGE->getPath()}'>
                                <input type='submit' class='button' value='##OK##'>
                            </form>
                        </td>
                    </tr> 
{assign var='currdir' value=$EXCHANGE->listFolder()}

{foreach from=$currdir.subdirs item=entry key=name}
    <tr class="{cycle values='blue1, blue2'}">
        <td style="border-right: 1px solid #333;"><b><a href="{$UI_BROWSER}?popup[]=BACKUP.setLocation&cd={$name|escape:"url"}">{$name|truncate:50:"...":true}</b></td>
        <td style="border-right: 1px solid #333;">{$entry.u|truncate:10:'...':true}</td>
        <td style="border-right: 1px solid #333;">{$entry.g|truncate:10:'...':true}</td>
        <td style="border-right: 1px solid #333;">{if $entry.r}r{/if}</td>
        <td style="border-right: 1px solid #333;">{if $entry.w}w{/if}</td>
        <td>{if $entry.x}x{/if}</td>
    </tr>
{/foreach} 


{foreach from=$currdir.files item=entry key=name}
    <tr class="{cycle values='blue1, blue2'}">
        <td style="border-right: 1px solid #333;"><a href="{$UI_BROWSER}?popup[]=BACKUP.setFile&file={$name|escape:"url"}">{$name|truncate:50:"...":true}</td>
        <td style="border-right: 1px solid #333;">{$entry.u|truncate:10:'...':true}</td>
        <td style="border-right: 1px solid #333;">{$entry.g|truncate:10:'...':true}</td>
        <td style="border-right: 1px solid #333;">{if $entry.r}r{/if}</td>
        <td style="border-right: 1px solid #333;">{if $entry.w}w{/if}</td>
        <td>{if $entry.x}x{/if}</td>
    </tr>
{/foreach}
                    
                </table>
            </center>
        </td>
    </tr>
</table>

<script language="javascript">
{if $EXCHANGE->errorMsg}
    alert("{$EXCHANGE->errorMsg|escape:'quotes'}");
{/if}
</script>

</body>
</html>