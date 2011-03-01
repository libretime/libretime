<?php

$command = __DIR__."/../library/liquibase/liquibase --driver=org.postgresql.Driver "
    ."--classpath=".__DIR__."/../library/liquibase/lib/postgresql-9.0-801.jdbc4.jar "
    ."--changeLogFile=".__DIR__."/upgrade/db.changelog-master.xml "
    ."--url=\"jdbc:postgresql://localhost:5432/airtime\" "
    ."--username=airtime "
    ."--password=airtime "
    ."migrate";
system($command);
