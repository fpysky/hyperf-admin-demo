#!/bin/bash

git fetch && git checkout . && git reset --hard origin/master && git pull

sudo docker stop hyperf-admin

sudo docker rm hyperf-admin

sudo docker-compose up -d

cd ./admin/

yarn build:prod