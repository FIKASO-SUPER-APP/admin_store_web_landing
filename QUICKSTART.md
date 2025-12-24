# 🚀 Guide de Démarrage Rapide

## Pour Commencer Immédiatement

### 1️⃣ Développement Local (5 minutes)

```bash
# Cloner le projet (si pas déjà fait)
cd /Users/bouba/Desktop/work/FIKASO/v2/fikaso/admin_store_web_landing

# Démarrer tous les services
make dev

# Ou sans Make:
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Accéder aux applications:
# - Admin:     http://localhost:8001
# - Store:     http://localhost:8002
# - Website:   http://localhost:8003
# - Landing:   http://localhost:8004
# - phpMyAdmin: http://localhost:8080
```

**Identifiants phpMyAdmin:**
- Serveur: `mysql`
- Utilisateur: `root`
- Mot de passe: `root`

---

### 2️⃣ Déploiement Production (15 minutes)

#### A. Préparer le VPS

```bash
# Sur votre VPS (SSH en tant que root)
ssh root@VOTRE_IP_VPS

# Télécharger et exécuter le script de configuration
wget https://raw.githubusercontent.com/VOTRE_REPO/main/scripts/setup-vps.sh
chmod +x setup-vps.sh
sudo ./setup-vps.sh

# ✅ Le script configure automatiquement Docker, Firewall, Fail2Ban, etc.
```

#### B. Configurer le Projet

```bash
# Se connecter avec l'utilisateur créé
su - deployer  # (ou le nom choisi)

# Cloner le repository
cd ~/fikaso
git clone https://github.com/VOTRE_USERNAME/VOTRE_REPO.git .

# Copier et éditer les variables d'environnement
cp .env.production.example .env
nano .env
```

**⚠️ Variables OBLIGATOIRES à modifier:**

```env
# Vos domaines
ADMIN_DOMAIN=admin.votredomaine.com
STORE_DOMAIN=store.votredomaine.com
WEBSITE_DOMAIN=shop.votredomaine.com
LANDING_DOMAIN=www.votredomaine.com

# Email pour Let's Encrypt
ACME_EMAIL=votre-email@votredomaine.com

# Mots de passe forts (CHANGEZ-LES!)
MYSQL_ROOT_PASSWORD=VotreMotDePasseTresFort123!
ADMIN_DB_PASSWORD=AdminMotDePasse456!
STORE_DB_PASSWORD=StoreMotDePasse789!
WEBSITE_DB_PASSWORD=WebsiteMotDePasse012!
REDIS_PASSWORD=RedisMotDePasse345!

# Clés Laravel (générez-les avec: php artisan key:generate)
ADMIN_APP_KEY=base64:...
STORE_APP_KEY=base64:...
WEBSITE_APP_KEY=base64:...
```

#### C. Importer les Bases de Données

```bash
# Démarrer MySQL
docker-compose up -d mysql
sleep 30

# Importer les données
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_admin_database.sql
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_store_database.sql
docker-compose exec -T mysql mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < emart_website_database.sql
```

#### D. Déployer

```bash
# Option 1: Script automatique (RECOMMANDÉ)
chmod +x scripts/deploy.sh
./scripts/deploy.sh deploy

# Option 2: Manuel
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

#### E. Vérifier

```bash
# Vérifier que les services sont démarrés
docker-compose ps

# Tester les URLs
curl -I https://admin.votredomaine.com
curl -I https://store.votredomaine.com
curl -I https://shop.votredomaine.com
curl -I https://www.votredomaine.com
```

---

### 3️⃣ Configuration CI/CD GitHub (10 minutes)

#### A. Secrets GitHub

Allez dans: **Repository → Settings → Secrets and variables → Actions**

Ajoutez ces secrets:

```
SSH_PRIVATE_KEY         # Votre clé SSH privée pour accéder au VPS
VPS_HOST               # IP du VPS (ex: 1.2.3.4)
VPS_USER               # Utilisateur de déploiement (ex: deployer)
ADMIN_DOMAIN           # admin.votredomaine.com
STORE_DOMAIN           # store.votredomaine.com
WEBSITE_DOMAIN         # shop.votredomaine.com
LANDING_DOMAIN         # www.votredomaine.com
MYSQL_ROOT_PASSWORD    # Votre mot de passe MySQL
```

#### B. Workflow Automatique

1. **Développement**: Push vers `main` → Build des images
2. **Production**: Push vers `production` → Déploiement automatique

```bash
# Créer une branche production
git checkout -b production
git push origin production

# Chaque push vers production déclenchera le déploiement
```

---

## 📋 Commandes Essentielles

### Avec Makefile

```bash
make help           # Voir toutes les commandes
make dev            # Démarrage dev
make prod           # Démarrage production
make logs           # Voir les logs
make db-backup      # Backup DB
make migrate        # Migrations
make cache-clear    # Vider caches
make optimize       # Optimiser
```

### Docker Compose Direct

```bash
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Logs
docker-compose logs -f

# État
docker-compose ps

# Exécuter une commande
docker-compose exec admin php artisan migrate
```

---

## 🔧 Problèmes Courants

### ❌ "Port already in use"

```bash
# Voir ce qui utilise le port
sudo lsof -i :80
sudo lsof -i :443

# Arrêter le service
sudo systemctl stop nginx  # ou apache2
```

### ❌ Certificats SSL ne se génèrent pas

1. Vérifiez que vos domaines pointent vers le VPS:
   ```bash
   dig admin.votredomaine.com
   ```

2. Vérifiez les logs Traefik:
   ```bash
   docker-compose logs traefik
   ```

3. Assurez-vous que les ports 80/443 sont ouverts:
   ```bash
   sudo ufw status
   ```

### ❌ Base de données inaccessible

```bash
# Redémarrer MySQL
docker-compose restart mysql

# Vérifier les logs
docker-compose logs mysql

# Tester la connexion
docker-compose exec mysql mysql -u root -p
```

### ❌ Application Laravel en erreur

```bash
# Vider les caches
make cache-clear

# Vérifier les permissions
docker-compose exec admin chown -R www-data:www-data storage bootstrap/cache

# Voir les logs Laravel
docker-compose exec admin tail -f storage/logs/laravel.log
```

---

## 📞 Besoin d'Aide ?

1. **Documentation complète**: Lisez [DEPLOYMENT.md](DEPLOYMENT.md)
2. **Issues GitHub**: Créez une issue sur le repository
3. **Logs**: Toujours vérifier les logs en premier: `docker-compose logs -f`

---

## ✅ Checklist de Déploiement

- [ ] VPS configuré avec le script `setup-vps.sh`
- [ ] DNS configurés et propagés (4 sous-domaines)
- [ ] Fichier `.env` configuré avec les vraies valeurs
- [ ] Bases de données importées
- [ ] Clés Laravel générées
- [ ] Services Docker démarrés
- [ ] Certificats SSL obtenus (vérifier https://)
- [ ] Applications accessibles et fonctionnelles
- [ ] Backups automatiques configurés
- [ ] CI/CD configuré (si souhaité)
- [ ] Monitoring en place

---

**🎉 Félicitations! Votre plateforme eMart/Fikaso est déployée!**

