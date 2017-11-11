#!/bin/bash

sound_folder='/srv/airtime/stor/'
backup_folder='/home/example/backup/'
psql_user='airtime'
psql_password='airtime'

## Remove old backup
rm -rf $backup_folder
mkdir $backup_folder

## Backup of database

echo 'db: Getting database...'
pg_dump --dbname='postgresql://'$psql_user':'$psql_password'@localhost/airtime' > $backup_folder'database'
echo 'db: Complete'

## Backup of sounds

mkdir $backup_folder'sounds/'

echo 'stor : Copying sounds...'
rsync -r -a --info=progress2 $sound_folder $backup_folder'sounds/'
echo 'stor: Complete'

## Backup of libretime config

mkdir $backup_folder'airtime_config/'

echo 'config: Copying config...'
rsync -r -a --info=progress2 /etc/airtime/ $backup_folder'airtime_config/'
echo 'config: Complete'
