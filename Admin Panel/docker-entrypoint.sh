#!/bin/sh
set -e

echo "🚀 Starting Admin Panel..."

# S'assurer qu'on est dans le bon répertoire
cd /var/www/admin || exit 1

# Attendre que la base de données soit prête
echo "⏳ Waiting for database..."
MAX_ATTEMPTS=60
ATTEMPT=0

until php artisan db:show >/dev/null 2>&1; do
  ATTEMPT=$((ATTEMPT + 1))
  if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
    echo "❌ Database connection failed after $MAX_ATTEMPTS attempts"
    echo "Debug info:"
    echo "  DB_HOST: ${DB_HOST:-not set}"
    echo "  DB_PORT: ${DB_PORT:-not set}"
    echo "  DB_DATABASE: ${DB_DATABASE:-not set}"
    echo "  DB_USERNAME: ${DB_USERNAME:-not set}"
    php artisan db:show 2>&1 || true
    exit 1
  fi
  echo "Database is unavailable - sleeping (attempt $ATTEMPT/$MAX_ATTEMPTS)"
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
if [ ! -L /var/www/admin/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Vérifier si les migrations doivent être exécutées
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi

echo "✨ Admin Panel is ready!"

# Exécuter la commande passée au conteneur
exec "$@"

