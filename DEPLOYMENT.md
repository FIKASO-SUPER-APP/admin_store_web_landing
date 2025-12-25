# Guide de Déploiement FIKASO sur VPS

Ce guide explique comment déployer les 4 applications FIKASO (Admin Panel, Store Panel, Website Panel, et Landing Panel) sur un VPS en utilisant Docker et Nginx.

## 📋 Prérequis

- Un VPS avec Ubuntu 20.04+ ou Debian 10+
- Accès root ou sudo
- Noms de domaine configurés pointant vers votre VPS
- Minimum 2GB RAM, 2 CPU cores, 20GB de stockage

## 🚀 Installation Initiale

### 1. Connexion au VPS

```bash
ssh root@your-vps-ip
```

### 2. Mise à jour du système

```bash
apt update && apt upgrade -y
```

### 3. Installation de Docker

```bash
# Installation de Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Démarrage de Docker
systemctl start docker
systemctl enable docker

# Installation de Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Vérification
docker --version
docker-compose --version
```

### 4. Installation de Git

```bash
apt install -y git
```

## 📦 Déploiement des Applications

### 1. Cloner le projet

```bash
cd /var/www
git clone <votre-repo-git> fikaso
cd fikaso
```

### 2. Configuration des variables d'environnement

```bash
# Copier le fichier d'exemple
cp env.example .env

# Éditer le fichier .env
nano .env
```

Configurez les variables suivantes :

```env
# MySQL Configuration
MYSQL_ROOT_PASSWORD=votre_mot_de_passe_securise

# Laravel App Keys (générez-les avec: php artisan key:generate)
ADMIN_APP_KEY=base64:votre_cle_admin
STORE_APP_KEY=base64:votre_cle_store
WEBSITE_APP_KEY=base64:votre_cle_website

# Domains Configuration
ADMIN_DOMAIN=admin.votredomaine.com
STORE_DOMAIN=store.votredomaine.com
WEBSITE_DOMAIN=votredomaine.com
LANDING_DOMAIN=www.votredomaine.com
```

### 3. Générer les clés Laravel

```bash
# Pour chaque application, générez une clé
docker run --rm -v $(pwd)/"Admin Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
docker run --rm -v $(pwd)/"Store Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
docker run --rm -v $(pwd)/"Website Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
```

Copiez les clés générées dans votre fichier `.env`.

### 4. Configuration des fichiers .env Laravel

Pour chaque application Laravel, créez/modifiez le fichier `.env` :

**Admin Panel/.env**
```env
APP_NAME="FIKASO Admin"
APP_ENV=production
APP_KEY=base64:votre_cle_admin
APP_DEBUG=false
APP_URL=http://admin.votredomaine.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=emart_admin
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_mysql
```

**Store Panel/.env**
```env
APP_NAME="FIKASO Store"
APP_ENV=production
APP_KEY=base64:votre_cle_store
APP_DEBUG=false
APP_URL=http://store.votredomaine.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=emart_store
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_mysql
```

**Website Panel/.env**
```env
APP_NAME="FIKASO Website"
APP_ENV=production
APP_KEY=base64:votre_cle_website
APP_DEBUG=false
APP_URL=http://votredomaine.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=emart_website
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_mysql
```

### 5. Mise à jour des configurations Nginx

Modifiez les fichiers dans `nginx/conf.d/` pour remplacer `yourdomain.com` par vos vrais domaines.

### 6. Rendre les scripts exécutables

```bash
chmod +x deploy.sh
chmod +x setup-ssl.sh
```

### 7. Déployer les applications

```bash
./deploy.sh start
```

Cette commande va :
- Construire toutes les images Docker
- Démarrer tous les conteneurs
- Créer les bases de données
- Exécuter les migrations
- Optimiser les applications Laravel

## 🔒 Configuration SSL (HTTPS)

### Option 1 : Let's Encrypt (Gratuit - Recommandé)

```bash
# Installer certbot
apt install -y certbot

# Exécuter le script de configuration SSL
./setup-ssl.sh
```

### Option 2 : Certificats personnalisés

1. Placez vos certificats dans `nginx/ssl/`
2. Nommez-les : `domain.crt` et `domain.key`
3. Décommentez les lignes SSL dans les fichiers `nginx/conf.d/*.conf`
4. Redémarrez nginx : `docker-compose restart nginx`

## 🛠️ Gestion des Applications

### Démarrer les applications

```bash
./deploy.sh start
```

### Arrêter les applications

```bash
./deploy.sh stop
```

### Redémarrer les applications

```bash
./deploy.sh restart
```

### Voir les logs

```bash
# Tous les logs
./deploy.sh logs

# Logs d'un service spécifique
./deploy.sh logs admin
./deploy.sh logs nginx
./deploy.sh logs mysql
```

### Mettre à jour les applications

```bash
# Pull les derniers changements
git pull

# Mettre à jour et redémarrer
./deploy.sh update
```

### Backup de la base de données

```bash
./deploy.sh backup
```

Les backups sont stockés dans le dossier `backups/`.

## 🔧 Commandes Docker Compose Utiles

```bash
# Voir l'état des conteneurs
docker-compose ps

# Exécuter une commande dans un conteneur
docker-compose exec admin bash
docker-compose exec admin php artisan migrate

# Reconstruire un conteneur spécifique
docker-compose up -d --build admin

# Voir les logs en temps réel
docker-compose logs -f

# Nettoyer les volumes et images inutilisés
docker system prune -a
```

## 📊 Structure des Services

- **MySQL** : Port 3306 (interne)
- **Admin Panel** : http://admin.votredomaine.com
- **Store Panel** : http://store.votredomaine.com
- **Website Panel** : http://votredomaine.com
- **Landing Panel** : http://www.votredomaine.com
- **Nginx** : Ports 80 (HTTP) et 443 (HTTPS)

## 🐛 Dépannage

### Les migrations échouent

```bash
# Vérifier si MySQL est prêt
docker-compose exec mysql mysql -u root -p -e "SHOW DATABASES;"

# Réexécuter les migrations manuellement
docker-compose exec admin php artisan migrate --force
```

### Erreurs de permissions

```bash
# Corriger les permissions des dossiers storage
docker-compose exec admin chmod -R 775 storage bootstrap/cache
docker-compose exec store chmod -R 775 storage bootstrap/cache
docker-compose exec website chmod -R 775 storage bootstrap/cache
```

### Clear cache Laravel

```bash
docker-compose exec admin php artisan cache:clear
docker-compose exec admin php artisan config:clear
docker-compose exec admin php artisan route:clear
docker-compose exec admin php artisan view:clear
```

### Nginx ne démarre pas

```bash
# Vérifier les logs nginx
docker-compose logs nginx

# Tester la configuration nginx
docker-compose exec nginx nginx -t

# Redémarrer nginx
docker-compose restart nginx
```

### Base de données ne se connecte pas

1. Vérifiez que MySQL est démarré : `docker-compose ps`
2. Vérifiez les credentials dans les fichiers `.env`
3. Vérifiez que le nom de la base de données existe
4. Attendez que MySQL soit complètement démarré (peut prendre 30 secondes au premier démarrage)

## 🔐 Sécurité

### Recommandations importantes

1. **Changez tous les mots de passe par défaut**
2. **Configurez un firewall (UFW)**

```bash
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

3. **Désactivez l'accès root SSH**

```bash
# Dans /etc/ssh/sshd_config
PermitRootLogin no
```

4. **Configurez des backups automatiques**

```bash
# Ajoutez dans crontab -e
0 2 * * * cd /var/www/fikaso && ./deploy.sh backup
```

5. **Surveillez les logs**

```bash
# Installer fail2ban
apt install -y fail2ban
```

## 📈 Monitoring

### Installer Portainer (Interface de gestion Docker)

```bash
docker volume create portainer_data
docker run -d -p 9000:9000 --name portainer \
    --restart=always \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -v portainer_data:/data \
    portainer/portainer-ce
```

Accédez à Portainer sur : `http://votre-vps-ip:9000`

## 📞 Support

Pour toute question ou problème :
- Vérifiez les logs : `./deploy.sh logs`
- Consultez la documentation Docker
- Vérifiez les issues GitHub du projet

## 📝 Notes

- Les volumes Docker persistent les données même après l'arrêt des conteneurs
- Faites des backups réguliers de votre base de données
- Surveillez l'utilisation des ressources avec `docker stats`
- Mettez à jour régulièrement vos images Docker

