<?php
?>

<form action="#" role="form" id="dbSettingsForm">
    <h3 class="form-title">Database Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <div class="form-group">
        <label class="control-label" for="dbUser">Username</label>
        <input required class="form-control" type="text" name="dbUser" id="dbUser" placeholder="Username" value="airtime"/>
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbPass">Password</label>
        <input required class="form-control" type="password" name="dbPass" id="dbPass" placeholder="Password" value="airtime"/>
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbName">Name</label>
        <input required class="form-control" type="text" name="dbName" id="dbName" placeholder="Name" value="airtime"/>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbHost">Host</label>
        <input required class="form-control" type="text" name="dbHost" id="dbHost" placeholder="Host" value="localhost"/>
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <input class="form-control" type="hidden" name="dbErr" id="dbErr" aria-describedby="helpBlock"/>
    <div>
        <input type="submit" formtarget="dbSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;"/>
        <input type="button" class="btn btn-default btn-skip" value="Skip this step &#10097;"/>
    </div>
</form>

<script>
    $("#dbSettingsForm").submit(function(e) {
        submitForm(e, "DatabaseSetup");
    });
</script>