<?php
?>

<form action="#" role="form" id="finishSettingsForm">
    <h3 class="form-title">Setup Complete!</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        Looks like you're almost done! As a final step, run the following commands from the terminal:<br/>
        <code>sudo service airtime-playout start</code><br/> 
        <code>sudo service airtime-liquidsoap start</code><br/>
        <code>sudo service airtime-media-monitor start</code>
    </p>
    <p>
        Click "Done!" to bring up the Airtime configuration checklist; if your configuration is all green, 
        you're ready to get started with your personal Airtime station!
    </p>
    <p>
        If you need to re-run the web installer, just remove <code>/etc/airtime/airtime.conf</code>.
    </p>
    <div>
        <input type="submit" formtarget="finishSettingsForm" class="btn btn-primary btn-next" value="Done!"/>
    </div>
</form>

<script>
    $(document).ready(function() {
        submitForm(e, "FinishSetup");
    });
    
    $("#finishSettingsForm").submit(function(e) {
        window.location.replace("/?config");
    });
</script>