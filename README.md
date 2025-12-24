# 🛒 eMart / Fikaso - Multi-Panel E-Commerce Platform

[![CI/CD](https://github.com/VOTRE_USERNAME/VOTRE_REPO/workflows/CI/CD%20Pipeline/badge.svg)](https://github.com/VOTRE_USERNAME/VOTRE_REPO/actions)
[![Security Scan](https://github.com/VOTRE_USERNAME/VOTRE_REPO/workflows/Security%20Scan/badge.svg)](https://github.com/VOTRE_USERNAME/VOTRE_REPO/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Plateforme e-commerce complète avec 4 applications interconnectées : Admin, Store, Website et Landing Page. Déployée avec Docker, Nginx/Traefik, et CI/CD automatisé.

## 📋 Table des Matières

- [Aperçu](#aperçu)
- [Architecture](#architecture)
- [Technologies](#technologies)
- [Démarrage Rapide](#démarrage-rapide)
- [Déploiement](#déploiement)
- [Documentation](#documentation)
- [License](#license)

---

## 🎯 Aperçu

**eMart/Fikaso** est une solution e-commerce multi-services comprenant :

### Les 4 Applications

1. **Admin Panel** 👨‍💼
   - Gestion complète de la plateforme
   - Multi-services (livraison, e-commerce, location, taxi, etc.)
   - Gestion des utilisateurs, vendeurs, commandes
   - Dashboard analytique
   - Configuration système

2. **Store Panel** 🏪
   - Interface dédiée aux vendeurs/magasins
   - Gestion des produits et inventaire
   - Suivi des commandes
   - Statistiques de ventes
   - Gestion du profil magasin

3. **Website Panel** 🛍️
   - Site e-commerce public
   - Catalogue de produits
   - Panier et checkout
   - Profils utilisateurs
   - Système de commandes
   - Support multi-services

4. **Landing Page** 🎨
   - Page marketing responsive
   - Présentation de l'application mobile eMart
   - Liens vers App Store / Google Play

---

## 🏗️ Architecture

```
┌──────────────────────────────────────────────────┐
│              Internet / Users                     │
└───────────────────┬──────────────────────────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │  Traefik (Port 443)  │
        │  - SSL Automatique   │
        │  - Load Balancing    │
        └──────────┬───────────┘
                   │
        ┌──────────┴────────────┐
        │                       │
        ▼                       ▼
┌──────────────┐      ┌──────────────┐
│ Laravel Apps │      │Static Assets │
│  - Admin     │      │  - Landing   │
│  - Store     │      └──────────────┘
│  - Website   │
└──────┬───────┘
       │
       ▼
┌──────────────────────────┐
│   Infrastructure         │
│  ┌────────┐ ┌─────────┐ │
│  │ MySQL  │ │ Redis   │ │
│  │ (DB)   │ │ (Cache) │ │
│  └────────┘ └─────────┘ │
└──────────────────────────┘
```

---

## 🛠️ Technologies

### Backend
- **PHP 8.2** - Language
- **Laravel 10** - Framework
- **MySQL 8.0** - Database
- **Redis 7** - Cache & Sessions

### Frontend
- **Bootstrap** - UI Framework
- **jQuery** - JavaScript Library
- **HTML5/CSS3** - Landing Page

### Infrastructure
- **Docker** - Containerization
- **Docker Compose** - Orchestration
- **Traefik v2** - Reverse Proxy & SSL
- **Nginx** - Web Server (dans les conteneurs)
- **Let's Encrypt** - SSL Certificates

### CI/CD
- **GitHub Actions** - Automation
- **Trivy** - Security Scanning
- **PHPUnit** - Testing

### Paiements
- Stripe
- Razorpay
- PayPal
- Paystack
- Xendit

---

## 🚀 Démarrage Rapide

### Prérequis

- Docker 20.10+
- Docker Compose 2.0+
- Git

### Installation Locale (Développement)

```bash
# 1. Cloner le repository
git clone https://github.com/VOTRE_USERNAME/VOTRE_REPO.git
cd VOTRE_REPO

# 2. Démarrer l'environnement de développement
make dev

# Ou sans Make:
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

### Accès aux Applications

Une fois démarrées, les applications sont accessibles sur :

| Application | URL Locale | Port |
|------------|------------|------|
| Admin Panel | http://localhost:8001 | 8001 |
| Store Panel | http://localhost:8002 | 8002 |
| Website Panel | http://localhost:8003 | 8003 |
| Landing Page | http://localhost:8004 | 8004 |
| phpMyAdmin | http://localhost:8080 | 8080 |
| Redis Commander | http://localhost:8081 | 8081 |
| Mailhog | http://localhost:8025 | 8025 |

### Commandes Utiles (avec Makefile)

```bash
# Voir toutes les commandes disponibles
make help

# Démarrer en développement
make dev

# Voir les logs
make logs

# Arrêter les services
make dev-stop

# Exécuter les migrations
make migrate

# Vider les caches
make cache-clear

# Créer un backup de la DB
make db-backup

# Exécuter les tests
make test
```

---

## 📦 Déploiement en Production

### Option 1: Script Automatique

```bash
# 1. Configurer le VPS (une seule fois)
./scripts/setup-vps.sh

# 2. Configurer les variables d'environnement
cp .env.production.example .env
nano .env  # Modifier avec vos valeurs

# 3. Déployer
./scripts/deploy.sh deploy
```

### Option 2: CI/CD avec GitHub Actions

1. Configurez les secrets GitHub (voir [DEPLOYMENT.md](DEPLOYMENT.md))
2. Push vers la branche `production`
3. Le déploiement se fait automatiquement

### Option 3: Manuel avec Docker Compose

```bash
# Build et démarrer
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Vérifier l'état
docker-compose ps

# Voir les logs
docker-compose logs -f
```

---

## 📚 Documentation

- **[Guide de Déploiement Complet](DEPLOYMENT.md)** - Instructions détaillées pour le déploiement
- **[Architecture](docs/architecture.md)** - Détails de l'architecture système
- **[Sécurité](docs/security.md)** - Bonnes pratiques de sécurité
- **[API Documentation](docs/api.md)** - Documentation des APIs
- **[Troubleshooting](docs/troubleshooting.md)** - Résolution de problèmes

---

## 🔒 Sécurité

### Fonctionnalités de Sécurité

- ✅ SSL/TLS automatique avec Let's Encrypt
- ✅ Firewall UFW configuré
- ✅ Fail2Ban pour protection SSH
- ✅ Headers de sécurité HTTP
- ✅ Rate limiting
- ✅ Scan automatique des vulnérabilités
- ✅ Authentification forte
- ✅ Mots de passe hashés
- ✅ Variables d'environnement sécurisées

### Signaler une Vulnérabilité

Si vous découvrez une faille de sécurité, merci de nous contacter à security@votredomaine.com au lieu de créer une issue publique.

---

## 🧪 Tests

```bash
# Tests unitaires
make test

# Tests avec couverture de code
docker-compose exec admin vendor/bin/phpunit --coverage-html coverage

# Lint PHP
docker-compose exec admin ./vendor/bin/phpcs

# Fix code style
docker-compose exec admin ./vendor/bin/phpcbf
```

---

## 📊 Monitoring

### Logs

```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f admin

# Dernières 100 lignes
docker-compose logs --tail=100
```

### Métriques

- **Traefik Dashboard**: `https://traefik.votredomaine.com`
- **phpMyAdmin**: `http://localhost:8080` (dev)
- **Redis Commander**: `http://localhost:8081` (dev)

### Ressources

```bash
# Utilisation des ressources
docker stats

# Avec ctop (plus joli)
ctop
```

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez consulter [CONTRIBUTING.md](CONTRIBUTING.md) pour les détails.

### Workflow

1. Fork le projet
2. Créez une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

---

## 📝 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👥 Équipe

- **Développeur Principal** - [Votre Nom](https://github.com/votre-username)
- **DevOps** - [Nom](https://github.com/username)

---

## 🙏 Remerciements

- Laravel Framework
- Docker & Docker Compose
- Traefik
- Tous les contributeurs open-source

---

## 📞 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](https://github.com/VOTRE_USERNAME/VOTRE_REPO/issues)
- **Email**: support@votredomaine.com

---

**Fait avec ❤️ par l'équipe Fikaso**
