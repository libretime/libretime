<?php
/*
 * We only get here after setup, or if there's an error in the configuration.
 *
 * Display a table to the user showing the necessary dependencies
 * (both PHP and binary) and the status of any application services,
 * along with steps to fix them if they're not found or misconfigured.
 */

$phpDependencies = airtimeCheckDependencies();
$zend = $phpDependencies["zend"];
$database = airtimeCheckDatabase();

$result = $database;

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="bootstrap.3.3.1.min.css">
    </head>

    <body style="padding: 2em 0; min-width: 500px; width: 50%; text-align: center; margin: 0 auto;">

        <h1>
            Airtime Configuration Checklist
        </h1>
        <table class="table table-striped" style="text-align: center">
            <caption>
                Airtime Configuration
            </caption>
            <thead>
                <tr>
                    <td>
                        Component
                    </td>
                    <td>
                        Description
                    </td>
                    <td>
                        Solution
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr class="<?=$zend ? 'success' : 'danger';?>">
                    <td>
                        <strong>Zend</strong>
                    </td>
                    <td>
                        PHP MVC Framework
                    </td>
                    <td <?php if ($zend) {echo 'style="background: #dff0d8 url(css/images/accept.png) no-repeat center"';?>>
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
                <tr class="<?=$database ? 'success' : 'danger';?>">
                    <td>
                        <strong>Database</strong>
                    </td>
                    <td>
                        PostgreSQL data store for Airtime
                    </td>
                    <td <?php if ($database) {echo 'style="background: #dff0d8 url(css/images/accept.png) no-repeat center"';?>>
                        <?php
                            } else {
                                ?>>
                                Try running <code>sudo apt-get install php5-pgsql php5-mysql</code>
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
    }
?>
        <br/>
        <h3>
            PHP Extension List
        </h3>
        <p>
            <?php

                foreach (get_loaded_extensions() as $ext) {
                    echo $ext . " | ";
                }

            ?>
        </p>
<?php