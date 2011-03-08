<?php

$dir = __DIR__;

$command = "php $dir/../library/doctrine/migrations/doctrine-migrations.phar --configuration=$dir/DoctrineMigrations/migrations.xml --db-configuration=$dir/../library/doctrine/migrations/migrations-db.php --no-interaction migrations:migrate";
system($command);

