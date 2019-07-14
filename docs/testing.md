# Testing LibreTime

## MVC

The MVC tests are based on PHPUnit and may be found in `airtime_mvc/tests`.

You can run the tests in you working copy as described below or let travis
run them for you on pushes.

### Prepare environment

PHPUnit will need to be able to access the database and be allowed to
create the libretime_test database. On a clean postgresql install this may 
be set up as follows.

```bash
psql -c 'CREATE DATABASE libretime;' -U postgres
psql -c "CREATE USER libretime WITH PASSWORD 'libretime';" -U postgres
psql -c 'GRANT CONNECT ON DATABASE libretime TO libretime;' -U postgres
psql -c 'ALTER USER libretime CREATEDB;' -U postgres
```

In this case the libretime database is only used for the initial connection
over which the libretime_test database is created.

You may need to tweak the exact commands needed to setup postgresql depending
on the distro you installed this to. On Ubuntu the above can be acheived as
follows.

```bash
sudo -u postgres psql -c 'CREATE DATABASE libretime;'
sudo -u postgres psql -c "CREATE USER libretime WITH PASSWORD 'libretime';"
sudo -u postgres psql -c 'GRANT CONNECT ON DATABASE libretime TO libretime;'
sudo -u postgres psql -c 'ALTER USER libretime CREATEDB;'
```

### Install PHPUnit 

PHPUnit should have already been installed when you ran `composer install`.

If you have not done so, now is the time to do so.

### Run the tests

```bash
# run all tests
cd airtime_mvc/tests
../../vendor/bin/phpunit

# run a subset of tests
../../vendor/bin/phpunit --filter testEditReatingShowInstance 
```

## Python

The python tests are run through nosetest. To prepare your env you should install
it.

```bash
# Debian/Ubuntu
apt-get install python-nose

# CentOS
yum install -y python-nose
```

In most cases you need to install deps before the tests can be run.

### Airtime Analyzer

```bash
cd python_apps/airtime_analyzer
nosetests
```
