To get the Airtime unit tests running:
==========================

1) Install PHPUnit

wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

2) Install the DbUnit extension

sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony.com
sudo pear install --alldeps phpunit/DbUnit

