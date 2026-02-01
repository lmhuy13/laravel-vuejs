#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

show_help() {
    echo "Laravel Docker Management Script"
    echo ""
    echo "Usage: ./docker-manage.sh [command] [options]"
    echo ""
    echo "Commands:"
    echo "  start              Start all containers"
    echo "  stop               Stop all containers"
    echo "  restart            Restart all containers"
    echo "  build              Build all Docker images"
    echo "  rebuild            Rebuild all Docker images from scratch"
    echo "  logs [service]     View logs (app, supervisor, vite, postgres, redis, pgadmin)"
    echo "  shell [service]    Open shell in container (default: app)"
    echo "  migrate            Run database migrations"
    echo "  seed               Seed database"
    echo "  migrate:seed       Run migrations and seed"
    echo "  artisan [cmd]      Run artisan command"
    echo "  composer [cmd]     Run composer command"
    echo "  npm [cmd]          Run npm command"
    echo "  tinker             Open Laravel Tinker"
    echo "  clean              Stop and remove containers, volumes"
    echo "  ps                 Show running containers"
    echo "  up                 Start containers in foreground (Ctrl+C to stop)"
    echo ""
    echo "Examples:"
    echo "  ./docker-manage.sh start"
    echo "  ./docker-manage.sh logs app"
    echo "  ./docker-manage.sh artisan migrate"
}

COMMAND=$1
OPTION=$2

case $COMMAND in
    start)
        echo -e "${GREEN}▶ Starting containers...${NC}"
        docker-compose up -d
        echo -e "${GREEN}✓ Containers started${NC}"
        echo ""
        echo -e "${YELLOW}URLs:${NC}"
        echo "  • Laravel:  http://localhost:8010"
        echo "  • Vite:     http://localhost:5183"
        echo "  • PgAdmin:  http://localhost:5050 (admin@admin.com / admin)"
        echo "  • Redis:    localhost:6389"
        ;;
    
    stop)
        echo -e "${YELLOW}⏸ Stopping containers...${NC}"
        docker-compose stop
        echo -e "${GREEN}✓ Containers stopped${NC}"
        ;;
    
    restart)
        echo -e "${YELLOW}⟳ Restarting containers...${NC}"
        docker-compose restart
        echo -e "${GREEN}✓ Containers restarted${NC}"
        ;;
    
    build)
        echo -e "${GREEN}🔨 Building Docker images...${NC}"
        docker-compose build
        echo -e "${GREEN}✓ Build complete${NC}"
        ;;
    
    rebuild)
        echo -e "${GREEN}🔨 Rebuilding Docker images (no cache)...${NC}"
        docker-compose build --no-cache
        echo -e "${GREEN}✓ Build complete${NC}"
        ;;
    
    logs)
        if [ -z "$OPTION" ]; then
            OPTION="app"
        fi
        echo -e "${YELLOW}📋 Showing logs for ${OPTION}...${NC}"
        docker-compose logs -f "$OPTION"
        ;;
    
    shell)
        if [ -z "$OPTION" ]; then
            OPTION="app"
        fi
        echo -e "${YELLOW}🔓 Opening shell in ${OPTION}...${NC}"
        docker-compose exec "$OPTION" sh
        ;;
    
    migrate)
        echo -e "${YELLOW}🗄 Running migrations...${NC}"
        docker-compose exec -T app php artisan migrate
        echo -e "${GREEN}✓ Migrations complete${NC}"
        ;;
    
    seed)
        echo -e "${YELLOW}🌱 Seeding database...${NC}"
        docker-compose exec -T app php artisan db:seed
        echo -e "${GREEN}✓ Seeding complete${NC}"
        ;;
    
    migrate:seed)
        echo -e "${YELLOW}🗄 Running migrations and seeding...${NC}"
        docker-compose exec -T app php artisan migrate:refresh --seed
        echo -e "${GREEN}✓ Migrations and seeding complete${NC}"
        ;;
    
    artisan)
        if [ -z "$OPTION" ]; then
            echo -e "${RED}✗ Please provide artisan command${NC}"
            echo "Usage: ./docker-manage.sh artisan [command]"
            exit 1
        fi
        echo -e "${YELLOW}⚙ Running artisan: php artisan ${OPTION} ${@:3}${NC}"
        docker-compose exec -T app php artisan "$OPTION" "${@:3}"
        ;;
    
    composer)
        if [ -z "$OPTION" ]; then
            echo -e "${RED}✗ Please provide composer command${NC}"
            echo "Usage: ./docker-manage.sh composer [command]"
            exit 1
        fi
        echo -e "${YELLOW}📦 Running composer: composer ${OPTION} ${@:3}${NC}"
        docker-compose exec -T app composer "$OPTION" "${@:3}"
        ;;
    
    npm)
        if [ -z "$OPTION" ]; then
            echo -e "${RED}✗ Please provide npm command${NC}"
            echo "Usage: ./docker-manage.sh npm [command]"
            exit 1
        fi
        echo -e "${YELLOW}📦 Running npm: npm ${OPTION} ${@:3}${NC}"
        docker-compose exec -T app npm "$OPTION" "${@:3}"
        ;;
    
    tinker)
        echo -e "${YELLOW}🔮 Opening Laravel Tinker...${NC}"
        docker-compose exec app php artisan tinker
        ;;
    
    clean)
        echo -e "${RED}🗑 Cleaning up (removing containers, volumes)...${NC}"
        docker-compose down -v
        echo -e "${GREEN}✓ Cleanup complete${NC}"
        ;;
    
    ps)
        echo -e "${YELLOW}📊 Container Status:${NC}"
        docker-compose ps
        ;;
    
    up)
        echo -e "${GREEN}▶ Starting containers (foreground)...${NC}"
        docker-compose up
        ;;
    
    help|--help|-h)
        show_help
        ;;
    
    *)
        echo -e "${RED}✗ Unknown command: $COMMAND${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac