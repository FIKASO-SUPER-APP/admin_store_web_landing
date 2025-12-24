# Makefile pour simplifier les commandes Docker
# Usage: make [commande]

.PHONY: help dev prod build start stop restart logs clean backup test

# Couleurs pour l'affichage
BLUE=\033[0;34m
GREEN=\033[0;32m
NC=\033[0m # No Color

help: ## Afficher cette aide
	@echo "$(BLUE)Commandes disponibles:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

# ====================
# Développement
# ====================

dev: ## Démarrer l'environnement de développement
	@echo "$(BLUE)🚀 Démarrage de l'environnement de développement...$(NC)"
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
	@echo "$(GREEN)✅ Environnement de développement démarré!$(NC)"
	@echo ""
	@echo "URLs disponibles:"
	@echo "  - Admin Panel:      http://localhost:8001"
	@echo "  - Store Panel:      http://localhost:8002"
	@echo "  - Website Panel:    http://localhost:8003"
	@echo "  - Landing Page:     http://localhost:8004"
	@echo "  - phpMyAdmin:       http://localhost:8080"
	@echo "  - Redis Commander:  http://localhost:8081"
	@echo "  - Mailhog:          http://localhost:8025"

dev-logs: ## Voir les logs en développement
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml logs -f

dev-stop: ## Arrêter l'environnement de développement
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml down

# ====================
# Production
# ====================

prod: ## Démarrer l'environnement de production
	@echo "$(BLUE)🚀 Démarrage de l'environnement de production...$(NC)"
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
	@echo "$(GREEN)✅ Environnement de production démarré!$(NC)"

prod-logs: ## Voir les logs en production
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml logs -f

prod-stop: ## Arrêter l'environnement de production
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml down

# ====================
# Build
# ====================

build: ## Construire toutes les images Docker
	@echo "$(BLUE)🔨 Construction des images Docker...$(NC)"
	docker-compose build --no-cache
	@echo "$(GREEN)✅ Images construites!$(NC)"

build-admin: ## Construire l'image Admin Panel
	docker-compose build --no-cache admin

build-store: ## Construire l'image Store Panel
	docker-compose build --no-cache store

build-website: ## Construire l'image Website Panel
	docker-compose build --no-cache website

build-landing: ## Construire l'image Landing Page
	docker-compose build --no-cache landing

# ====================
# Gestion des services
# ====================

start: ## Démarrer tous les services
	docker-compose up -d

stop: ## Arrêter tous les services
	docker-compose down

restart: ## Redémarrer tous les services
	docker-compose restart

ps: ## Voir l'état des conteneurs
	docker-compose ps

# ====================
# Logs
# ====================

logs: ## Voir tous les logs
	docker-compose logs -f

logs-admin: ## Voir les logs Admin Panel
	docker-compose logs -f admin

logs-store: ## Voir les logs Store Panel
	docker-compose logs -f store

logs-website: ## Voir les logs Website Panel
	docker-compose logs -f website

logs-mysql: ## Voir les logs MySQL
	docker-compose logs -f mysql

logs-traefik: ## Voir les logs Traefik
	docker-compose logs -f traefik

# ====================
# Base de données
# ====================

db-shell: ## Accéder au shell MySQL
	docker-compose exec mysql mysql -u root -p

db-backup: ## Créer un backup de toutes les bases de données
	@echo "$(BLUE)💾 Création du backup...$(NC)"
	@mkdir -p backups
	docker-compose exec -T mysql mysqldump -u root -p"${MYSQL_ROOT_PASSWORD}" --all-databases > backups/backup-$(shell date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✅ Backup créé dans backups/$(NC)"

db-restore: ## Restaurer un backup (Usage: make db-restore FILE=backups/backup.sql)
	@echo "$(BLUE)📥 Restauration du backup...$(NC)"
	docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < $(FILE)
	@echo "$(GREEN)✅ Backup restauré!$(NC)"

# ====================
# Laravel
# ====================

migrate: ## Exécuter les migrations sur toutes les apps
	@echo "$(BLUE)🗄️  Exécution des migrations...$(NC)"
	docker-compose exec admin php artisan migrate --force
	docker-compose exec store php artisan migrate --force
	docker-compose exec website php artisan migrate --force
	@echo "$(GREEN)✅ Migrations exécutées!$(NC)"

migrate-fresh: ## Réinitialiser et migrer toutes les bases
	@echo "$(BLUE)⚠️  Réinitialisation des bases de données...$(NC)"
	docker-compose exec admin php artisan migrate:fresh --force
	docker-compose exec store php artisan migrate:fresh --force
	docker-compose exec website php artisan migrate:fresh --force
	@echo "$(GREEN)✅ Bases de données réinitialisées!$(NC)"

seed: ## Exécuter les seeders
	docker-compose exec admin php artisan db:seed --force
	docker-compose exec store php artisan db:seed --force
	docker-compose exec website php artisan db:seed --force

cache-clear: ## Vider tous les caches Laravel
	@echo "$(BLUE)🧹 Nettoyage des caches...$(NC)"
	docker-compose exec admin php artisan cache:clear
	docker-compose exec admin php artisan config:clear
	docker-compose exec admin php artisan view:clear
	docker-compose exec store php artisan cache:clear
	docker-compose exec store php artisan config:clear
	docker-compose exec store php artisan view:clear
	docker-compose exec website php artisan cache:clear
	docker-compose exec website php artisan config:clear
	docker-compose exec website php artisan view:clear
	@echo "$(GREEN)✅ Caches vidés!$(NC)"

optimize: ## Optimiser toutes les applications Laravel
	@echo "$(BLUE)⚡ Optimisation des applications...$(NC)"
	docker-compose exec admin php artisan optimize
	docker-compose exec store php artisan optimize
	docker-compose exec website php artisan optimize
	@echo "$(GREEN)✅ Applications optimisées!$(NC)"

# ====================
# Shell / Accès
# ====================

shell-admin: ## Accéder au shell du conteneur Admin
	docker-compose exec admin sh

shell-store: ## Accéder au shell du conteneur Store
	docker-compose exec store sh

shell-website: ## Accéder au shell du conteneur Website
	docker-compose exec website sh

# ====================
# Nettoyage
# ====================

clean: ## Nettoyer les conteneurs et volumes
	@echo "$(BLUE)🧹 Nettoyage...$(NC)"
	docker-compose down -v
	@echo "$(GREEN)✅ Nettoyage effectué!$(NC)"

clean-all: ## Nettoyer tout (conteneurs, volumes, images)
	@echo "$(BLUE)🧹 Nettoyage complet...$(NC)"
	docker-compose down -v --rmi all
	docker system prune -af
	@echo "$(GREEN)✅ Nettoyage complet effectué!$(NC)"

# ====================
# Tests
# ====================

test: ## Exécuter les tests
	@echo "$(BLUE)🧪 Exécution des tests...$(NC)"
	docker-compose exec admin vendor/bin/phpunit
	docker-compose exec store vendor/bin/phpunit
	docker-compose exec website vendor/bin/phpunit
	@echo "$(GREEN)✅ Tests terminés!$(NC)"

# ====================
# Monitoring
# ====================

stats: ## Afficher l'utilisation des ressources
	docker stats

health: ## Vérifier l'état de santé des services
	@echo "$(BLUE)🏥 Vérification de l'état des services...$(NC)"
	@docker-compose ps
	@echo ""
	@echo "Traefik Dashboard: http://localhost:8080"

# ====================
# Déploiement
# ====================

deploy: ## Exécuter le script de déploiement
	./scripts/deploy.sh deploy

deploy-build: ## Build et déployer
	./scripts/deploy.sh build
	./scripts/deploy.sh deploy

