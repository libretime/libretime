<?php
?>

<form action="#" role="form" id="generalSettingsForm">
    <h3 class="form-title">General Settings</h3>
    <p>
        These values are automatically pulled from your webserver settings, under most circumstances you will not need to change them.
    </p>
    <span id="helpBlock" class="help-block help-message"></span>
    <div id="generalFormBody">
        <div class="form-group">
            <label class="control-label" for="generalHost">Webserver Host</label>
            <input required class="form-control" type="text" name="generalHost" id="generalHost" placeholder="Host" value="<?=$_SERVER["SERVER_NAME"]?>"/>
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <div class="form-group">
            <label class="control-label" for="generalPort">Webserver Port</label>
            <input required class="form-control" type="text" name="generalPort" id="generalPort" placeholder="Port" value="<?=$_SERVER["SERVER_PORT"]?>"/>
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <input class="form-control" type="hidden" name="generalErr" id="generalErr" aria-describedby="helpBlock"/>
        <p>The CORS URL can be setup during install if you are accessing your LibreTime instance behind a Proxy.
            This is common with docker setups. If you have a reverse proxy setup enter the URL below, otherwise you
            can safely ignore this.
        </p>
        <div id="corsSlideToggle">
            <span><strong>CORS URL </strong></span><span id="corsCaret" class="caret"></span><hr/>
        </div>
        <div id="corsFormBody">
            <div class="form-group">
                <label class="control-label" for="corsURL">CORS URLs</label>
                <textarea class="form-control" id="corsURL" rows="4" cols="50">
                </textarea>
            </div>
        </div>
    </div>
    <div>
        <input type="submit" formtarget="generalSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;"/>
        <input type="button" class="btn btn-primary btn-back" value="&#10096; Back"/>
    </div>
</form>

<script>
    $("#corsSlideToggle").click(function() {
        $("#corsFormBody").slideToggle(500);
        $("#corsCaret").toggleClass("caret-up");
    });
    $("#generalSettingsForm").submit(function(e) {
        submitForm(e, "GeneralSetup");
    });
</script>
