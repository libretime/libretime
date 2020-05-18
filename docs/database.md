---
sidebar: devs
---

# Database

LibreTime is designed to work with a [PostgreSQL](https://www.postgresql.org/) database server running locally.
LibreTime uses [PropelORM](http://propelorm.org) to interact with the ZendPHP components and create the database.

## Modifying the Database
If you are a developer seeking to add new columns to the database here are the steps.

1. Modify `airtime_mvc/build/schema.xml` with any changes.
2. Run `dev_tools/propel_generate.sh`
3. Update the upgrade.sql under `airtime_mvc/application/controllers/upgrade_sql/VERSION` for example
 `ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;`
