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

### 4. Accéder aux applications

- **Admin Panel** : http://localhost:8081 ou http://admin.fikaso.com
- **Store Panel** : http://localhost:8082 ou http://store.fikaso.com  
- **Website Panel** : http://localhost:8083 ou http://www.fikaso.com
- **Landing Panel** : http://localhost:8084 ou http://landing.fikaso.com

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
```

## ⚙️ Configuration des domaines

1. **Configurer DNS** : Pointez vos domaines vers l'IP du serveur VPS
2. **Modifier nginx/proxy.conf** : Remplacez les domaines par les vôtres
3. **Redémarrer le reverse proxy** : `docker-compose restart nginx_proxy`

## 🔒 Configuration SSL (Recommandé)

```bash
# Installer Certbot
sudo apt-get install certbot python3-certbot-nginx

# Générer les certificats
sudo certbot --nginx -d admin.fikaso.com -d store.fikaso.com -d www.fikaso.com
```

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

## 📚 Documentation complète

Consultez `README_DEPLOYMENT.md` pour la documentation complète.

