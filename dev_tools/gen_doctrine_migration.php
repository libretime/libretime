<?php
	/* to enable this script, make sure phar files are enabled
	 * phar.readonly = Off
	 * in /etc/php5/cli/php.ini
	 */

    $dir = __DIR__;
    $command = "php --php-ini $dir/../install_minimal/airtime-php.ini $dir/../airtime_mvc/library/doctrine/migrations/doctrine-migrations.phar --configuration=$dir/../install_minimal/DoctrineMigrations/migrations.xml --db-configuration=$dir/../airtime_mvc/library/doctrine/migrations/migrations-db.php --no-interaction migrations:generate";
    system($command);
