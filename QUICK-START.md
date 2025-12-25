# 🚀 Guide de Démarrage Rapide - FIKASO

## Déploiement en 5 Minutes

### 1. Prérequis VPS

```bash
# Connectez-vous à votre VPS
ssh root@votre-vps-ip

# Installez Docker et Docker Compose
curl -fsSL https://get.docker.com | sh
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
```

### 2. Cloner et Configurer

```bash
# Clonez le projet
cd /var/www
git clone <votre-repo> fikaso
cd fikaso

# Configurez les variables d'environnement
cp env.example .env
nano .env  # Modifiez vos domaines et mots de passe
```

### 3. Générer les Clés Laravel

```bash
# Créez les fichiers .env pour chaque panel
cd "Admin Panel" && cp .env.example .env && cd ..
cd "Store Panel" && cp .env.example .env && cd ..
cd "Website Panel" && cp .env.example .env && cd ..

# Générez les clés (copiez-les dans vos fichiers .env)
docker run --rm -v $(pwd)/"Admin Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
docker run --rm -v $(pwd)/"Store Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
docker run --rm -v $(pwd)/"Website Panel":/app composer:latest sh -c "cd /app && php artisan key:generate --show"
```

### 4. Mettre à Jour les Domaines

```bash
# Remplacez yourdomain.com par vos vrais domaines
sed -i 's/yourdomain.com/votredomaine.com/g' nginx/conf.d/*.conf
sed -i 's/admin.yourdomain.com/admin.votredomaine.com/g' nginx/conf.d/admin.conf
sed -i 's/store.yourdomain.com/store.votredomaine.com/g' nginx/conf.d/store.conf
sed -i 's/www.yourdomain.com/www.votredomaine.com/g' nginx/conf.d/landing.conf
```

### 5. Déployer

```bash
# Rendez le script exécutable
chmod +x deploy.sh

# Démarrez tout
./deploy.sh start
```

### 6. Configuration SSL (Optionnel)

```bash
chmod +x setup-ssl.sh
./setup-ssl.sh
```

## ✅ Vérification

Vos applications sont maintenant accessibles sur :

- **Admin Panel** : http://admin.votredomaine.com
- **Store Panel** : http://store.votredomaine.com
- **Website Panel** : http://votredomaine.com
- **Landing Panel** : http://www.votredomaine.com

## 📋 Configuration DNS

Assurez-vous que vos enregistrements DNS pointent vers votre VPS :

```
Type    Nom                 Valeur
A       @                   Votre-IP-VPS
A       www                 Votre-IP-VPS
A       admin               Votre-IP-VPS
A       store               Votre-IP-VPS
```

## 🛠️ Commandes Utiles

```bash
# Voir les logs
./deploy.sh logs

# Redémarrer
./deploy.sh restart

# Arrêter
./deploy.sh stop

# Backup
./deploy.sh backup

# Mettre à jour
git pull && ./deploy.sh update
```

## 🔥 Problèmes Courants

### Les conteneurs ne démarrent pas

```bash
# Vérifiez les logs
docker-compose logs

# Vérifiez l'état
docker-compose ps
```

### Erreur de connexion à la base de données

```bash
# Attendez 30 secondes que MySQL démarre
sleep 30

# Réessayez
./deploy.sh restart
```

### Port 80 déjà utilisé

```bash
# Trouvez le processus
sudo lsof -i :80

# Arrêtez Apache ou autre serveur web
sudo systemctl stop apache2
# ou
sudo systemctl stop nginx
```

## 📚 Documentation Complète

Pour plus de détails, consultez [DEPLOYMENT.md](DEPLOYMENT.md)

## 🎉 C'est Tout !

Votre plateforme FIKASO est maintenant en ligne ! 🚀

