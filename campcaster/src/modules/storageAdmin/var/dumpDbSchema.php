<?
require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
 header("Conten-type: text/plain");
 $dbname = $config['dsn']['database'];
 $dbuser = $config['dsn']['username'];
 $dbhost = $config['dsn']['hostspec'];
 $res = `pg_dump -s $dbname -U $dbuser`;
 echo "$res\n";
?>