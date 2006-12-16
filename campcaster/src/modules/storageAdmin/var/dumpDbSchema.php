<?
require_once('conf.php');
require_once("$storageServerPath/var/conf.php");
header("Conten-type: text/plain");
$dbname = $CC_CONFIG['dsn']['database'];
$dbuser = $CC_CONFIG['dsn']['username'];
$dbhost = $CC_CONFIG['dsn']['hostspec'];
$res = `pg_dump -s $dbname -U $dbuser`;
echo "$res\n";
?>