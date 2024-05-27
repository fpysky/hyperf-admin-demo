#!/bin/bash

# shellcheck disable=SC2164
cd /var/www/hyperf-admin-demo

git fetch && git checkout . && git reset --hard origin/master && git pull

sudo docker stop hyperf-admin

sudo docker rm hyperf-admin

sudo docker-compose up -d