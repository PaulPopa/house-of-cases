#!/bin/bash

# install latest nvm
rm -rf ~/.nvm
git clone https://github.com/creationix/nvm.git ~/.nvm && cd ~/.nvm && git checkout `git describe --abbrev=0 --tags`
source ~/.nvm/nvm.sh
echo "source ~/.nvm/nvm.sh" >> ~/.bashrc

# install latest lts node.js
echo "Installing node.js... (please be patient)"
nvm install --lts &> /dev/null

# install global node packages
echo "Installing global node.js packages... (please be patient)"
# change 'gulp' to 'grunt' depending on project setup
npm install -g gulp # bower npm-check-updates

echo 'REMOVE EXISTING YARN'
rm -rf ~/.yarn &> /dev/null

echo 'DOWNLOAD AND INSTALL YARN'
curl -o- -L https://yarnpkg.com/install.sh | bash &> /dev/null

echo 'DELETE NODE MODULES'
cd '/var/www/house-of-cases/magento/vendor/snowdog/frontools'
rm -rf node_modules

echo 'RUN YARN TO INSTALL DEPENDECIES'
bash ~/.yarn/bin/yarn

echo 'RUN GULP SETUP'
gulp setup
