#!/bin/bash

git checkout . && git reset --hard origin/master

sudo docker stop hyperf-admin

sudo docker rm hyperf-admin

sudo docker-compose up -d