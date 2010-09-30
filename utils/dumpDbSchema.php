<?
require_once('conf.php');
require_once("$STORAGE_SERVER_PATH/var/conf.php");
header("Conten-type: text/plain");
$dbname = $CC_CONFIG['dsn']['database'];
$dbuser = $CC_CONFIG['dsn']['username'];
$dbhost = $CC_CONFIG['dsn']['hostspec'];
$res = `pg_dump -s $dbname -U $dbuser`;
echo "$res\n";
?>