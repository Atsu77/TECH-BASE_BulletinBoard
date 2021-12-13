#!/usr/bin/env bash
#wait for the MySQL Server to come up
#sleep 90s

#run the setup script to create the DB and the schema in the DB
mysql --user=$DB_USER --password=$DB_PASSWORD mission4_database < "/docker-entrypoint-initdb.d/create-tables.sql"