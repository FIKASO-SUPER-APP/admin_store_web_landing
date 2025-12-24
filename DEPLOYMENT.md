# 🚀 Guide de Déploiement - eMart/Fikaso

Guide complet pour déployer les 4 applications eMart/Fikaso sur un VPS avec Docker, Nginx/Traefik, et CI/CD.

## 📋 Table des Matières

1. [Architecture](#architecture)
2. [Prérequis](#prérequis)
3. [Configuration Initiale du VPS](#configuration-initiale-du-vps)
4. [Configuration des Applications](#configuration-des-applications)
5. [Déploiement Manuel](#déploiement-manuel)
6. [CI/CD avec GitHub Actions](#cicd-avec-github-actions)
7. [Monitoring et Maintenance](#monitoring-et-maintenance)
8. [Sécurité](#sécurité)
9. [Troubleshooting](#troubleshooting)

---

## 🏗️ Architecture

Le projet contient 4 applications containerisées:

```
┌─────────────────────────────────────────────────────┐
│                    Internet                          │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│         Traefik (Reverse Proxy + SSL)                │
│         - Ports 80/443                               │
│         - Let's Encrypt automatique                  │
└─┬──────────┬──────────┬──────────┬───────────────────┘
  │          │          │          │
  ▼          ▼          ▼          ▼
┌────┐    ┌────┐    ┌────┐    ┌────┐
│Admin    │Store    │Website  │Landing
│Panel│    │Panel│    │Panel │    │Page │
└─┬──┘    └─┬──┘    └─┬───┘    └────┘
  │          │          │
  └──────────┴──────────┘
             │
    ┌────────┴────────┐
    │                 │
    ▼                 ▼
┌────────┐      ┌────────┐
│ MySQL  │      │ Redis  │
└────────┘      └────────┘
```

### Applications

1. **Admin Panel** (`admin.votredomaine.com`) - Interface d'administration
2. **Store Panel** (`store.votredomaine.com`) - Interface vendeurs/magasins
3. **Website Panel** (`shop.votredomaine.com`) - Site e-commerce public
4. **Landing Page** (`www.votredomaine.com`) - Page marketing statique

### Services Partagés

- **MySQL 8.0** - Base de données (3 DB séparées)
- **Redis 7** - Cache et sessions
- **Traefik v2** - Reverse proxy avec SSL automatique

---

## 📦 Prérequis

### Sur votre VPS

- **OS**: Ubuntu 20.04/22.04 LTS (recommandé) ou Debian 11+
- **RAM**: Minimum 4GB (8GB recommandé)
- **Stockage**: Minimum 40GB SSD
- **CPU**: 2 cores minimum (4 cores recommandé)
- **Domaines**: 4 sous-domaines configurés (DNS)

### Sur votre machine locale

- Git
- Docker et Docker Compose (pour tests locaux)
- SSH client

### Domaines DNS

Configurez les enregistrements A pour:
- `admin.votredomaine.com` → IP_VPS
- `store.votredomaine.com` → IP_VPS
- `shop.votredomaine.com` → IP_VPS
- `www.votredomaine.com` → IP_VPS
- `traefik.votredomaine.com` → IP_VPS (optionnel, pour le dashboard)

---

## 🔧 Configuration Initiale du VPS

### Étape 1: Connexion au VPS

```bash
ssh root@VOTRE_IP_VPS
```

### Étape 2: Exécuter le script d'installation

```bash
# Télécharger le script
wget https://raw.githubusercontent.com/VOTRE_REPO/main/scripts/setup-vps.sh

# Rendre exécutable
chmod +x setup-vps.sh

# Exécuter (en tant que root)
sudo ./setup-vps.sh
```

Ce script configure automatiquement:
- ✅ Mise à jour du système
- ✅ Installation de Docker et Docker Compose
- ✅ Configuration du firewall (UFW)
- ✅ Installation de Fail2Ban
- ✅ Création d'un utilisateur de déploiement
- ✅ Configuration SSH sécurisée
- ✅ Configuration du swap
- ✅ Backups automatiques quotidiens

### Étape 3: Cloner le repository

```bash
# Se connecter avec l'utilisateur de déploiement
su - deployer  # ou le nom d'utilisateur que vous avez choisi

# Cloner le repository
cd ~/fikaso
git clone https://github.com/VOTRE_USERNAME/VOTRE_REPO.git .
```

---

## ⚙️ Configuration des Applications

### Étape 1: Copier le fichier d'environnement

```bash
cp .env.production.example .env
```

### Étape 2: Éditer le fichier .env

```bash
nano .env
```

**Configuration minimale obligatoire:**

```env
# Domaines
ADMIN_DOMAIN=admin.votredomaine.com
STORE_DOMAIN=store.votredomaine.com
WEBSITE_DOMAIN=shop.votredomaine.com
LANDING_DOMAIN=www.votredomaine.com

# Email pour Let's Encrypt
ACME_EMAIL=votre-email@votredomaine.com

# Mots de passe (CHANGEZ-LES!)
MYSQL_ROOT_PASSWORD=VotreMotDePasseSecurise123!
ADMIN_DB_PASSWORD=AdminDbPassword123!
STORE_DB_PASSWORD=StoreDbPassword123!
WEBSITE_DB_PASSWORD=WebsiteDbPassword123!
REDIS_PASSWORD=RedisPassword123!
```

### Étape 3: Générer les clés Laravel

```bash
# Pour chaque application, générez une clé unique
# Vous pouvez utiliser cette commande en local ou générer manuellement
php artisan key:generate --show

# Ajoutez les clés dans .env
ADMIN_APP_KEY=base64:VotreCleGeneree==
STORE_APP_KEY=base64:VotreCleGeneree==
WEBSITE_APP_KEY=base64:VotreCleGeneree==
```

### Étape 4: Générer le hash pour Traefik Dashboard

```bash
# Installer apache2-utils si pas déjà fait
sudo apt-get install apache2-utils

# Générer le hash (remplacez 'admin' et 'votre_password')
echo $(htpasswd -nb admin votre_password) | sed -e s/\\$/\\$\\$/g

# Copiez le résultat dans .env
TRAEFIK_AUTH_USER=admin:$$apr1$$xyz$$leHashGenere
```

### Étape 5: Créer le répertoire pour les configs Traefik

```bash
mkdir -p traefik
```

### Étape 6: Importer les bases de données

```bash
# Démarrer uniquement MySQL temporairement
docker-compose up -d mysql

# Attendre que MySQL soit prêt
sleep 30

# Importer les bases de données
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_admin_database.sql
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_store_database.sql
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_website_database.sql

# Créer les utilisateurs de base de données
docker-compose exec mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "
CREATE USER IF NOT EXISTS 'admin_user_fikaso'@'%' IDENTIFIED BY '${ADMIN_DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'store_user_fikaso'@'%' IDENTIFIED BY '${STORE_DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'website_user_fikaso'@'%' IDENTIFIED BY '${WEBSITE_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON fikaso_admin.* TO 'admin_user'@'%';
GRANT ALL PRIVILEGES ON fikaso_store.* TO 'store_user'@'%';
GRANT ALL PRIVILEGES ON fikaso_website.* TO 'website_user'@'%';
FLUSH PRIVILEGES;
"
```

---

## 🚀 Déploiement Manuel

### Option 1: Utiliser le script de déploiement

```bash
# Rendre le script exécutable
chmod +x scripts/deploy.sh

# Lancer le déploiement complet
./scripts/deploy.sh deploy
```

Le script va:
1. ✅ Vérifier les prérequis
2. ✅ Créer un backup de la base de données
3. ✅ Pull/Build les images Docker
4. ✅ Démarrer les services
5. ✅ Exécuter les migrations (si demandé)
6. ✅ Optimiser les applications
7. ✅ Vérifier l'état de santé

### Option 2: Commandes Docker Compose manuelles

```bash
# Build les images
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build

# Démarrer tous les services
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Vérifier l'état
docker-compose ps

# Voir les logs
docker-compose logs -f

# Exécuter les migrations
docker-compose exec admin php artisan migrate --force
docker-compose exec store php artisan migrate --force
docker-compose exec website php artisan migrate --force

# Optimiser les applications
docker-compose exec admin php artisan optimize
docker-compose exec store php artisan optimize
docker-compose exec website php artisan optimize
```

### Vérification

Testez l'accès à vos applications:

```bash
curl -I https://admin.votredomaine.com
curl -I https://store.votredomaine.com
curl -I https://shop.votredomaine.com
curl -I https://www.votredomaine.com
```

Tous devraient retourner `HTTP/2 200` (ou 302/301 pour les redirections).

---

## 🔄 CI/CD avec GitHub Actions

### Étape 1: Configurer les secrets GitHub

Dans votre repository GitHub, allez dans `Settings` → `Secrets and variables` → `Actions` et ajoutez:

```
SSH_PRIVATE_KEY         # Clé privée SSH pour se connecter au VPS
VPS_HOST                # IP ou hostname du VPS
VPS_USER                # Utilisateur de déploiement (ex: deployer)
ADMIN_DOMAIN            # admin.votredomaine.com
STORE_DOMAIN            # store.votredomaine.com
WEBSITE_DOMAIN          # shop.votredomaine.com
LANDING_DOMAIN          # www.votredomaine.com
MYSQL_ROOT_PASSWORD     # Mot de passe root MySQL
ADMIN_DB_PASSWORD       # Mot de passe DB admin
STORE_DB_PASSWORD       # Mot de passe DB store
WEBSITE_DB_PASSWORD     # Mot de passe DB website
REDIS_PASSWORD          # Mot de passe Redis
SLACK_WEBHOOK           # (Optionnel) Pour les notifications
SONAR_TOKEN             # (Optionnel) Pour SonarCloud
```

### Étape 2: Workflow de déploiement

Le workflow `.github/workflows/deploy.yml` se déclenche automatiquement:
- ✅ Sur push vers `main` → Build seulement
- ✅ Sur push vers `production` → Build + Déploiement
- ✅ Manuellement via GitHub Actions UI

### Étape 3: Processus de déploiement

1. **Pull Request** → Branche `feature` vers `main`
2. **Tests automatiques** → PHPUnit, linting
3. **Merge vers `main`** → Build des images Docker
4. **Push vers `production`** → Déploiement automatique sur VPS
5. **Health checks** → Vérification que tout fonctionne

### Commandes utiles

```bash
# Déclencher un déploiement manuel
gh workflow run deploy.yml

# Voir les logs d'une exécution
gh run view

# Lister les runs
gh run list --workflow=deploy.yml
```

---

## 📊 Monitoring et Maintenance

### Surveiller les conteneurs

```bash
# Voir les conteneurs en cours d'exécution
docker-compose ps

# Voir l'utilisation des ressources
docker stats

# Utiliser ctop (installé par le script setup)
ctop
```

### Logs

```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f admin
docker-compose logs -f mysql
docker-compose logs -f traefik

# Dernières 100 lignes
docker-compose logs --tail=100
```

### Backups

Les backups automatiques sont configurés quotidiennement à 3h du matin:

```bash
# Backup manuel
docker-compose exec mysql mysqldump -u root -p"${MYSQL_ROOT_PASSWORD}" \
  --all-databases > backup-$(date +%Y%m%d).sql

# Restaurer un backup
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" \
  < backup-20231225.sql
```

### Mises à jour

```bash
# Pull les dernières images
docker-compose pull

# Redémarrer avec les nouvelles images
docker-compose up -d

# Nettoyage
docker image prune -af
docker volume prune -f
```

---

## 🔒 Sécurité

### Checklist de Sécurité

- ✅ Firewall activé (UFW)
- ✅ Fail2Ban pour protection SSH
- ✅ SSL/TLS automatique avec Let's Encrypt
- ✅ Connexion SSH par clé uniquement (pas de mot de passe)
- ✅ Root login SSH désactivé
- ✅ Mots de passe forts pour les bases de données
- ✅ Variables sensibles dans .env (pas dans le code)
- ✅ Headers de sécurité HTTP (via Traefik)
- ✅ Rate limiting configuré
- ✅ Logs rotatifs

### Recommandations Supplémentaires

1. **Changer le port SSH** (optionnel mais recommandé)
   ```bash
   sudo nano /etc/ssh/sshd_config
   # Port 2222  # Au lieu de 22
   sudo systemctl restart sshd
   sudo ufw allow 2222/tcp
   sudo ufw delete allow 22/tcp
   ```

2. **Activer l'authentification 2FA** pour SSH
   ```bash
   sudo apt-get install libpam-google-authenticator
   google-authenticator
   ```

3. **Scanner régulièrement** les vulnérabilités
   - GitHub Security Scan (automatique)
   - Trivy pour les images Docker (automatique via CI/CD)

4. **Monitorer les logs** avec un service externe (Sentry, LogRocket, etc.)

---

## 🔧 Troubleshooting

### Les conteneurs ne démarrent pas

```bash
# Vérifier les logs
docker-compose logs

# Vérifier l'état
docker-compose ps

# Redémarrer proprement
docker-compose down
docker-compose up -d
```

### Certificats SSL ne se génèrent pas

```bash
# Vérifier les logs Traefik
docker-compose logs traefik

# Vérifier que les domaines pointent vers le VPS
dig admin.votredomaine.com

# Vérifier les ports 80/443
sudo netstat -tlnp | grep -E ':(80|443)'
```

### Base de données inaccessible

```bash
# Vérifier que MySQL est démarré
docker-compose ps mysql

# Tester la connexion
docker-compose exec mysql mysql -u root -p

# Recréer le conteneur MySQL si nécessaire
docker-compose stop mysql
docker-compose rm mysql
docker-compose up -d mysql
```

### Application Laravel en erreur

```bash
# Vérifier les logs Laravel
docker-compose exec admin tail -f storage/logs/laravel.log

# Regénérer les caches
docker-compose exec admin php artisan config:clear
docker-compose exec admin php artisan cache:clear
docker-compose exec admin php artisan view:clear
docker-compose exec admin php artisan optimize

# Vérifier les permissions
docker-compose exec admin chown -R www-data:www-data storage bootstrap/cache
```

### Espace disque plein

```bash
# Vérifier l'utilisation
df -h

# Nettoyer Docker
docker system prune -a --volumes

# Nettoyer les logs
docker-compose down
sudo rm -rf /var/lib/docker/containers/*/*-json.log
docker-compose up -d
```

### Problèmes de performance

```bash
# Vérifier l'utilisation des ressources
htop
docker stats

# Optimiser les bases de données
docker-compose exec mysql mysqlcheck --optimize --all-databases -u root -p

# Vider le cache Redis
docker-compose exec redis redis-cli -a "${REDIS_PASSWORD}" FLUSHALL
```

---

## 📞 Support

Pour obtenir de l'aide:

1. Consultez les logs: `docker-compose logs -f`
2. Vérifiez les issues GitHub du projet
3. Contactez l'équipe de développement

---

## 📝 Notes Importantes

- **Ne commitez JAMAIS** le fichier `.env` dans Git
- **Sauvegardez régulièrement** vos bases de données
- **Testez toujours** en local avant de déployer en production
- **Surveillez** les logs et les performances
- **Mettez à jour** régulièrement les dépendances et les images Docker

---

**Bon déploiement! 🚀**

