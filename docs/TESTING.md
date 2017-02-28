# Testing LibreTime

## MVC

The MVC tests are based on PHPUnit and may be found in `airtime_mvc/tests`.

You can run the tests in you working copy as described below or let travis
run them for you on pushes.

### Prepare environment

PHPUnit will need to be able to access the database and be allowed to
create the libretime_test database. On a clean install this may be
set up as follows.

```bash
psql -c 'CREATE DATABASE libretime;' -U postgres -h localhost
psql -c "CREATE USER libretime WITH PASSWORD 'libretime';" -U postgres -h localhost
psql -c 'GRANT CONNECT ON DATABASE libretime TO libretime;' -U postgres -h localhost
psql -c 'ALTER USER libretime CREATEDB;' -U postgres -h localhost
```

In this case the libretime database is only used for the initial connection
over which the libretime_test database is created.

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
