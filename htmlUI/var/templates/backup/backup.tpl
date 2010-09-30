<div class="content">

<div class="container_elements" style="width: 607px;">
<h1>##Backup management##</h1>
     
{if $EXCHANGE->getBackupToken() === false}
     
    <p><input type="button" class="button_large" value="##Start backup now##" onClick="location.href='{$UI_HANDLER}?act=BACKUP.createBackupOpen'"></p>
    <p><input type="button" class="button_large" value="##Schedule backup##" onClick="location.href='{$UI_BROWSER}?act=BACKUP.schedule'"></p>
            
{else}
    {assign var='status' value=$EXCHANGE->createBackupCheck()}
        
     Backup status: {$status.status}
        
    {if $status.status === 'success'}
        <p>
            <input type="button" class="button" value="##Download##" onCLick="location.href='{$UI_BROWSER}?popup[]=BACKUP.createBackupDownload'">
            <input type="button" class="button_large" value="##Copy backup file##" onClick="popup('{$UI_BROWSER}?popup[]=BACKUP.setLocation', 'BACKUP.selectLocation', 600, 600)">
            <input type="button" class="button" value="##Close backup##" onCLick="location.href='{$UI_HANDLER}?act=BACKUP.createBackupClose'">
    {/if}
     
{/if}

</div>
</div>

{assign var='status' value=null}        

{*
{$EXCHANGE->completeTarget()}
        {if $EXCHANGE->getPath() !== false}
            <p>
                <b>##Backup file:##</b>
                <br>
                {$EXCHANGE->getPath()}: 
                {if $EXCHANGE->checkTarget() === true}
                    ##OK##
                {else}
                    ##Permission denied##    
                {/if}
            </p>
        {/if}
        
*}