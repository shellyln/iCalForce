#!/bin/bash

echo '[IN] ./vendor'
cd ./vendor || { echo error; exit 1; }

echo 'Removing vendor libraries...'
rm -rf soapclient.repo || { echo error; exit 1; }
rm -rf PHP-OAuth2      || { echo error; exit 1; }
echo 'finished!'

echo 'Fetching vendor libraries...'
git clone https://github.com/developerforce/Force.com-Toolkit-for-PHP.git soapclient.repo || { echo error; exit 1; }
git clone https://github.com/adoy/PHP-OAuth2.git PHP-OAuth2                               || { echo error; exit 1; }
echo 'finished!'

echo 'Removing vendor .git directories...'
rm -rf soapclient.repo/.git || { echo error; exit 1; }
rm -rf PHP-OAuth2/.git      || { echo error; exit 1; }
echo 'finished!'

echo '[OUT] ./vendor'
cd .. || { echo error; exit 1; }

if [ -d ../.git ]; then
  echo "it is Git managed."
  git fetch --all || { echo error; exit 1; }
  git status      || { echo error; exit 1; }
fi

echo 'finished!'