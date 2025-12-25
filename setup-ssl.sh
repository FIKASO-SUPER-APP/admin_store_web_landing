#!/bin/bash

# Script pour configurer SSL avec Let's Encrypt
# Usage: ./setup-ssl.sh

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Charger les variables d'environnement
if [ ! -f .env ]; then
    error "Le fichier .env n'existe pas."
    exit 1
fi

source .env

# Vérifier si certbot est installé
if ! command -v certbot &> /dev/null; then
    info "Installation de Certbot..."
    apt-get update
    apt-get install -y certbot
fi

# Créer le dossier SSL s'il n'existe pas
mkdir -p nginx/ssl

# Fonction pour obtenir un certificat SSL
get_certificate() {
    local domain=$1
    info "Obtention du certificat SSL pour $domain..."
    
    certbot certonly --standalone \
        --preferred-challenges http \
        --http-01-port 80 \
        -d $domain \
        --non-interactive \
        --agree-tos \
        --email ${EMAIL:-admin@${domain}}
    
    # Copier les certificats dans le dossier nginx/ssl
    cp /etc/letsencrypt/live/$domain/fullchain.pem nginx/ssl/${domain}.crt
    cp /etc/letsencrypt/live/$domain/privkey.pem nginx/ssl/${domain}.key
    
    info "Certificat SSL obtenu pour $domain"
}

# Arrêter nginx temporairement pour permettre à certbot d'utiliser le port 80
info "Arrêt temporaire de nginx..."
docker-compose stop nginx

# Obtenir les certificats pour chaque domaine
get_certificate ${ADMIN_DOMAIN}
get_certificate ${STORE_DOMAIN}
get_certificate ${WEBSITE_DOMAIN}
get_certificate ${LANDING_DOMAIN}

# Mettre à jour les configurations nginx pour activer SSL
info "Activation de SSL dans les configurations nginx..."

for conf in nginx/conf.d/*.conf; do
    # Décommenter les lignes SSL
    sed -i 's/# listen 443/listen 443/g' $conf
    sed -i 's/# ssl_/ssl_/g' $conf
    sed -i 's/# if ($scheme/if ($scheme/g' $conf
    sed -i 's/#     return 301/    return 301/g' $conf
    sed -i 's/# }/}/g' $conf
done

# Redémarrer nginx
info "Redémarrage de nginx avec SSL..."
docker-compose up -d nginx

# Configurer le renouvellement automatique
info "Configuration du renouvellement automatique..."
cat > /etc/cron.d/certbot-renew << EOF
0 3 * * * root certbot renew --quiet --post-hook "cd $(pwd) && docker-compose restart nginx"
EOF

info "SSL configuré avec succès!"
info "Vos applications sont maintenant accessibles en HTTPS:"
echo "  - https://${ADMIN_DOMAIN}"
echo "  - https://${STORE_DOMAIN}"
echo "  - https://${WEBSITE_DOMAIN}"
echo "  - https://${LANDING_DOMAIN}"

