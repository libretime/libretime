#!/bin/bash

if [ -z "$1" ]
        then
                ## Use config
                backup_folder=~/libretime_backup/
        else
                ## User arg as config
                backup_folder=$1
fi


airtime_conf_path=/etc/airtime/airtime.conf
uploads_folder=/srv/airtime/stor/

psdl_db=$(grep dbname ${airtime_conf_path} | awk '{print $3;}' )
psql_user=$(grep dbuser ${airtime_conf_path} | awk '{print $3;}' )
psql_password=$(grep dbpass ${airtime_conf_path} | awk '{print $3;}' )

## Remove old backup
rm -rf $backup_folder
mkdir $backup_folder

## Backup of database

echo 'db: Getting database...'
pg_dump --dbname='postgresql://'$psql_user':'$psql_password'@localhost/'$psql_db > $backup_folder'database'
echo 'db: Complete'

## Backup of sounds

mkdir $backup_folder'uploads/'

echo 'stor : Copying uploaded files...'
rsync -r -a --info=progress2 $uploads_folder $backup_folder'uploads/'
echo 'stor: Complete'

## Backup of libretime config

mkdir $backup_folder'airtime_config/'

echo 'config: Copying config...'
rsync -r -a --info=progress2 /etc/airtime/ $backup_folder'airtime_config/'
echo 'config: Complete'

date >> $backup_folder'datelog.txt'
