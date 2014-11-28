<?php

?>

<html style="background-color:black;">
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
    </head>
    <body style="background-color:black;color:white;padding: 2em 0; min-width: 400px; width: 30%; text-align: center; margin: 3em auto;">
        <img src="css/images/airtime_logo_jp.png" style="margin-bottom: .5em;" /><br/>
        <form role="form">
            <h2>Database Settings</h2>
            <div class="form-group">
                <label class="sr-only" for="dbUser">Database Username</label>
                <input type="text"  id="dbUser" placeholder="Username"/>
                <label class="sr-only" for="dbPass">Database Password</label>
                <input type="password" id="dbPass" placeholder="Password"/>
                <label class="sr-only" for="dbName">Database Name</label>
                <input type="text" id="dbName" placeholder="Name"/>
                <label class="sr-only" for="dbHost">Database Host</label>
                <input type="text" id="dbHost" placeholder="Host" value="localhost"/>
                <input type="submit" class="btn btn-default"/>
            </div>
        </form>