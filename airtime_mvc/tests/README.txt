To get the Airtime unit tests running:
==========================

1) Install PHPUnit 

We explicitly install PHPUnit 3.4 because that the most recent version
that's still supported by Zend Framework 1:

sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.symfony-project.com
sudo pear install channel://pear.symfony-project.com/YAML
sudo pear install pear.phpunit.de/PHPUnit-3.4.10


DO NOT INSTALL the DbUnit package!
DbUnit overwrites a file that's actually part of the PHPUnit package,
    /usr/share/php/PHPUnit/Extensions/Database/DataSet/QueryTable.php
with a version that's incompatible and gives an error for us.


2) Running the unit tests:

    1. To run all the unit tests, run:

        $ sudo ./runtests.sh

    (It has to be run as root to access the database for now.)

    2. To run one specific test, you can do something like:

        $ export AIRTIME_UNIT_TEST="1"
        $ sudo -E phpunit --filter testEditReatingShowInstance application/services/database/ShowServiceDbTest.php

    IMPORTANT: Make sure you use "sudo" with the "-E" flag so it preserves the environment variable we set before that.

