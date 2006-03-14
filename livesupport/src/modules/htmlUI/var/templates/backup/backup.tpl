<div class="content">

<div class="container_elements" style="width: 607px;">
     <h1>##Backup management##</h1>
     
     {assign var=token' value=$EXCHANGE->getBackupToken()}
     
     {if $token === false}
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
        
        {if $EXCHANGE->checkTarget() === true} 
            <p><input type="button" class="button_large" value="##Start backup##" onClick="location.href='{$UI_HANDLER}?act=EXCHANGE.createBackupOpen'"></p>
        {/if}
        
        <p><input type="button" class="button_large" value="##Set backup location##" onClick="popup('{$UI_BROWSER}?popup[]=BACKUP.setLocation', 'BACKUP.selectLocation', 500, 400)"></p>
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