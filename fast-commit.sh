#!/bin/bash

git pull

git add .

if [ "$1" == '' ]; then
    git commit -m "commit from fast commit"
else
    git commit -m "$1"
fi

git push