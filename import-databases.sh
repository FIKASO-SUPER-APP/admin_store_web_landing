#!/bin/bash

# Script d'import des bases de données Fikaso
# Usage: ./import-databases.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Charger les variables d'environnement
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo -e "${RED}❌ Le fichier .env n'existe pas.${NC}"
    exit 1
fi

MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD:-fikaso_root_password}

echo -e "${GREEN}📦 Import des bases de données...${NC}"

# Vérifier que MySQL est prêt
until docker-compose exec -T mysql mysqladmin ping -h localhost --silent; do
    echo -e "${YELLOW}⏳ En attente de MySQL...${NC}"
    sleep 2
done

# Importer Admin Panel database
if [ -f "emart_admin_database.sql" ]; then
    echo -e "${GREEN}📥 Import de la base de données Admin Panel...${NC}"
    docker-compose exec -T mysql mysql -uroot -p${MYSQL_PASSWORD} fikaso_admin < emart_admin_database.sql
    echo -e "${GREEN}✅ Base de données Admin Panel importée${NC}"
else
    echo -e "${YELLOW}⚠️  Fichier emart_admin_database.sql non trouvé${NC}"
fi

# Importer Store Panel database
if [ -f "emart_store_database.sql" ]; then
    echo -e "${GREEN}📥 Import de la base de données Store Panel...${NC}"
    docker-compose exec -T mysql mysql -uroot -p${MYSQL_PASSWORD} fikaso_store < emart_store_database.sql
    echo -e "${GREEN}✅ Base de données Store Panel importée${NC}"
else
    echo -e "${YELLOW}⚠️  Fichier emart_store_database.sql non trouvé${NC}"
fi

# Importer Website Panel database
if [ -f "emart_website_database.sql" ]; then
    echo -e "${GREEN}📥 Import de la base de données Website Panel...${NC}"
    docker-compose exec -T mysql mysql -uroot -p${MYSQL_PASSWORD} fikaso_website < emart_website_database.sql
    echo -e "${GREEN}✅ Base de données Website Panel importée${NC}"
else
    echo -e "${YELLOW}⚠️  Fichier emart_website_database.sql non trouvé${NC}"
fi

echo -e "${GREEN}✅ Import terminé!${NC}"

