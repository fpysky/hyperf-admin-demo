#!/bin/bash

cd  "$( dirname ${0} )/.."

composer dump-autoload -o

php bin/hyperf.php start