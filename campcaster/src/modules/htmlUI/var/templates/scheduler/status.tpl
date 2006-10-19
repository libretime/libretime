<div class="content" style="width: auto;">
<!-- start scheduler -->
    <div class="container_elements" style="width: 790px;">
        <div class="head_scheduler" style=""><h1>##Scheduler status##</h1></div>
        <div class="clearer">&nbsp;</div>
        

        {if $SCHEDULER->testDaemon() === true}
            <p>##Scheduler is running##</p>
            <p><input type="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.stopDaemon');" value="##Stop scheduler##"></p>
        {else}
            <p>##Scheduler is not running##</p>
            <p><input type="button" class="button" onClick="hpopup('{$UI_HANDLER}?act=SCHEDULER.startDaemon');" value="##Start scheduler##"></p>
        {/if}
        </p>
    </div>
</div>