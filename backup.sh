#!/usr/bin/env bash

PRESERVE_BACKUP_COUNT=2
MYSQL_USERNAME=""
MYSQL_PASSWORD=""
DATESTAMP=`date +'%Y-%m-%d'`

cd /home/lemmingsforums.net/www/public/
mkdir ../private/backup

rclone copy gdrive:lf-log.txt ../private/backup/
IFS=$'\n' read -d '' -r -a BACKUP_DATES < ../private/backup/lf-log.txt

tar -cf ../private/backup/attachments.tar attachments*/*
tar -cf ../private/backup/pm-attachments.tar pm_attachments*/*

cd ../private/backup/
mysqldump -u $MYSQL_USERNAME -p$MYSQL_PASSWORD --databases LemForums --result-file=LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}_Database.tar.gz LemmingsForums_${DATESTAMP}.sql
rm LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}_Attachments.tar.gz *.tar
rm *.tar

if ( rclone move LemmingsForums_${DATESTAMP}_Database.tar.gz gdrive: ) &&
   ( rclone move LemmingsForums_${DATESTAMP}_Attachments.tar.gz gdrive: ); then
    if [ "${#BACKUP_DATES[@]}" -gt "${PRESERVE_BACKUP_COUNT}" ]; then
      rclone delete gdrive:LemmingsForums_${BACKUP_DATES[0]}_Database.tar.gz
      rclone delete gdrive:LemmingsForums_${BACKUP_DATES[0]}_Attachments.tar.gz

      BACKUP_DATES=( ${BACKUP_DATES[@]:1} )
    fi
fi

BACKUP_DATES+=( "${DATESTAMP}" )

printf "%s\n" "${BACKUP_DATES[@]}" > lf-log.txt
rclone move lf-log.txt gdrive:
