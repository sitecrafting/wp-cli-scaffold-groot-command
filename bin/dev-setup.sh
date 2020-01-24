#!/usr/bin/env bash

composer install

wp core download --path=wp

wp config create --dbname=wordpress \
  --dbuser=wordpress \
  --dbpass=wordpress \
  --dbhost=database

wp core install --url=wp-cli-command-scaffold-groot.lndo.site \
  --title='Groot Scaffold' \
  --admin_user=lando \
  --admin_email=lando@example.com
