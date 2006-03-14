<div class="content">

<div class="container_elements" style="width: 607px;">
     <h1>##Backup management##</h1>
     
     {assign var=token' value=$EXCHANGE->getBackupToken()}
     
     {if $token === false}
        <input type="button" value="##Start backup##" onClick="location.href='{$UI_HANDLER}?act=EXCHANGE.createBackupOpen'">
        
     {else}
        {assign var='status' value=$EXCHANGE->createBackupCheck()}
        
        Backup status: {$status.status}
        
        {if $status.status === 'success'}
            <p><input type="button" class="button" value="##Download##" onCLick="hpopup('{$status.tmpfile}')"></p>
        {/if}
     
     {/if}
</div>


</div>

{assign var='status' value=null}