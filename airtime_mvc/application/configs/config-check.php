<?php
/*
 * We only get here after setup, or if there's an error in the configuration.
 *
 * Display a table to the user showing the necessary dependencies
 * (both PHP and binary) and the status of any application services,
 * along with steps to fix them if they're not found or misconfigured.
 */

$phpDependencies    = checkPhpDependencies();
$externalServices   = checkExternalServices();
$postgres           = $phpDependencies["postgres"];

$database           = $externalServices["database"];
$rabbitmq           = $externalServices["rabbitmq"];

$pypo               = $externalServices["pypo"];
$liquidsoap         = $externalServices["liquidsoap"];
$analyzer           = $externalServices["analyzer"];
$celery             = $externalServices['celery'];
$api                = $externalServices['api'];

$r1 = array_reduce($phpDependencies, "booleanReduce", true);
$r2 = array_reduce($externalServices, "booleanReduce", true);
$result = $r1 && $r2;
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
        <link rel="stylesheet" type="text/css" href="css/setup/config-check.css">
    </head>
    <style>
        /*
            This is here because we're using the config-check css for
            both this page and the system status page
         */
        html {
            background-color: #f5f5f5;
        }

        body {
            padding: 2em;
            min-width: 600px;
            text-align: center;
            margin: 3em ;
            border: 1px solid lightgray;
            border-radius: 5px;
        }
    </style>

    <body>
        <h2>
            <img class="logo" src="css/images/airtime_logo_jp.png" /><br/>
            <strong>Configuration Checklist</strong>
        </h2>

        <?php
        if (!$result) {
            ?>
            <br/>
            <h3 class="error">Looks like something went wrong!</h3>
            <p>
                Take a look at the checklist below for possible solutions. If you're tried the suggestions and are
                still experiencing issues, read the
                <a href="https://github.com/LibreTime/libretime/releases">release notes</a>,
                come <a href="https://discourse.libretime.org/">visit our discourse</a>
                or, check <a href="http://www.libretime.org/">the website and main docs</a>.
            </p>
        <?php
        } else {
            ?>
            <p>
                Your Airtime station is up and running! Get started by logging in with the default username and password: admin/admin
            </p>
            <button onclick="location = location.pathname;">Log in to Airtime!</button>
        <?php
        }
        ?>


        <table class="table">
            <thead>
                <tr>
                    <th class="component">
                        Component
                    </th>
                    <th class="description">
                        <strong>Description</strong>
                    </th>
                    <th class="solution">
                        <strong>Status or Solution</strong>
                    </th>
                </tr>
            </thead>
        </table>

        <div class="checklist">
            <table class="table table-striped">
                <caption class="caption">
                    PHP Dependencies
                </caption>
                <tbody>
                    <tr class="<?=$postgres ? 'success' : 'danger';?>">
                        <td class="component">
                            Postgres
                        </td>
                        <td class="description">
                            PDO and PostgreSQL libraries
                        </td>
                        <td class="solution <?php if ($postgres) {echo 'check';?>">
                            <?php
                                } else {
                                    ?>">
                                    Try running <code>sudo apt-get install php5-pgsql</code>
                                <?php
                                }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-striped">
                <caption class="caption">
                    External Services
                </caption>
                <tbody>
                <tr class="<?=$database ? 'success' : 'danger';?>">
                    <td class="component">
                        Database
                    </td>
                    <td class="description">
                        Database configuration for Airtime
                    </td>
                    <td class="solution <?php if ($database) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Make sure you aren't missing any of the Postgres dependencies in the table above.
                            If your dependencies check out, make sure your database configuration settings in
                            <code>/etc/airtime.conf</code> are correct and the Airtime database was installed correctly.
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$rabbitmq ? 'success' : 'danger';?>">
                    <td class="component">
                        RabbitMQ
                    </td>
                    <td class="description">
                        RabbitMQ configuration for Airtime
                    </td>
                    <td class="solution <?php if ($rabbitmq) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Make sure RabbitMQ is installed correctly, and that your settings in /etc/airtime/airtime.conf
                            are correct. Try using <code>sudo rabbitmqctl list_users</code> and <code>sudo rabbitmqctl list_vhosts</code>
                            to see if the airtime user (or your custom RabbitMQ user) exists, then checking that
                            <code>sudo rabbitmqctl list_exchanges</code> contains entries for airtime-pypo and airtime-uploads.
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$analyzer ? 'success' : 'danger';?>">
                    <td class="component">
                        Media Analyzer
                    </td>
                    <td class="description">
                        <?php echo _("LibreTime media analyzer service") ?>
                    </td>
                    <td class="solution <?php if ($analyzer) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            <?php echo _("Check that the libretime-analyzer service is installed correctly in ") ?><code>/etc/systemd/system/</code>,
                            <?php echo _(" and ensure that it's running with ") ?>
                            <br/><code>systemctl status libretime-analyzer</code><br/>
                            <?php echo _("If not, try ") ?><br/><code>sudo systemctl restart libretime-analyzer</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$pypo ? 'success' : 'danger';?>">
                    <td class="component">
                        Pypo
                    </td>
                    <td class="description">
                        <?php echo _("LibreTime playout service") ?>
                    </td>
                    <td class="solution <?php if ($pypo) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            <?php echo _("Check that the libretime-playout service is installed correctly in ") ?><code>/etc/systemd/system/</code>,
                            <?php echo _(" and ensure that it's running with ") ?>
                            <br/><code>systemctl status libretime-playout</code><br/>
                            <?php echo _("If not, try ") ?><br/><code>sudo systemctl restart libretime-playout</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$liquidsoap ? 'success' : 'danger';?>">
                    <td class="component">
                        Liquidsoap
                    </td>
                    <td class="description">
                        <?php echo _("LibreTime liquidsoap service") ?>
                    </td>
                    <td class="solution <?php if ($liquidsoap) {echo 'check';?>" >
                        <?php
                        } else {
                            ?>">
                            <?php echo _("Check that the libretime-liquidsoap service is installed correctly in ") ?><code>/etc/systemd/system/</code>,
                            <?php echo _(" and ensure that it's running with ") ?>
                            <br/><code>systemctl status libretime-liquidsoap</code><br/>
                            <?php echo _("If not, try ") ?><br/><code>sudo systemctl restart libretime-liquidsoap</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$celery ? 'success' : 'danger';?>">
                    <td class="component">
                        Celery
                    </td>
                    <td class="description">
                        <?php echo _("LibreTime Celery Task service") ?>
                    </td>
                    <td class="solution <?php if ($celery) {echo 'check';?>" >
                        <?php
                        } else {
                            ?>">
                            <?php echo _("Check that the libretime-celery service is installed correctly in ") ?><code>/etc/systemd/system/</code>,
                            <?php echo _(" and ensure that it's running with ") ?>
                            <br/><code>systemctl status libretime-celery</code><br/>
                            <?php echo _("If not, try ") ?><br/><code>sudo systemctl restart libretime-celery</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$api ? 'success' : 'danger';?>">
                    <td class="component">
                        API
                    </td>
                    <td class="description">
                        <?php echo _("LibreTime API service") ?>
                    </td>
                    <td class="solution <?php if ($api) {echo 'check';?>" >
                        <?php
                        } else {
                            ?>">
                            <?php echo _("Check that the libretime-api service is installed correctly in ") ?><code>/etc/init.d/</code>,
                            <?php echo _(" and ensure that it's running with ") ?>
                            <br/><code>systemctl status libretime-api</code><br/>
                            <?php echo _("If not, try ") ?><br/><code>sudo systemctl restart libretime-api</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <h3>
                PHP Extension List
            </h3>
            <p>
                <?php
                    global $extensions;
                    $first = true;
                    foreach ($extensions as $ext) {
                        if (!$first) {
                            echo " | ";
                        } else {
                            $first = false;
                        }
                        echo $ext;
                    }
                ?>
            </p>
        </div>
