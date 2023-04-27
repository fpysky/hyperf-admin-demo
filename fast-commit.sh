#!/bin/bash

if $1 == ''
then
  commitStr=$1
else
  commitStr="optimized code"
fi


git pull

git add .

git commit -m commitStr

git push