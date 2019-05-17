#!/usr/bin/env bash

PRESERVE_BACKUP_COUNT=2
MYSQL_USERNAME=""
MYSQL_PASSWORD=""
DATESTAMP=`date +'%Y-%m-%d'`

cd /var/www/lemmingsforums.net/public/
mkdir ../private/backup

rclone copy gdrive:log.txt ../private/backup/
IFS=$'\n' read -d '' -r -a BACKUP_DATES < ../private/backup/log.txt

tar -cf ../private/backup/attachments.tar attachments*/*
tar -cf ../private/backup/pm-attachments.tar pm_attachments*/*

cd ../private/backup/
mysqldump -u $MYSQL_USERNAME -p$MYSQL_PASSWORD --databases LemForums > LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}.sql.gz LemmingsForums_${DATESTAMP}.sql
rm LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}_Attachments.tar.gz *.tar
rm *.tar

if ( rclone move LemmingsForums_${DATESTAMP}.sql.gz gdrive: ) &&
   ( rclone move LemmingsForums_${DATESTAMP}_Attachments.tar.gz gdrive: ); then
    if [ "${#BACKUP_DATES[@]}" -gt "${PRESERVE_BACKUP_COUNT}" ]; then
      rclone delete gdrive:LemmingsForums_${BACKUP_DATES[0]}.sql.gz
      rclone delete gdrive:LemmingsForums_${BACKUP_DATES[0]}_Attachments.tar.gz

      BACKUP_DATES=( ${BACKUP_DATES[@]:1} )
    fi
fi

BACKUP_DATES+=( "${DATESTAMP}" )

printf "%s\n" "${BACKUP_DATES[@]}" > log.txt
rclone move log.txt gdrive:
