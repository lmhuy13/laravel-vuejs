#!/bin/bash

set -e

cd /var/www/html/laravel

until [ -f vendor/autoload.php ]; do
  echo "waiting for vendor/autoload.php..."
  sleep 2
done

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
