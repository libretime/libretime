<div class="content">

<div class="container_elements" style="width: 607px;">
<h1>##Scheduler Export##</h1>
     
{if $SCHEDULER->getExportToken() === false}
    <p>
    <form action="{$UI_HANDLER}?act=SCHEDULER.scheduleExportOpen" method="post">
    <label for="fromYear">From:</label> {html_select_date prefix="from" field_separator="/" start_year="-5" end_year="+3"}&nbsp;&nbsp;&nbsp;{html_select_time prefix="from" display_seconds="0"}<br>
	<label for="toYear">To:</label> {html_select_date prefix="to" field_separator="/" start_year="-5" end_year="+3"}&nbsp;&nbsp;&nbsp;{html_select_time prefix="to" display_seconds="0"}<br><br>
    <input type="submit" class="button_large" value="##Start export now##">
    </form></p>
            
{else}
    {assign var='status' value=$SCHEDULER->scheduleExportCheck()}
        
     Export status: {$status.status}
        
    {if $status.status === 'success'}
		Export file location: {$status.file}
        <p>
            <input type="button" class="button" value="##Close export##" onCLick="location.href='{$UI_HANDLER}?act=SCHEDULER.scheduleExportClose'">
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