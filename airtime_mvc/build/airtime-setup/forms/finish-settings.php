<?php
?>

<form action="#" role="form" id="finishSettingsForm">
    <h3 class="form-title">Media Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        Looks like you're almost done! Click "Done!" to bring up the Airtime configuration checklist; if
        your configuration is all green, you're ready to get started with your personal Airtime station!
    </p>
    <div>
        <input type="submit" formtarget="finishSettingsForm" class="btn btn-primary btn-next" value="Done!"/>
        <input type="button" class="btn btn-primary btn-back" value="&#10096; Back"/>
    </div>
</form>

<script>
    $("#finishSettingsForm").submit(function(e) {
        submitForm(e, "FinishSetup");
    });
</script>