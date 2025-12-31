# Guide de Déploiement Fikaso avec Docker

Ce guide explique comment déployer les 4 applications Fikaso sur un serveur VPS en utilisant Docker, Nginx et MySQL.

## Architecture

- **Admin Panel** : Application Laravel pour l'administration
- **Store Panel** : Application Laravel pour les magasins
- **Website Panel** : Application Laravel pour le site web public
- **Landing Panel** : Site statique HTML
- **MySQL** : Base de données
- **Nginx** : Reverse proxy et serveur web

## Prérequis

- Serveur VPS avec Docker et Docker Compose installés
- Domaine(s) configuré(s) pointant vers votre serveur VPS
- Accès SSH au serveur

## Installation

### 1. Cloner le projet sur le serveur

```bash
git clone <votre-repo> /var/www/fikaso
cd /var/www/fikaso
```

### 2. Configurer les variables d'environnement

```bash
cp .env.example .env
nano .env
```

Modifiez les valeurs suivantes :
- `MYSQL_ROOT_PASSWORD` : Mot de passe sécurisé pour MySQL
- `DB_PASSWORD` : Même mot de passe que ci-dessus
- Les noms de domaines si nécessaire

### 3. Configurer les fichiers .env de chaque application Laravel

Pour chaque application (Admin Panel, Store Panel, Website Panel), créez un fichier `.env` :

```bash
# Admin Panel
cd "Admin Panel"
cp .env.example .env
nano .env
```

Configurez les variables suivantes dans chaque `.env` :

```env
APP_NAME=Fikaso Admin
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=http://admin.fikaso.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fikaso_admin  # ou fikaso_store, fikaso_website selon l'app
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_mysql

# Répétez pour Store Panel et Website Panel
```

### 4. Générer les clés d'application Laravel

```bash
# Dans chaque dossier d'application Laravel
docker compose exec admin_panel php artisan key:generate
docker compose exec store_panel php artisan key:generate
docker compose exec website_panel php artisan key:generate
```

### 5. Configurer les permissions

```bash
# Donner les permissions nécessaires
sudo chown -R $USER:$USER .
chmod -R 755 .
chmod -R 775 "Admin Panel/storage" "Store Panel/storage" "Website Panel/storage"
chmod -R 775 "Admin Panel/bootstrap/cache" "Store Panel/bootstrap/cache" "Website Panel/bootstrap/cache"
```

### 6. Construire et démarrer les conteneurs

```bash
docker compose build
docker compose up -d
```

### 7. Initialiser les bases de données

Si vous avez des fichiers SQL d'import :

```bash
# Importer les bases de données
docker compose exec mysql mysql -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_admin < emart_admin_database.sql
docker compose exec mysql mysql -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_store < emart_store_database.sql
docker compose exec mysql mysql -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_website < emart_website_database.sql
```

### 8. Exécuter les migrations Laravel (si nécessaire)

```bash
docker compose exec admin_panel php artisan migrate --force
docker compose exec store_panel php artisan migrate --force
docker compose exec website_panel php artisan migrate --force
```

### 9. Optimiser Laravel pour la production

```bash
# Pour chaque application
docker compose exec admin_panel php artisan config:cache
docker compose exec admin_panel php artisan route:cache
docker compose exec admin_panel php artisan view:cache

docker compose exec store_panel php artisan config:cache
docker compose exec store_panel php artisan route:cache
docker compose exec store_panel php artisan view:cache

docker compose exec website_panel php artisan config:cache
docker compose exec website_panel php artisan route:cache
docker compose exec website_panel php artisan view:cache
```

### 10. Configurer les domaines DNS

Configurez vos DNS pour pointer vers votre serveur VPS :

```
A     admin.fikaso.com     -> VOTRE_IP_VPS
A     store.fikaso.com     -> VOTRE_IP_VPS
A     www.fikaso.com       -> VOTRE_IP_VPS
A     fikaso.com           -> VOTRE_IP_VPS
A     landing.fikaso.com   -> VOTRE_IP_VPS
```

