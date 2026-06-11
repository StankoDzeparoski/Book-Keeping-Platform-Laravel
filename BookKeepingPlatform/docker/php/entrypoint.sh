#!/bin/sh
set -eu

mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php artisan key:generate --show --no-ansi)"
fi

if [ "${DB_CONNECTION:-}" = "pgsql" ] && [ "${WAIT_FOR_DB:-true}" = "true" ]; then
    attempts="${DB_WAIT_ATTEMPTS:-60}"
    attempt=1

    until php -r '
        $host = getenv("DB_HOST") ?: "127.0.0.1";
        $port = getenv("DB_PORT") ?: "5432";
        $database = getenv("DB_DATABASE") ?: "laravel";
        $username = getenv("DB_USERNAME") ?: "root";
        $password = getenv("DB_PASSWORD") ?: "";

        try {
            new PDO(
                "pgsql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            exit(0);
        } catch (Throwable $exception) {
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            exit(1);
        }
    ' >/dev/null 2>&1; do
        if [ "$attempt" -ge "$attempts" ]; then
            echo "PostgreSQL is not reachable after ${attempts} attempts." >&2
            exit 1
        fi

        attempt=$((attempt + 1))
        sleep 1
    done
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_SEEDER:-false}" = "true" ]; then
    php artisan db:seed --force
fi

exec "$@"
