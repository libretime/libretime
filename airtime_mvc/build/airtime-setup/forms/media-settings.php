<?php
?>

<form action="#" role="form" id="mediaSettingsForm">
    <h3 class="form-title">Media Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        Here you can set the default media storage directory for Airtime. If left blank, we'll create a new
        directory located at <code>/srv/airtime/stor/</code> for you.
    </p>
    <div class="form-group">
        <label class="control-label" for="mediaFolder">Media folder</label>
        <input class="form-control" type="text" name="mediaFolder" id="mediaFolder" placeholder="/path/to/my/airtime/music/directory/"/>
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        <span id="mediaHelpBlock" class="help-block">
            Note that you need to enter the <strong>fully qualified</strong> path name!
        </span>
    </div>
    <input class="form-control" type="hidden" name="mediaErr" id="mediaErr" aria-describedby="helpBlock"/>
    <div>
        <input type="submit" formtarget="mediaSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;"/>
        <input type="button" class="btn btn-primary btn-back" value="&#10096; Back"/>
    </div>
</form>

<script>
    $("#mediaSettingsForm").submit(function(e) {
        submitForm(e, "MediaSetup");
    });
</script>