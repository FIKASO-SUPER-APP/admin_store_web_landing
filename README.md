# 🚀 FIKASO - Plateforme Multi-Applications E-Commerce

FIKASO est une plateforme e-commerce complète composée de 4 applications :

1. **Admin Panel** - Interface d'administration
2. **Store Panel** - Interface pour les vendeurs
3. **Website Panel** - Site web client
4. **Landing Panel** - Page d'atterrissage

## 📚 Documentation

- **[Guide de Démarrage Rapide](QUICK-START.md)** - Déploiement en 5 minutes
- **[Guide de Déploiement Complet](DEPLOYMENT.md)** - Documentation détaillée

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Nginx Reverse Proxy                   │
│                    (Port 80/443)                         │
└────────┬────────────┬────────────┬────────────┬─────────┘
         │            │            │            │
    ┌────▼───┐  ┌────▼───┐  ┌────▼───┐  ┌────▼───┐
    │ Admin  │  │ Store  │  │Website │  │Landing │
    │ Panel  │  │ Panel  │  │ Panel  │  │ Panel  │
    │ (PHP)  │  │ (PHP)  │  │ (PHP)  │  │ (HTML) │
    └────┬───┘  └────┬───┘  └────┬───┘  └────────┘
         │            │            │
         └────────────┴────────────┘
                      │
                 ┌────▼────┐
                 │  MySQL  │
                 │  (8.0)  │
                 └─────────┘
```

## 🚀 Déploiement Rapide

```bash
# 1. Cloner le projet
git clone <votre-repo> fikaso && cd fikaso

# 2. Configuration
cp env.example .env
nano .env  # Modifiez vos paramètres

# 3. Déployer
chmod +x deploy.sh
./deploy.sh start
```

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8.1 + Laravel
- **Frontend** : HTML, CSS, JavaScript
- **Base de données** : MySQL 8.0
- **Conteneurisation** : Docker + Docker Compose
- **Reverse Proxy** : Nginx
- **SSL** : Let's Encrypt (Certbot)

## 📦 Composants Docker

| Service | Image | Port | Description |
|---------|-------|------|-------------|
| admin | Custom (PHP 8.1-FPM + Nginx) | - | Panel d'administration |
| store | Custom (PHP 8.1-FPM + Nginx) | - | Panel vendeur |
| website | Custom (PHP 8.1-FPM + Nginx) | - | Site web client |
| landing | Custom (Nginx Alpine) | - | Page d'atterrissage |
| mysql | mysql:8.0 | 3306 | Base de données |
| nginx | nginx:alpine | 80, 443 | Reverse proxy |

## 🔧 Scripts Disponibles

| Script | Description |
|--------|-------------|
| `./deploy.sh start` | Démarrer tous les services |
| `./deploy.sh stop` | Arrêter tous les services |
| `./deploy.sh restart` | Redémarrer tous les services |
| `./deploy.sh logs` | Afficher les logs |
| `./deploy.sh update` | Mettre à jour les applications |
| `./deploy.sh backup` | Backup de la base de données |
| `./setup-ssl.sh` | Configurer SSL/HTTPS |

## 🌐 URLs des Applications

Après le déploiement, vos applications seront accessibles sur :

- **Admin** : `http://admin.votredomaine.com`
- **Store** : `http://store.votredomaine.com`
- **Website** : `http://votredomaine.com`
- **Landing** : `http://www.votredomaine.com`

## 📋 Prérequis

- VPS avec Ubuntu 20.04+ ou Debian 10+
- Docker & Docker Compose
- Minimum 2GB RAM, 2 CPU cores
- Noms de domaine configurés
- Accès root/sudo

## 🔒 Sécurité

- ✅ Variables d'environnement séparées
- ✅ Support SSL/TLS (Let's Encrypt)
- ✅ Headers de sécurité Nginx
- ✅ Isolation des conteneurs Docker
- ✅ Gestion des permissions
- ✅ Backups automatiques

## 📊 Monitoring

Les logs sont disponibles via :

```bash
# Tous les logs
./deploy.sh logs

# Un service spécifique
./deploy.sh logs admin
./deploy.sh logs nginx
./deploy.sh logs mysql

# Logs en temps réel
docker-compose logs -f
```

## 🔄 Mise à Jour

```bash
# Pull les derniers changements
git pull

# Mettre à jour et redémarrer
./deploy.sh update
```

## 💾 Backup

```bash
# Backup manuel
./deploy.sh backup

# Backup automatique (crontab)
0 2 * * * cd /var/www/fikaso && ./deploy.sh backup
```

## 🐛 Dépannage

### Les conteneurs ne démarrent pas
```bash
docker-compose ps
docker-compose logs
```

### Erreur de base de données
```bash
docker-compose restart mysql
docker-compose exec mysql mysql -u root -p
```

### Erreur de permissions
```bash
docker-compose exec admin chmod -R 775 storage bootstrap/cache
```

### Clear cache Laravel
```bash
docker-compose exec admin php artisan cache:clear
docker-compose exec admin php artisan config:clear
```

## 📖 Structure des Fichiers

```
.
├── Admin Panel/              # Application Admin Laravel
│   ├── Dockerfile
│   └── docker/nginx.conf
├── Store Panel/              # Application Store Laravel
│   ├── Dockerfile
│   └── docker/nginx.conf
├── Website Panel/            # Application Website Laravel
│   ├── Dockerfile
│   └── docker/nginx.conf
├── Landing Panel/            # Application Landing HTML
│   ├── Dockerfile
│   └── nginx.conf
├── nginx/                    # Configuration Nginx
│   ├── nginx.conf
│   ├── conf.d/
│   │   ├── admin.conf
│   │   ├── store.conf
│   │   ├── website.conf
│   │   └── landing.conf
│   └── ssl/                  # Certificats SSL
├── docker-compose.yml        # Orchestration Docker
├── deploy.sh                 # Script de déploiement
├── setup-ssl.sh             # Script SSL
├── env.example              # Variables d'environnement
├── DEPLOYMENT.md            # Guide complet
├── QUICK-START.md           # Guide rapide
└── README.md                # Ce fichier
```

## 🤝 Contribution

1. Fork le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

## 👥 Support

Pour toute question ou problème :
- Consultez la [Documentation](DEPLOYMENT.md)
- Vérifiez les logs : `./deploy.sh logs`
- Contactez l'équipe de support

## 🎉 Remerciements

Merci d'utiliser FIKASO ! 🚀

---

**Note** : Assurez-vous de sécuriser votre production en changeant tous les mots de passe par défaut et en configurant SSL.

