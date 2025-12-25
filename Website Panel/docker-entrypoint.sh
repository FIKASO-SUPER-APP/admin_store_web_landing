#!/bin/sh
set -e

echo "🚀 Starting Website Panel..."

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:YOUR_APP_KEY_HERE" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Optimiser l'application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer les liens symboliques pour le storage
if [ ! -L /var/www/website/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Vérifier si les migrations doivent être exécutées
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi

echo "✨ Website Panel is ready!"

# Exécuter la commande passée au conteneur
exec "$@"

