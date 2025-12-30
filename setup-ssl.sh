#!/bin/bash

# Script de configuration SSL avec Let's Encrypt
# Usage: ./setup-ssl.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🔒 Configuration SSL avec Let's Encrypt${NC}"
echo ""

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker n'est pas installé.${NC}"
    exit 1
fi

# Créer les dossiers nécessaires
echo -e "${GREEN}📁 Création des dossiers pour les certificats...${NC}"
mkdir -p certbot/conf
mkdir -p certbot/www

# Demander les domaines
echo -e "${YELLOW}📝 Veuillez entrer vos domaines (séparés par des espaces):${NC}"
echo -e "${YELLOW}   Exemple: admin.fikaso.com store.fikaso.com www.fikaso.com fikaso.com landing.fikaso.com${NC}"
read -p "Domaines: " DOMAINS

if [ -z "$DOMAINS" ]; then
    echo -e "${RED}❌ Aucun domaine fourni.${NC}"
    exit 1
fi

# Demander l'email pour Let's Encrypt
read -p "Email pour Let's Encrypt: " EMAIL

if [ -z "$EMAIL" ]; then
    echo -e "${RED}❌ Email requis pour Let's Encrypt.${NC}"
    exit 1
fi

# Vérifier que les conteneurs sont démarrés
echo -e "${GREEN}🔍 Vérification que les conteneurs sont démarrés...${NC}"
if ! docker-compose ps | grep -q "fikaso_nginx_proxy.*Up"; then
    echo -e "${YELLOW}⚠️  Démarrage des conteneurs...${NC}"
    docker-compose up -d nginx_proxy
    sleep 5
fi

# Générer les certificats pour chaque domaine
echo -e "${GREEN}🔐 Génération des certificats SSL...${NC}"

for DOMAIN in $DOMAINS; do
    echo -e "${GREEN}   Génération du certificat pour $DOMAIN...${NC}"
    
    docker-compose run --rm certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email $EMAIL \
        --agree-tos \
        --no-eff-email \
        -d $DOMAIN
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}   ✅ Certificat généré pour $DOMAIN${NC}"
    else
        echo -e "${RED}   ❌ Erreur lors de la génération du certificat pour $DOMAIN${NC}"
    fi
done

# Redémarrer le reverse proxy pour charger les nouveaux certificats
echo -e "${GREEN}🔄 Redémarrage du reverse proxy...${NC}"
docker-compose restart nginx_proxy

echo ""
echo -e "${GREEN}✅ Configuration SSL terminée!${NC}"
echo ""
echo -e "${YELLOW}📋 Prochaines étapes:${NC}"
echo -e "   1. Vérifiez que vos domaines pointent vers l'IP de votre serveur"
echo -e "   2. Testez l'accès HTTPS: https://votre-domaine.com"
echo -e "   3. Les certificats seront renouvelés automatiquement tous les 12h"
echo ""
echo -e "${GREEN}💡 Pour renouveler manuellement:${NC}"
echo -e "   docker-compose run --rm certbot renew"

