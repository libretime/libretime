<?
require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
 header("Conten-type: text/plain");
 $dbname = $config['dsn']['database'];
 $res = `pg_dump -s $dbname`;
 echo "$res\n";
?>