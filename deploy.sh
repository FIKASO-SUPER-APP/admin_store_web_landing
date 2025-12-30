#!/bin/bash

# Script de déploiement Fikaso
# Usage: ./deploy.sh

set -e

echo "🚀 Déploiement de Fikaso..."

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier si Docker est installé
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker n'est pas installé. Veuillez l'installer d'abord.${NC}"
    exit 1
fi

# Vérifier si Docker Compose est installé
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose n'est pas installé. Veuillez l'installer d'abord.${NC}"
    exit 1
fi

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    echo -e "${YELLOW}📝 Création du fichier .env...${NC}"
    cat > .env << EOF
# Environment
APP_ENV=production
APP_DEBUG=false
TZ=Africa/Abidjan

# MySQL Configuration
MYSQL_ROOT_PASSWORD=change_me_secure_password_123
DB_USERNAME=root
DB_PASSWORD=change_me_secure_password_123

# Database Names
ADMIN_DB_NAME=fikaso_admin
STORE_DB_NAME=fikaso_store
WEBSITE_DB_NAME=fikaso_website
EOF
    echo -e "${YELLOW}⚠️  Veuillez modifier le fichier .env avec vos propres valeurs avant de continuer.${NC}"
    echo -e "${YELLOW}   Appuyez sur Entrée pour continuer ou Ctrl+C pour annuler...${NC}"
    read
fi

# Configurer les permissions
echo -e "${GREEN}🔧 Configuration des permissions...${NC}"
chmod -R 755 .
chmod -R 775 "Admin Panel/storage" "Store Panel/storage" "Website Panel/storage" 2>/dev/null || true
chmod -R 775 "Admin Panel/bootstrap/cache" "Store Panel/bootstrap/cache" "Website Panel/bootstrap/cache" 2>/dev/null || true

# Vérifier les fichiers .env des applications Laravel
for app in "Admin Panel" "Store Panel" "Website Panel"; do
    if [ ! -f "$app/.env" ]; then
        echo -e "${YELLOW}⚠️  Le fichier $app/.env n'existe pas.${NC}"
        echo -e "${YELLOW}   Veuillez créer ce fichier avec les bonnes configurations.${NC}"
    fi
done

# Construire les images Docker
echo -e "${GREEN}🔨 Construction des images Docker...${NC}"
docker-compose build --no-cache

# Démarrer les conteneurs
echo -e "${GREEN}🚀 Démarrage des conteneurs...${NC}"
docker-compose up -d

# Attendre que MySQL soit prêt
echo -e "${GREEN}⏳ Attente du démarrage de MySQL...${NC}"
sleep 15

# Vérifier que MySQL est prêt
until docker-compose exec -T mysql mysqladmin ping -h localhost --silent; do
    echo -e "${YELLOW}⏳ En attente de MySQL...${NC}"
    sleep 5
done

echo -e "${GREEN}✅ MySQL est prêt!${NC}"

# Générer les clés d'application Laravel
echo -e "${GREEN}🔑 Génération des clés d'application...${NC}"
docker-compose exec -T admin_panel php artisan key:generate --force 2>/dev/null || echo -e "${YELLOW}⚠️  Admin Panel: Vérifiez manuellement la clé${NC}"
docker-compose exec -T store_panel php artisan key:generate --force 2>/dev/null || echo -e "${YELLOW}⚠️  Store Panel: Vérifiez manuellement la clé${NC}"
docker-compose exec -T website_panel php artisan key:generate --force 2>/dev/null || echo -e "${YELLOW}⚠️  Website Panel: Vérifiez manuellement la clé${NC}"

# Optimiser Laravel pour la production
echo -e "${GREEN}⚡ Optimisation de Laravel pour la production...${NC}"
for app in admin_panel store_panel website_panel; do
    echo -e "${GREEN}   Optimisation de $app...${NC}"
    docker-compose exec -T $app php artisan config:cache 2>/dev/null || true
    docker-compose exec -T $app php artisan route:cache 2>/dev/null || true
    docker-compose exec -T $app php artisan view:cache 2>/dev/null || true
done

echo -e "${GREEN}✅ Déploiement terminé!${NC}"
echo ""
echo -e "${GREEN}📋 Informations importantes:${NC}"
echo -e "   - Admin Panel: http://localhost:8081"
echo -e "   - Store Panel: http://localhost:8082"
echo -e "   - Website Panel: http://localhost:8083"
echo -e "   - Landing Panel: http://localhost:8084"
echo ""
echo -e "${YELLOW}⚠️  N'oubliez pas de:${NC}"
echo -e "   1. Configurer vos domaines DNS"
echo -e "   2. Mettre à jour nginx/proxy.conf avec vos domaines"
echo -e "   3. Importer vos bases de données si nécessaire"
echo -e "   4. Configurer SSL/HTTPS avec Let's Encrypt"
echo ""
echo -e "${GREEN}📖 Consultez README_DEPLOYMENT.md pour plus d'informations.${NC}"

