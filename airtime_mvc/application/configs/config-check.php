<?php
/*
 * We only get here after setup, or if there's an error in the configuration.
 *
 * Display a table to the user showing the necessary dependencies
 * (both PHP and binary) and the status of any application services,
 * along with steps to fix them if they're not found or misconfigured.
 */

$phpDependencies = checkPhpDependencies();
$zend = $phpDependencies["zend"];
$postgres = $phpDependencies["postgres"];
$database = checkDatabaseConfiguration();

function booleanReduce($a, $b) {
    return $a && $b;
}

$r = array_reduce($phpDependencies, "booleanReduce", true);
$result = $r && $database;

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
        <link rel="stylesheet" type="text/css" href="css/setup/config-check.css">
    </head>

    <body>
        <h2>
            <img class="logo" src="css/images/airtime_logo_jp.png" /><br/>
            <strong>Configuration Checklist</strong>
        </h2>
        <table class="table">
            <thead>
                <tr>
                    <td class="component">
                        Component
                    </td>
                    <td class="description">
                        <strong>Description</strong>
                    </td>
                    <td class="solution">
                        <strong>Solution</strong>
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
                        <td class="solution <?php if ($zend) {echo 'check';?>">
                            <?php
                                } else {
                                    ?>">
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
                </tbody>
            </table>
        </div>
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
        <button onclick="location = location.pathname;">Log in to Airtime!</button>
    <?php
    }
?>
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
