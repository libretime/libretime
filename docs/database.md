Database
=============

LibreTime is designed to work with a [PostgreSQL](https://www.postgresql.org/) database server running locally.
LibreTime uses [PropelORM](http://propelorm.org) to interact with the ZendPHP components and create the database.

#Modifying the Database
If you are a developer seeking to add new columns to the database here are the steps.

1. Modify `airtime_mvc/build/schema.xml` with any changes.
2. Run `dev_tools/propel_generate.sh`
3. Update the upgrade.sql under `airtime_mvc/application/controllers/upgrade_sql/VERSION` for example
 `ALTER TABLE imported_podcast ADD COLUMN album_override boolean default 'f' NOT NULL;`

#Viewing the Database and Data
One new tool that you can use to interact with and directly view the LibreTime database and tables is [Postage](https://github.com/workflowproducts/postage/releases/)

It provides a graphical interface that can show the LibreTime tables and easily modify the data inside. It runs as a local GUI client.
Here are some brief instructions for how to get starting using it as a developer with a vagrant virtual machine.

1. Download and install release from their [github page](https://github.com/workflowproducts/postage/releases/) - Additional instructions [here](https://github.com/workflowproducts/postage/) 
2. Setup port-forwarding for the vagrant VM - check the VirtualBox Settings for the VM -> Network -> Advanced -> Port Forwarding -> Forward HostPort 5550 to GuestPort 5432
3. Modify PostgreSQL to accept connections from outside localhost -> edit /etc/postgresql/VERSION#/main/postgresql.conf - uncomment and modify the listen_address to be `listen_addresses = '*' `you may also need to edit pg_hba.conf in the same directory and allow Ipv4 connections from your localhost. I modified it to all as security wasn't a concern.
4. Setup a Postgres username/password for super user in Ubuntu etc use ```

sudo -u postgres psql postgres

# \password postgres

Enter new password: 
``` 
5. Startup Postage by running `postage` and edit the postage-connections.conf and set the port to 5550 and save it. Then type in the username postgres and password you set above.
6. Launch and select the airtime database to view the copy running on your vagrant box. To see the data/schema in a particular table click Schemas->Tables->table_name and then DesignTable or EditData

This can provide a easier way to view how LibreTime composes its tables than the CLI to postgresql.

## Description of Tables and their Purposes 
TODO
