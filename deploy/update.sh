#!/bin/bash
cd /root/hermes
git add *
git stash
git pull

cp -rf /root/hermes/app/controllers/* /var/www/hermes/controllers
cp -rf /root/hermes/app/models/* /var/www/hermes/models
cp -rf /root/hermes/app/static/* /var/www/hermes/static
cp -rf /root/hermes/app/views/* /var/www/hermes/views

mv -f /var/www/hermes/application/config.php /root/hermes/old_config.php
cp -rf /root/hermes/app/application/* /var/www/hermes/application
mv -f /root/hermes/old_config.php /var/www/hermes/application/config.php
