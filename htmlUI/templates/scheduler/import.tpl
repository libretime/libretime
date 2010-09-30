<div class="content">

<div class="container_elements" style="width: 607px;">
     <h1>##Scheduler Import##</h1>
     
     {assign var='token' value=$SCHEDULER->getImportToken()}
     {if $token === false}
        <input type="button" class="button_large" value="##Choose export file##" onClick="popup('{$UI_BROWSER}?popup[]=BACKUP.setLocation&isRestore=scheduler', 'RESTORE.backupFile', 600, 600)">
        
     {else}
        {assign var='status' value=$SCHEDULER->scheduleImportCheck($SCHEDULER->getImportToken())}
        
        ##Import status##: {if $status.status=='fault'}<b style="font-color:red">{$status.status}!!!</b>{else}{$status.status}{/if}
        
        {if $status.status !== 'working'}
            <p><input type="button" class="button" value="##Close Import##" onCLick="location.href='{$UI_HANDLER}?act=SCHEDULE.scheduleImportClose'"></p>
        {/if}
     
     {/if}
     
     
</div>


</div>

{assign var='status' value=null}