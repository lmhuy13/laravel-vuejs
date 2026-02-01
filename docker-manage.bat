@echo off
setlocal enabledelayedexpansion

set COMMAND=%1
set OPTION=%2

if "%COMMAND%"=="" (
    call :show_help
    exit /b 0
)

if /i "%COMMAND%"=="start" (
    echo.
    echo [+] Starting containers...
    docker-compose up -d
    echo [OK] Containers started
    echo.
    echo [*] URLs:
    echo     - Laravel:  http://localhost:8010
    echo     - Vite:     http://localhost:5183
    echo     - PgAdmin:  http://localhost:5050 (admin@admin.com / admin)
    echo     - Redis:    localhost:6389
    exit /b 0
)

if /i "%COMMAND%"=="stop" (
    echo [*] Stopping containers...
    docker-compose stop
    echo [OK] Containers stopped
    exit /b 0
)

if /i "%COMMAND%"=="restart" (
    echo [*] Restarting containers...
    docker-compose restart
    echo [OK] Containers restarted
    exit /b 0
)

if /i "%COMMAND%"=="build" (
    echo [+] Building Docker images...
    docker-compose build
    echo [OK] Build complete
    exit /b 0
)

if /i "%COMMAND%"=="rebuild" (
    echo [+] Rebuilding Docker images (no cache)...
    docker-compose build --no-cache
    echo [OK] Build complete
    exit /b 0
)

if /i "%COMMAND%"=="logs" (
    if "%OPTION%"=="" (
        set OPTION=app
    )
    echo [*] Showing logs for !OPTION!...
    docker-compose logs -f !OPTION!
    exit /b 0
)

if /i "%COMMAND%"=="shell" (
    if "%OPTION%"=="" (
        set OPTION=app
    )
    echo [*] Opening shell in !OPTION!...
    docker-compose exec !OPTION! sh
    exit /b 0
)

if /i "%COMMAND%"=="migrate" (
    echo [*] Running migrations...
    docker-compose exec -T app php artisan migrate
    echo [OK] Migrations complete
    exit /b 0
)

if /i "%COMMAND%"=="seed" (
    echo [*] Seeding database...
    docker-compose exec -T app php artisan db:seed
    echo [OK] Seeding complete
    exit /b 0
)

if /i "%COMMAND%"=="migrate:seed" (
    echo [*] Running migrations and seeding...
    docker-compose exec -T app php artisan migrate:refresh --seed
    echo [OK] Migrations and seeding complete
    exit /b 0
)

if /i "%COMMAND%"=="artisan" (
    if "%OPTION%"=="" (
        echo [-] Please provide artisan command
        echo Usage: docker-manage.bat artisan [command]
        exit /b 1
    )
    echo [*] Running artisan: php artisan %OPTION% %3 %4 %5 %6 %7 %8 %9
    docker-compose exec -T app php artisan %OPTION% %3 %4 %5 %6 %7 %8 %9
    exit /b 0
)

if /i "%COMMAND%"=="composer" (
    if "%OPTION%"=="" (
        echo [-] Please provide composer command
        echo Usage: docker-manage.bat composer [command]
        exit /b 1
    )
    echo [*] Running composer: composer %OPTION% %3 %4 %5 %6 %7 %8 %9
    docker-compose exec -T app composer %OPTION% %3 %4 %5 %6 %7 %8 %9
    exit /b 0
)

if /i "%COMMAND%"=="npm" (
    if "%OPTION%"=="" (
        echo [-] Please provide npm command
        echo Usage: docker-manage.bat npm [command]
        exit /b 1
    )
    echo [*] Running npm: npm %OPTION% %3 %4 %5 %6 %7 %8 %9
    docker-compose exec -T app npm %OPTION% %3 %4 %5 %6 %7 %8 %9
    exit /b 0
)

if /i "%COMMAND%"=="tinker" (
    echo [*] Opening Laravel Tinker...
    docker-compose exec app php artisan tinker
    exit /b 0
)

if /i "%COMMAND%"=="clean" (
    echo [-] Cleaning up (removing containers, volumes)...
    docker-compose down -v
    echo [OK] Cleanup complete
    exit /b 0
)

if /i "%COMMAND%"=="ps" (
    echo [*] Container Status:
    docker-compose ps
    exit /b 0
)

if /i "%COMMAND%"=="up" (
    echo [+] Starting containers (foreground)...
    docker-compose up
    exit /b 0
)

if /i "%COMMAND%"=="help" (
    call :show_help
    exit /b 0
)

if /i "%COMMAND%"=="--help" (
    call :show_help
    exit /b 0
)

if /i "%COMMAND%"=="-h" (
    call :show_help
    exit /b 0
)

echo [-] Unknown command: %COMMAND%
echo.
call :show_help
exit /b 1

:show_help
echo.
echo Laravel Docker Management Script
echo.
echo Usage: docker-manage.bat [command] [options]
echo.
echo Commands:
echo   start              Start all containers
echo   stop               Stop all containers
echo   restart            Restart all containers
echo   build              Build all Docker images
echo   rebuild            Rebuild all Docker images from scratch
echo   logs [service]     View logs (app, supervisor, vite, postgres, redis, pgadmin)
echo   shell [service]    Open shell in container (default: app)
echo   migrate            Run database migrations
echo   seed               Seed database
echo   migrate:seed       Run migrations and seed
echo   artisan [cmd]      Run artisan command
echo   composer [cmd]     Run composer command
echo   npm [cmd]          Run npm command
echo   tinker             Open Laravel Tinker
echo   clean              Stop and remove containers, volumes
echo   ps                 Show running containers
echo   up                 Start containers in foreground (Ctrl+C to stop)
echo.
echo Examples:
echo   docker-manage.bat start
echo   docker-manage.bat logs app
echo   docker-manage.bat artisan migrate
echo.
exit /b 0
