<?php
    $tempConfigPath = "/etc/airtime/airtime.conf.tmp";
    if (file_exists($tempConfigPath)) {
        $airtimeConfig = parse_ini_file($tempConfigPath, true);
        $db = $airtimeConfig["database"];
    }
?>

<form action="#" role="form" id="dbSettingsForm">
    <h3 class="form-title">Database Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        Enter your Airtime database settings here. Empty or non-existent databases will be created and populated 
        if the given user has administrative permissions in postgres.
    </p>
    <div class="form-group">
        <label class="control-label" for="dbUser">Username</label>
        <input required class="form-control" type="text" name="dbUser" id="dbUser" placeholder="Username" 
            value="<?php echo (isset($db) ? $db["dbuser"] : "airtime"); ?>" />
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbPass">Password</label>
        <input required class="form-control" type="password" name="dbPass" id="dbPass" placeholder="Password" 
            value="<?php echo (isset($db) ? $db["dbpass"] : "airtime"); ?>" />
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbName">Name</label>
        <input required class="form-control" type="text" name="dbName" id="dbName" placeholder="Name" 
            value="<?php echo (isset($db) ? $db["dbname"] : "airtime"); ?>" />
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <div class="form-group">
        <label class="control-label" for="dbHost">Host</label>
        <input required class="form-control" type="text" name="dbHost" id="dbHost" placeholder="Host" 
            value="<?php echo (isset($db) ? $db["host"] : "localhost"); ?>" />
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    </div>
    <input class="form-control" type="hidden" name="dbErr" id="dbErr" aria-describedby="helpBlock"/>
    <div>
        <p style="text-align:right">
            This may take up to 30 seconds to complete!
        </p>
        <input type="submit" formtarget="dbSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;"/>
    </div>
</form>

<script>
    $("#dbSettingsForm").submit(function(e) {
        submitForm(e, "DatabaseSetup");
    });
</script>