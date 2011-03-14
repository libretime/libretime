<?php
	/* to enable this script, make sure phar files are enabled
	 * phar.readonly = Off
	 * in /etc/php5/cli/php.ini
	 */

    $dir = __DIR__;
    $command = "php $dir/../library/doctrine/migrations/doctrine-migrations.phar --configuration=$dir/../install/DoctrineMigrations/migrations.xml --db-configuration=$dir/../library/doctrine/migrations/migrations-db.php --no-interaction migrations:generate";
    system($command);
