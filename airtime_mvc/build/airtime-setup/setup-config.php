<?php

?>

<html style="background-color: #111141;">
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
        <script type="text/javascript" src="js/libs/jquery-1.8.3.min.js"></script>
    </head>
    <body style="background-color: #111141; color: white; padding: 2em 0; min-width: 400px; width: 30%; text-align: center; margin: 3em auto;">
        <img src="css/images/airtime_logo_jp.png" style="margin-bottom: .5em;" /><br/>
        <form action="#" role="form" style="width: 50%; margin: auto;" id="dbSettingsForm">
            <h3 style="margin: 1em 0;">Database Settings</h3>
            <div class="form-group">
                <label class="sr-only" for="dbUser">Database Username</label>
                <input required class="form-control" type="text" name="dbUser" id="dbUser" placeholder="Username"/>
            </div>
            <div class="form-group">
                <label class="sr-only" for="dbPass">Database Password</label>
                <input required class="form-control" type="password" name="dbPass" id="dbPass" placeholder="Password"/>
            </div>
            <div class="form-group">
                <label class="sr-only" for="dbName">Database Name</label>
                <input required class="form-control" type="text" name="dbName" id="dbName" placeholder="Name"/>
            </div>
            <div class="form-group">
                <label class="sr-only" for="dbHost">Database Host</label>
                <input required class="form-control" type="text" name="dbHost" id="dbHost" placeholder="Host" value="localhost"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default"/>
            </div>
        </form>

        <script>
            $("#dbSettingsForm").submit(function(e) {
                e.preventDefault();
                var d  = $('#dbSettingsForm').serializeArray();
                $.post('setup/functions.php?fn=airtimeValidateDatabaseSettings', d, function(data) {
                    console.log(data);
                }, "json");
            });
        </script>
    </body>
</html>