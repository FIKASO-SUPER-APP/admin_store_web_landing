#!/bin/sh
set -e

echo "🚀 Starting Store Panel..."

# Attendre que la base de données soit prête
echo "⏳ Waiting for database..."
until php artisan db:show 2>/dev/null; do
  echo "Database is unavailable - sleeping"
  sleep 2
done

echo "✅ Database is ready!"

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
if [ ! -L /var/www/store/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Vérifier si les migrations doivent être exécutées
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi

echo "✨ Store Panel is ready!"

# Exécuter la commande passée au conteneur
exec "$@"

