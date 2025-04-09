@servers(['web' => 'envoy-server'])

@setup
    $branch = 'master';
    $path = 'public_html/bev-bomberos';
@endsetup

@task('addChanges', ['on' => 'web'])
    cd {{ $path }}

    echo "Descargando cambios..."
    git pull origin {{ $branch }}

    echo "Instalando dependencias..."
    composer install --no-dev --optimize-autoloader

    echo "Ejecutando migraciones..."
    php artisan migrate --force

    echo "Limpiando caché..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Despliegue completado."
@endtask

@task('clear-cache', ['on' => 'web'])
    cd {{ $path }}
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
@endtask

@task('deploy', ['on' => 'web'])
    cd {{ $path }}

    echo "Descargando cambios..."
    git pull origin {{ $branch }}

    echo "Limpiando caché..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Despliegue completado."
@endtask