#!/usr/bin/env bash

MYSQL_USERNAME=""
MYSQL_PASSWORD=""
DATESTAMP=`date +'%Y-%m-%d'`

mkdir ../private/backup

tar -cf ../private/backup/attachments.tar attachments*/*
tar -cf ../private/backup/pm-attachments.tar pm_attachments*/*

cd ../private/backup/
mysqldump -u $MYSQL_USERNAME -p$MYSQL_PASSWORD --databases LemForums > LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}.sql.gz LemmingsForums_${DATESTAMP}.sql
rm LemmingsForums_${DATESTAMP}.sql

tar -czf LemmingsForums_${DATESTAMP}_Attachments.tar.gz *.tar
rm *.tar

rclone move LemmingsForums_${DATESTAMP}.sql.gz gdrive:
rclone move LemmingsForums_${DATESTAMP}_Attachments.tar.gz gdrive:
