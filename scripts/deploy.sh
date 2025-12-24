#!/bin/bash

# ========================================
# Script de déploiement automatique
# eMart/Fikaso - Déploiement sur VPS
# ========================================

set -e  # Arrêt en cas d'erreur

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction d'affichage
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Vérification des prérequis
check_requirements() {
    log "Vérification des prérequis..."
    
    command -v docker >/dev/null 2>&1 || error "Docker n'est pas installé"
    command -v docker-compose >/dev/null 2>&1 || error "Docker Compose n'est pas installé"
    
    if [ ! -f ".env" ]; then
        error "Fichier .env introuvable. Copiez .env.production.example vers .env et configurez-le."
    fi
    
    success "Tous les prérequis sont satisfaits"
}

# Backup de la base de données
backup_database() {
    log "Création d'un backup de la base de données..."
    
    BACKUP_DIR="backups/$(date +'%Y-%m-%d_%H-%M-%S')"
    mkdir -p "$BACKUP_DIR"
    
    # Backup de chaque base de données
    for DB in admin store website; do
        log "Backup de la base $DB..."
        docker-compose exec -T mysql mysqldump -u root -p"${MYSQL_ROOT_PASSWORD}" "fikaso_${DB}" > "${BACKUP_DIR}/${DB}.sql" 2>/dev/null || warning "Échec du backup de ${DB}"
    done
    
    # Backup des volumes Docker
    log "Backup des volumes de stockage..."
    docker run --rm \
        -v fikaso_admin_storage:/source:ro \
        -v "$(pwd)/${BACKUP_DIR}":/backup \
        alpine tar czf /backup/admin_storage.tar.gz -C /source . 2>/dev/null || warning "Échec du backup admin storage"
    
    docker run --rm \
        -v fikaso_store_storage:/source:ro \
        -v "$(pwd)/${BACKUP_DIR}":/backup \
        alpine tar czf /backup/store_storage.tar.gz -C /source . 2>/dev/null || warning "Échec du backup store storage"
    
    docker run --rm \
        -v fikaso_website_storage:/source:ro \
        -v "$(pwd)/${BACKUP_DIR}":/backup \
        alpine tar czf /backup/website_storage.tar.gz -C /source . 2>/dev/null || warning "Échec du backup website storage"
    
    success "Backups créés dans ${BACKUP_DIR}"
}

# Pull des images
pull_images() {
    log "Téléchargement des nouvelles images Docker..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml pull
    success "Images téléchargées"
}

# Build des images (en local)
build_images() {
    log "Construction des images Docker..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache
    success "Images construites"
}

# Démarrage des services
start_services() {
    log "Démarrage des services..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
    
    log "Attente du démarrage des services..."
    sleep 30
    
    success "Services démarrés"
}

# Exécution des migrations
run_migrations() {
    log "Exécution des migrations..."
    
    read -p "Voulez-vous exécuter les migrations ? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker-compose exec -T admin php artisan migrate --force || warning "Migrations admin échouées"
        docker-compose exec -T store php artisan migrate --force || warning "Migrations store échouées"
        docker-compose exec -T website php artisan migrate --force || warning "Migrations website échouées"
        success "Migrations exécutées"
    else
        warning "Migrations ignorées"
    fi
}

# Optimisation
optimize_apps() {
    log "Optimisation des applications..."
    
    for APP in admin store website; do
        log "Optimisation de ${APP}..."
        docker-compose exec -T "$APP" php artisan config:cache
        docker-compose exec -T "$APP" php artisan route:cache
        docker-compose exec -T "$APP" php artisan view:cache
        docker-compose exec -T "$APP" php artisan optimize
    done
    
    success "Applications optimisées"
}

# Vérification de santé
health_check() {
    log "Vérification de l'état des services..."
    
    # Charger les domaines depuis .env
    source .env
    
    check_url() {
        local url=$1
        local name=$2
        if curl -f -s -o /dev/null "$url"; then
            success "${name} est accessible"
        else
            error "${name} n'est pas accessible à ${url}"
        fi
    }
    
    sleep 10  # Attendre que les services soient prêts
    
    check_url "https://${ADMIN_DOMAIN}" "Admin Panel"
    check_url "https://${STORE_DOMAIN}" "Store Panel"
    check_url "https://${WEBSITE_DOMAIN}" "Website Panel"
    check_url "https://${LANDING_DOMAIN}" "Landing Panel"
    
    success "Tous les services sont opérationnels"
}

# Nettoyage
cleanup() {
    log "Nettoyage des ressources inutilisées..."
    docker image prune -af
    docker volume prune -f
    success "Nettoyage effectué"
}

# Affichage des logs
show_logs() {
    log "Affichage des logs..."
    docker-compose logs --tail=100 -f
}

# Rollback
rollback() {
    error "Rollback en cours..."
    # À implémenter selon vos besoins
}

# Menu principal
main() {
    echo ""
    echo "=========================================="
    echo "  Déploiement eMart/Fikaso"
    echo "=========================================="
    echo ""
    
    PS3='Choisissez une option: '
    options=("Déploiement complet" "Build local" "Pull images" "Démarrer services" "Arrêter services" "Backup BDD" "Migrations" "Optimisation" "Health check" "Voir logs" "Nettoyage" "Quitter")
    
    select opt in "${options[@]}"
    do
        case $opt in
            "Déploiement complet")
                check_requirements
                backup_database
                pull_images
                start_services
                run_migrations
                optimize_apps
                health_check
                success "🎉 Déploiement terminé avec succès!"
                break
                ;;
            "Build local")
                check_requirements
                build_images
                break
                ;;
            "Pull images")
                pull_images
                break
                ;;
            "Démarrer services")
                start_services
                break
                ;;
            "Arrêter services")
                log "Arrêt des services..."
                docker-compose -f docker-compose.yml -f docker-compose.prod.yml down
                success "Services arrêtés"
                break
                ;;
            "Backup BDD")
                backup_database
                break
                ;;
            "Migrations")
                run_migrations
                break
                ;;
            "Optimisation")
                optimize_apps
                break
                ;;
            "Health check")
                health_check
                break
                ;;
            "Voir logs")
                show_logs
                break
                ;;
            "Nettoyage")
                cleanup
                break
                ;;
            "Quitter")
                break
                ;;
            *) 
                error "Option invalide $REPLY"
                ;;
        esac
    done
}

# Gestion des arguments en ligne de commande
if [ $# -eq 0 ]; then
    main
else
    case "$1" in
        deploy)
            check_requirements
            backup_database
            pull_images
            start_services
            run_migrations
            optimize_apps
            health_check
            success "🎉 Déploiement terminé avec succès!"
            ;;
        build)
            check_requirements
            build_images
            ;;
        start)
            start_services
            ;;
        stop)
            docker-compose -f docker-compose.yml -f docker-compose.prod.yml down
            ;;
        backup)
            backup_database
            ;;
        logs)
            show_logs
            ;;
        *)
            echo "Usage: $0 {deploy|build|start|stop|backup|logs}"
            exit 1
            ;;
    esac
fi

