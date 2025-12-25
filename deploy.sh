#!/bin/bash

# Script de déploiement pour FIKASO
# Usage: ./deploy.sh [start|stop|restart|logs|update]

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Vérifier si Docker est installé
check_docker() {
    if ! command -v docker &> /dev/null; then
        error "Docker n'est pas installé. Veuillez l'installer d'abord."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        error "Docker Compose n'est pas installé. Veuillez l'installer d'abord."
        exit 1
    fi
}

# Vérifier si le fichier .env existe
check_env() {
    if [ ! -f .env ]; then
        warning "Le fichier .env n'existe pas. Copie de env.example vers .env"
        cp env.example .env
        warning "Veuillez éditer le fichier .env avec vos configurations avant de continuer."
        exit 1
    fi
}

# Démarrer les conteneurs
start() {
    info "Démarrage des conteneurs..."
    check_docker
    check_env
    
    # Construire et démarrer les conteneurs
    docker-compose up -d --build
    
    info "Attente du démarrage de MySQL..."
    sleep 10
    
    # Installer les dépendances Composer dans chaque conteneur
    info "Installation des dépendances Composer..."
    docker-compose exec admin composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Admin échouée"
    docker-compose exec store composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Store échouée"
    docker-compose exec website composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Website échouée"
    
    # Exécuter les migrations pour chaque application
    #info "Exécution des migrations..."
    #docker-compose exec admin php artisan migrate --force || warning "Migrations Admin échouées"
    #docker-compose exec store php artisan migrate --force || warning "Migrations Store échouées"
    #docker-compose exec website php artisan migrate --force || warning "Migrations Website échouées"
    
    # Optimisation Laravel
    #info "Optimisation des applications Laravel..."
    #docker-compose exec admin php artisan config:cache
    #docker-compose exec admin php artisan route:cache
    #docker-compose exec admin php artisan view:cache
    
    #docker-compose exec store php artisan config:cache
    #docker-compose exec store php artisan route:cache
    #docker-compose exec store php artisan view:cache
    
    #docker-compose exec website php artisan config:cache
    #docker-compose exec website php artisan route:cache
    #docker-compose exec website php artisan view:cache
    
    info "Déploiement terminé avec succès!"
    info "Vos applications sont accessibles sur:"
    echo "  - Admin:   http://admin.fikasoplus.com"
    echo "  - Store:   http://store.fikasoplus.com"
    echo "  - Website: http://web.fikasoplus.com"
    echo "  - Landing: http://landing.fikasoplus.com"
}

# Arrêter les conteneurs
stop() {
    info "Arrêt des conteneurs..."
    docker-compose down
    info "Conteneurs arrêtés."
}

# Redémarrer les conteneurs
restart() {
    info "Redémarrage des conteneurs..."
    stop
    start
}

# Afficher les logs
logs() {
    docker-compose logs -f "$@"
}

# Mettre à jour les conteneurs
update() {
    info "Mise à jour des conteneurs..."
    
    # Pull des images
    docker-compose pull
    
    # Rebuild et redémarrage
    docker-compose up -d --build
    
    # Installer les dépendances Composer dans chaque conteneur
    info "Installation des dépendances Composer..."
    docker-compose exec admin composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Admin échouée"
    docker-compose exec store composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Store échouée"
    docker-compose exec website composer install --no-dev --optimize-autoloader --no-interaction || warning "Installation Website échouée"
    
    # Migrations
    #docker-compose exec admin php artisan migrate --force
    #docker-compose exec store php artisan migrate --force
    #docker-compose exec website php artisan migrate --force
    
    # Clear cache
    #docker-compose exec admin php artisan cache:clear
    #docker-compose exec store php artisan cache:clear
    #docker-compose exec website php artisan cache:clear
    
    info "Mise à jour terminée!"
}

# Backup de la base de données
backup() {
    info "Création d'un backup de la base de données..."
    BACKUP_DIR="backups"
    mkdir -p $BACKUP_DIR
    TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    
    docker-compose exec -T mysql mysqldump -u root -p${MYSQL_ROOT_PASSWORD} --all-databases > "$BACKUP_DIR/backup_$TIMESTAMP.sql"
    
    info "Backup créé: $BACKUP_DIR/backup_$TIMESTAMP.sql"
}

# Afficher l'aide
help() {
    echo "Usage: ./deploy.sh [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  start      - Démarrer tous les conteneurs"
    echo "  stop       - Arrêter tous les conteneurs"
    echo "  restart    - Redémarrer tous les conteneurs"
    echo "  logs       - Afficher les logs (Ctrl+C pour quitter)"
    echo "  update     - Mettre à jour les conteneurs"
    echo "  backup     - Créer un backup de la base de données"
    echo "  help       - Afficher cette aide"
    echo ""
}

# Main
case "${1:-}" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    logs)
        shift
        logs "$@"
        ;;
    update)
        update
        ;;
    backup)
        backup
        ;;
    help|--help|-h)
        help
        ;;
    *)
        error "Commande invalide: ${1:-}"
        help
        exit 1
        ;;
esac

