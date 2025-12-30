# Guide de Démarrage Rapide - Fikaso Docker

## 🚀 Déploiement en 5 minutes

### 1. Prérequis
- Docker et Docker Compose installés
- Au moins 4GB de RAM disponible
- Ports 80, 443, 3306, 8081-8084 disponibles

### 2. Configuration rapide

```bash
# 1. Cloner ou télécharger le projet
cd /var/www/fikaso  # ou votre répertoire

# 2. Créer le fichier .env
cp .env.example .env
nano .env  # Modifier les mots de passe MySQL

# 3. Configurer les .env de chaque application Laravel
# Pour chaque dossier (Admin Panel, Store Panel, Website Panel):
cd "Admin Panel"
cp .env.example .env
nano .env  # Configurer DB_HOST=mysql, DB_DATABASE, etc.
cd ..

# 4. Lancer le déploiement automatique
./deploy.sh
```

### 3. Importer les bases de données (si vous avez les fichiers SQL)

```bash
./import-databases.sh
```

### 4. Configurer SSL/HTTPS (Recommandé)

```bash
# 1. Assurez-vous que vos domaines pointent vers votre serveur
# 2. Utilisez le script automatique
./setup-ssl.sh

# Le script vous demandera vos domaines et votre email
```

**Note** : Pour la première configuration SSL, vous devrez peut-être utiliser temporairement la configuration HTTP :
```bash
cp nginx/proxy-http-only.conf nginx/proxy.conf
docker-compose restart nginx_proxy
./setup-ssl.sh
# Puis restaurez HTTPS
cp nginx/proxy-https.conf nginx/proxy.conf
docker-compose restart nginx_proxy
```

### 5. Accéder aux applications

- **Admin Panel** : https://admin.fikaso.com ou http://localhost:8081
- **Store Panel** : https://store.fikaso.com ou http://localhost:8082  
- **Website Panel** : https://www.fikaso.com ou http://localhost:8083
- **Landing Panel** : https://landing.fikaso.com ou http://localhost:8084

**Toutes les requêtes HTTP sont automatiquement redirigées vers HTTPS une fois SSL configuré.**

## 📋 Commandes essentielles

```bash
# Voir les logs
docker-compose logs -f

# Redémarrer un service
docker-compose restart admin_panel

# Arrêter tous les services
docker-compose down

# Redémarrer tous les services
docker-compose restart

# Voir le statut
docker-compose ps

# Renouveler les certificats SSL manuellement
docker-compose run --rm certbot renew
docker-compose restart nginx_proxy
```

## ⚙️ Configuration des domaines

1. **Configurer DNS** : Pointez vos domaines vers l'IP du serveur VPS
2. **Modifier nginx/proxy.conf** : Remplacez les domaines par les vôtres
3. **Redémarrer le reverse proxy** : `docker-compose restart nginx_proxy`

## 🔒 Configuration SSL (Déjà intégrée)

La configuration HTTPS est déjà intégrée ! Il suffit de :

1. Configurer vos DNS
2. Exécuter `./setup-ssl.sh`
3. Les certificats sont renouvelés automatiquement

Consultez `SSL_SETUP.md` pour plus de détails.

## 🐛 Dépannage

### Les styles ne s'affichent pas
```bash
# Vérifier les permissions
chmod -R 755 "Admin Panel/public"
docker-compose restart admin_nginx
```

### Erreur de connexion MySQL
```bash
# Vérifier que MySQL est démarré
docker-compose ps mysql

# Tester la connexion
docker-compose exec mysql mysql -uroot -p
```

### Erreur 502 Bad Gateway
```bash
# Vérifier les logs
docker-compose logs admin_panel
docker-compose logs admin_nginx

# Redémarrer les services
docker-compose restart admin_panel admin_nginx
```

### Problème avec SSL
```bash
# Vérifier les certificats
ls -la certbot/conf/live/

# Vérifier les logs Certbot
docker-compose logs certbot

# Vérifier la config Nginx
docker-compose exec nginx_proxy nginx -t
```

## 📚 Documentation complète

- **Déploiement complet** : `README_DEPLOYMENT.md`
- **Configuration SSL** : `SSL_SETUP.md`
- **Architecture** : `ARCHITECTURE.md`
