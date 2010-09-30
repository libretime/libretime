<div class="content">

<div class="container_elements" style="width: 607px;">
     <h1>##Restore backup##</h1>
     
     {assign var='token' value=$EXCHANGE->getRestoreToken()}
     
     {if $token === false}
        <input type="button" class="button_large" value="##Choose backup file##" onClick="popup('{$UI_BROWSER}?popup[]=BACKUP.setLocation&isRestore=1', 'RESTORE.backupFile', 600, 600)">
        
     {else}
        {assign var='status' value=$EXCHANGE->backupRestoreCheck()}
        
        ##Restore status##: {if $status.status=='fault'}<b style="font-color:red">{$status.status}!!!</b>{else}{$status.status}{/if}
        
        {if $status.status !== 'working'}
            <p><input type="button" class="button" value="##Close Backup Restore##" onCLick="location.href='{$UI_HANDLER}?act=RESTORE.backupRestoreClose'"></p>
        {/if}
     
     {/if}
     
     
</div>


</div>

{assign var='status' value=null}