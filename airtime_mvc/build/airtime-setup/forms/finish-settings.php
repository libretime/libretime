<?php
?>

<form action="#" role="form" id="finishSettingsForm">
    <h3 class="form-title">Manual Step: Start Airtime Services</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        Looks like you're almost done! As a final step, please run the following commands from the terminal:
    </p>
    <pre style="text-align: left">sudo service airtime-playout start
sudo service airtime-liquidsoap start
sudo service airtime_analyzer start
sudo service airtime-celery start</pre>
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
    $("#finishSettingsForm").submit(function(e) {
        e.preventDefault();
        window.location.assign("/?config");
    });
</script>