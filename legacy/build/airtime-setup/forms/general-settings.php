<?php
?>

<form action="#" role="form" id="generalSettingsForm">
    <h3 class="form-title">General Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <div id="generalFormBody">
        <div class="form-group">
            <label class="control-label" for="publicUrl">Public URL</label>
            <input required class="form-control" type="text" name="publicUrl" id="publicUrl" placeholder="https://example.com/" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <input class="form-control" type="hidden" name="generalErr" id="generalErr" aria-describedby="helpBlock" />
        <hr />
        <p>
            The CORS URL can be setup during install if you are accessing your LibreTime instance behind a Proxy.
            This is common with docker setups. If you have a reverse proxy setup enter the URL below, otherwise you
            can safely ignore this. Please enter one URL per line. Include the entire URL such as http://example.com
            If you are reinstalling LibreTime on an existing setup you can ignore this as well,
            the settings in your existing database will be retained unless you enter new values below.
        </p>
        <div class="form-group">
            <label class="control-label" for="corsUrl">CORS URLs</label>
            <textarea name="corsUrl" class="form-control" id="corsUrl" rows="4" cols="50"></textarea>
        </div>
    </div>
    <div>
        <input type="submit" formtarget="generalSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;" />
        <input type="button" class="btn btn-primary btn-back" value="&#10096; Back" />
    </div>
</form>

<script>
    $("#publicUrl").val(function() {
        return window.location.href;
    });
    $("#corsUrl").text(function() {
        return window.location.origin;
    });
    $("#generalSettingsForm").submit(function(e) {
        submitForm(e, "GeneralSetup");
    });
</script>