### 11. Mettre à jour la configuration Nginx reverse proxy

Éditez `nginx/proxy.conf` et remplacez les domaines par les vôtres :

```nginx
server_name admin.fikaso.com admin.yourdomain.com;
```

### 12. Configurer SSL/HTTPS (Recommandé)

Consultez le guide complet dans `SSL_SETUP.md` :

```bash
# Méthode rapide avec le script automatique
./setup-ssl.sh
```

**Important** : Pour la première configuration SSL, utilisez temporairement `nginx/proxy-http-only.conf` :

```bash
cp nginx/proxy.conf nginx/proxy-https.conf
cp nginx/proxy-http-only.conf nginx/proxy.conf
docker compose restart nginx_proxy
./setup-ssl.sh
# Puis restaurez la config HTTPS
cp nginx/proxy-https.conf nginx/proxy.conf
docker compose restart nginx_proxy
```

## Accès aux applications

- **Admin Panel** : https://admin.fikaso.com (ou http://VOTRE_IP:8081)
- **Store Panel** : https://store.fikaso.com (ou http://VOTRE_IP:8082)
- **Website Panel** : https://www.fikaso.com (ou http://VOTRE_IP:8083)
- **Landing Panel** : https://landing.fikaso.com (ou http://VOTRE_IP:8084)

**Note** : Toutes les requêtes HTTP sont automatiquement redirigées vers HTTPS une fois SSL configuré.

## Commandes utiles

### Voir les logs
```bash
docker compose logs -f
docker compose logs -f admin_panel
docker compose logs -f mysql
```

### Redémarrer un service
```bash
docker compose restart admin_panel
docker compose restart nginx_proxy
```

### Arrêter tous les services
```bash
docker compose down
```

### Arrêter et supprimer les volumes (ATTENTION : supprime les données)
```bash
docker compose down -v
```

### Accéder au shell d'un conteneur
```bash
docker compose exec admin_panel bash
docker compose exec mysql mysql -uroot -p
```

### Vérifier le statut
```bash
docker compose ps
```

## Configuration SSL/HTTPS

La configuration HTTPS est maintenant intégrée dans le projet. Consultez `SSL_SETUP.md` pour le guide complet.

**Résumé rapide :**
1. Configurez vos DNS pour pointer vers votre serveur
2. Utilisez `./setup-ssl.sh` pour générer automatiquement les certificats
3. Les certificats sont renouvelés automatiquement tous les 12h

## Dépannage

### Les styles ne s'affichent pas

1. Vérifiez les permissions sur les dossiers `public` :
```bash
chmod -R 755 "Admin Panel/public"
```

2. Vérifiez que les fichiers statiques sont bien servis par Nginx
3. Videz le cache du navigateur
4. Vérifiez les logs Nginx : `docker compose logs nginx_proxy`

### Erreur de connexion à la base de données

1. Vérifiez que MySQL est démarré : `docker compose ps mysql`
2. Vérifiez les credentials dans les fichiers `.env`
3. Testez la connexion : `docker compose exec mysql mysql -uroot -p`

### Erreur 502 Bad Gateway

1. Vérifiez que les conteneurs PHP-FPM sont démarrés
2. Vérifiez les logs : `docker compose logs admin_panel`
3. Vérifiez la configuration Nginx

## Maintenance

### Sauvegarder les bases de données

```bash
docker compose exec mysql mysqldump -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_admin > backup_admin_$(date +%Y%m%d).sql
docker compose exec mysql mysqldump -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_store > backup_store_$(date +%Y%m%d).sql
docker compose exec mysql mysqldump -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_website > backup_website_$(date +%Y%m%d).sql
```

### Restaurer une base de données

```bash
docker compose exec -T mysql mysql -uroot -p${MYSQL_ROOT_PASSWORD} fikaso_admin < backup_admin_20240101.sql
```

## Support

Pour toute question ou problème, consultez les logs Docker ou contactez l'équipe de développement.

