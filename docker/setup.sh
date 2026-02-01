#!/bin/bash

set -e

cd /var/www/html/laravel

echo "================================"
echo "Laravel Setup Script"
echo "================================"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}[1/8] Checking .env file...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo -e "${YELLOW}[2/7] Installing Composer dependencies...${NC}"
composer install --no-interaction --prefer-dist

echo -e "${YELLOW}[3/7] Generating APP_KEY (if missing)...${NC}"
php -r "exit((int) (!preg_match('/^APP_KEY=base64:/m', file_get_contents('.env'))));" \
    && php artisan key:generate --force \
    || true

echo -e "${YELLOW}[4/7] Creating storage symlink...${NC}"
php artisan storage:link || true

echo -e "${YELLOW}[5/7] Running migrations (retrying until DB ready)...${NC}"
attempts=0
until php artisan migrate --force; do
    attempts=$((attempts+1))
    if [ "$attempts" -ge 30 ]; then
        echo "Database not ready after 30 attempts." >&2
        exit 1
    fi
    sleep 2
done

echo -e "${YELLOW}[6/7] Clearing cache...${NC}"
php artisan cache:clear
php artisan config:clear

echo ""
echo -e "${GREEN}✓ Setup complete!${NC}"
echo "Starting Laravel server on 0.0.0.0:8000"
echo "Access at http://localhost:8010"
echo ""

exec php artisan serve --host=0.0.0.0 --port=8000

