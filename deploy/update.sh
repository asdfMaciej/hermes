#!/bin/bash
# directory in which I have a repo
cd /root/hermes

# stash the changes if local changes were made:
git add *
git stash

# pull the changes from @asdfMaciej repo
git pull

# copy the necessary files
# we omit the application & uploads folders
cp -rf /root/hermes/app/controllers/* /var/www/hermes/controllers
cp -rf /root/hermes/app/models/* /var/www/hermes/models
cp -rf /root/hermes/app/static/* /var/www/hermes/static
cp -rf /root/hermes/app/views/* /var/www/hermes/views
cp -f /root/hermes/app/index.php /var/www/hermes/index.php

# copy everything except the config file
# will change that after I utilize env variables
mv -f /var/www/hermes/application/config.php /root/hermes/old_config.php
cp -rf /root/hermes/app/application/* /var/www/hermes/application
mv -f /root/hermes/old_config.php /var/www/hermes/application/config.php

# give exec rights to this file so I can use it later
chmod +x /root/hermes/deploy/update.sh
