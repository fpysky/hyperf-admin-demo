#!/bin/bash

git pull

sudo docker stop hyperf-admin

sudo docker rm hyperf-admin

sudo docker-compose up -d

cd ./admin/

yarn build:prod