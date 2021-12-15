#!/usr/bin/env bash
#wait for the MySQL Server to come up
#sleep 90s

#run the setup script to create the DB and the schema in the DB
mysql --user=root --password=$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < "/docker-entrypoint-initdb.d/create-tables.sql"
