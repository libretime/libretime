#/bin/bash

psql -c 'CREATE DATABASE libretime;' -U postgres
psql -c "CREATE USER libretime WITH PASSWORD 'libretime';" -U postgres
psql -c 'GRANT CONNECT ON DATABASE libretime TO libretime;' -U postgres
psql -c 'ALTER USER libretime CREATEDB;' -U postgres