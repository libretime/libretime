<?php
    $tempConfigPath = "/etc/airtime/airtime.conf.tmp";
    if (file_exists($tempConfigPath)) {
        $airtimeConfig = parse_ini_file($tempConfigPath, true);
        $rmq = $airtimeConfig["rabbitmq"];
    }
?>

<form action="#" role="form" id="rmqSettingsForm">
    <h3 class="form-title">RabbitMQ Settings</h3>
    <span id="helpBlock" class="help-block help-message"></span>
    <p>
        RabbitMQ is an AMQP-based messaging system used by Libretime. You should only edit these settings
        if you have changed the defaults since running the installer, or if you've opted to install RabbitMQ manually.
    </p>
    <p>
        In either case, we recommend that you change at least the default password provided -
        you can do this by running the following line from the command line:<br/>
        <code>sudo rabbitmqctl change_password &lt;username&gt; &lt;newpassword&gt;</code><br/>
        <strong>Notice:</strong> using special characters such as ! in your rabbitmq password will cause LibreTime to fail
            to load properly after setup. Please use alphanumerical characters only.
    </p>
    <div id="rmqSlideToggle">
        <span><strong>Advanced </strong></span><span id="advCaret" class="caret"></span><hr/>
    </div>
    <div id="rmqFormBody">
        <div class="form-group">
            <label class="control-label" for="rmqUser">Username</label>
            <input required class="form-control" type="text" name="rmqUser" id="rmqUser" placeholder="Username" 
                value="<?php echo (isset($rmq) ? $rmq["user"] : "airtime"); ?>" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <div class="form-group">
            <label class="control-label" for="rmqPass">Password</label>
            <input class="form-control" type="password" name="rmqPass" id="rmqPass" placeholder="Password" 
                value="<?php echo (isset($rmq) ? $rmq["password"] : "airtime"); ?>" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
            <span id="rmqHelpBlock" class="help-block">
                You probably want to change this!
            </span>
        </div>
        <div class="form-group">
            <label class="control-label" for="rmqHost">Host</label>
            <input required class="form-control" type="text" name="rmqHost" id="rmqHost" placeholder="Host" 
                value="<?php echo (isset($rmq) ? $rmq["host"] : "127.0.0.1"); ?>" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <div class="form-group">
            <label class="control-label" for="rmqPort">Port</label>
            <input required class="form-control" type="text" name="rmqPort" id="rmqPort" placeholder="Port" 
                value="<?php echo (isset($rmq) ? $rmq["port"] : "5672"); ?>" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <div class="form-group">
            <label class="control-label" for="rmqVHost">Virtual Host</label>
            <input required class="form-control" type="text" name="rmqVHost" id="rmqVHost" placeholder="VHost" 
                value="<?php echo (isset($rmq) ? $rmq["vhost"] : "/airtime"); ?>" />
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        </div>
        <input class="form-control" type="hidden" name="rmqErr" id="rmqErr" aria-describedby="helpBlock"/>
    </div>
    <div>
        <input type="submit" formtarget="rmqSettingsForm" class="btn btn-primary btn-next" value="Next &#10097;"/>
        <input type="button" class="btn btn-primary btn-back" value="&#10096; Back"/>
    </div>
</form>

<script>
    $("#rmqSlideToggle").click(function() {
        $("#rmqFormBody").slideToggle(500);
        $("#advCaret").toggleClass("caret-up");
    });

    $("#rmqSettingsForm").submit(function(e) {
        submitForm(e, "RabbitMQSetup");
    });
</script>
