<?php
/*
 * We only get here after setup, or if there's an error in the configuration.
 *
 * Display a table to the user showing the necessary dependencies
 * (both PHP and binary) and the status of any application services,
 * along with steps to fix them if they're not found or misconfigured.
 */

$phpDependencies = airtimeCheckPhpDependencies();
$zend = $phpDependencies["zend"];
$postgres = $phpDependencies["postgres"];
$database = airtimeCheckDatabaseConfiguration();

function booleanReduce($a, $b) {
    return $a && $b;
}

$r = array_reduce($phpDependencies, "booleanReduce", true);
$result = $r && $database;

?>
<html style="background-color: #f5f5f5">
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
        <style>
            .checklist {
                overflow: auto;
                height: 50%;
            }

            .caption {
                padding-left: .5em;
            }

            .component {
                font-weight: bold;
                width: 20%;
            }

            .description, .solution {
                width: 40%;
            }

            .footer {
                margin: inherit;
                width: inherit;
                bottom: 0;
            }
        </style>
    </head>

    <body style="padding: 2em 0; min-width: 600px; width: 50%; text-align: center; margin: 3em auto;
                 border: 1px solid lightgray; border-radius: 5em;">
        <h2>
            <img src="css/images/airtime_logo_jp.png" style="margin-bottom: .5em;" /><br/>
            <strong>Configuration Checklist</strong>
        </h2>
        <table class="table" style="padding: 0; margin: 3em 0 0 0; font-weight: bold">
            <thead>
                <tr>
                    <td class="component">
                        Component
                    </td>
                    <td class="description">
                        Description
                    </td>
                    <td class="solution">
                        Solution
                    </td>
                </tr>
            </thead>
        </table>

        <div class="checklist">
            <table class="table table-striped">
                <caption class="caption">
                    PHP Dependencies
                </caption>
                <tbody>
                    <tr class="<?=$zend ? 'success' : 'danger';?>">
                        <td class="component">
                            Zend
                        </td>
                        <td class="description">
                            Zend MVC Framework
                        </td>
                        <td class="solution" <?php if ($zend) {echo 'style="background: #dff0d8 url(css/images/accept.png) no-repeat center"';?>>
                            <?php
                                } else {
                                    ?>>
                                    <b>Ubuntu</b>: try running <code>sudo apt-get install libzend-framework-php</code>
                                    <br/><b>Debian</b>: try running <code>sudo apt-get install zendframework</code>
                                <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <tr class="<?=$postgres ? 'success' : 'danger';?>">
                        <td class="component">
                            Postgres
                        </td>
                        <td class="description">
                            PDO and PostgreSQL libraries
                        </td>
                        <td class="solution" <?php if ($postgres) {echo 'style="background: #dff0d8 url(css/images/accept.png) no-repeat center"';?>>
                            <?php
                                } else {
                                    ?>>
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
                    <td class="solution" <?php if ($database) {echo 'style="background: #dff0d8 url(css/images/accept.png) no-repeat center"';?>>
                        <?php
                        } else {
                            ?>>
                            Make sure you aren't missing any of the Postgres dependencies in the table above.
                            If your dependencies check out, make sure your database configuration settings in
                            <code>airtime.conf</code> is correct and the Airtime database was installed correctly.
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
<?php
    if (!$result) {
        ?>
        <p>
            Looks like something went wrong! If you've tried everything we've recommended in the table above, come
            <a href="https://forum.sourcefabric.org/">visit our forums</a>
            or <a href="http://www.sourcefabric.org/en/airtime/manuals/">check out the manual</a>.
        </p>
    <?php
    } else {
        ?>
        <p>
            Your Airtime station is up and running!
        </p>
    <?php
    }
?>
        </div>

        <div class="footer">
            <h3>
                PHP Extension List
            </h3>
            <p>
                <?php
                    global $extensions;
                    foreach ($extensions as $ext) {
                        echo $ext . " | ";
                    }
                ?>
            </p>
        </div>
<?php