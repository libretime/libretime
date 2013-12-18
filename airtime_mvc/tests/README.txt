To get the Airtime unit tests running:
==========================

1) Install PHPUnit 

We explicitly install PHPUnit 3.4 because that's as a new of a version
as is supported by Zend Framework 1:

sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.symfony-project.com
sudo pear install channel://pear.symfony-project.com/YAML
sudo pear install pear.phpunit.de/PHPUnit-3.4.10


DO NOT INSTALL the DbUnit package!
DbUnit overwrites a file that's actually part of the PHPUnit package,
    /usr/share/php/PHPUnit/Extensions/Database/DataSet/QueryTable.php
with a version that's incompatible and gives an error for us.



