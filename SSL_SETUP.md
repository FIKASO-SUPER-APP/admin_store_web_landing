# Guide de Configuration SSL/HTTPS

Ce guide explique comment configurer HTTPS pour tous vos domaines avec Let's Encrypt.

## 📋 Prérequis

1. Vos domaines doivent pointer vers l'IP de votre serveur VPS
2. Les ports 80 et 443 doivent être ouverts dans le firewall
3. Docker et Docker Compose doivent être installés

## 🚀 Configuration SSL en 3 étapes

### Étape 1 : Préparer les dossiers

```bash
mkdir -p certbot/conf
mkdir -p certbot/www
```

### Étape 2 : Démarrer les services (sans HTTPS d'abord)

Pour la première configuration, utilisez temporairement la configuration HTTP :

```bash
# Sauvegarder la config actuelle
cp nginx/proxy.conf nginx/proxy-https.conf

# Utiliser la config HTTP temporaire
cp nginx/proxy-http-only.conf nginx/proxy.conf

# Redémarrer
docker-compose restart nginx_proxy
```

### Étape 3 : Générer les certificats SSL

Utilisez le script automatique :

```bash
chmod +x setup-ssl.sh
./setup-ssl.sh
```

Le script vous demandera :
- Vos domaines (séparés par des espaces)
- Votre email pour Let's Encrypt

**Exemple :**
```
Domaines: admin.fikaso.com store.fikaso.com www.fikaso.com fikaso.com landing.fikaso.com
Email: admin@fikaso.com
```

### Étape 4 : Activer HTTPS

Une fois les certificats générés, utilisez la configuration HTTPS :

```bash
# Restaurer la config HTTPS
cp nginx/proxy-https.conf nginx/proxy.conf

# Redémarrer
docker-compose restart nginx_proxy
```

## 🔧 Configuration manuelle

Si vous préférez configurer manuellement :

### 1. Générer un certificat pour un domaine

```bash
docker-compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email votre-email@example.com \
    --agree-tos \
    --no-eff-email \
    -d admin.fikaso.com \
    -d store.fikaso.com \
    -d www.fikaso.com \
    -d fikaso.com \
    -d landing.fikaso.com
```

### 2. Vérifier les certificats générés

```bash
ls -la certbot/conf/live/
```

Vous devriez voir vos domaines listés.

### 3. Mettre à jour nginx/proxy.conf

Assurez-vous que les chemins des certificats dans `nginx/proxy.conf` correspondent à vos domaines :

```nginx
ssl_certificate /etc/letsencrypt/live/admin.fikaso.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/admin.fikaso.com/privkey.pem;
```

### 4. Redémarrer Nginx

```bash
docker-compose restart nginx_proxy
```

## ✅ Vérification

Testez vos domaines :

```bash
curl -I https://admin.fikaso.com
curl -I https://store.fikaso.com
curl -I https://www.fikaso.com
```

Vous devriez voir `HTTP/2 200` ou une redirection.

## 🔄 Renouvellement automatique

Les certificats Let's Encrypt expirent après 90 jours. Le conteneur `certbot` renouvelle automatiquement les certificats tous les 12 heures.

Pour renouveler manuellement :

```bash
docker-compose run --rm certbot renew
docker-compose restart nginx_proxy
```

## 🐛 Dépannage

### Erreur : "Failed to obtain certificate"

**Causes possibles :**
1. Le domaine ne pointe pas vers votre serveur
2. Le port 80 n'est pas accessible depuis l'extérieur
3. Un autre service utilise le port 80

**Solutions :**
```bash
# Vérifier que le domaine pointe vers votre IP
dig admin.fikaso.com

# Vérifier que le port 80 est ouvert
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Vérifier qu'aucun autre service n'utilise le port 80
sudo netstat -tulpn | grep :80
```

### Les certificats ne se chargent pas

```bash
# Vérifier que les certificats existent
ls -la certbot/conf/live/

# Vérifier les logs Nginx
docker-compose logs nginx_proxy

# Vérifier la syntaxe de la config
docker-compose exec nginx_proxy nginx -t
```

### Erreur "SSL certificate problem"

Assurez-vous que :
1. Les chemins dans `nginx/proxy.conf` sont corrects
2. Les certificats sont bien montés dans le conteneur
3. Les permissions sont correctes

```bash
# Vérifier les volumes dans docker-compose.yml
docker-compose config | grep certbot
```

## 🔒 Sécurité

La configuration inclut :
- ✅ TLS 1.2 et 1.3 uniquement
- ✅ Cipher suites sécurisées
- ✅ HSTS (HTTP Strict Transport Security)
- ✅ Headers de sécurité
- ✅ Redirection HTTP → HTTPS automatique

## 📝 Notes importantes

1. **Première configuration** : Utilisez `proxy-http-only.conf` temporairement pour permettre à Let's Encrypt de valider vos domaines
2. **Renouvellement** : Les certificats sont renouvelés automatiquement, mais vous pouvez vérifier les logs avec `docker-compose logs certbot`
3. **Multi-domaines** : Vous pouvez générer un certificat pour plusieurs domaines en une seule commande (wildcard ou SAN)

## 🌐 Certificats wildcard

Pour utiliser un certificat wildcard (*.fikaso.com), vous devez utiliser la validation DNS au lieu de HTTP :

```bash
docker-compose run --rm certbot certonly \
    --manual \
    --preferred-challenges dns \
    -d *.fikaso.com \
    -d fikaso.com
```

Suivez les instructions pour ajouter le record TXT dans votre DNS.

## 📚 Ressources

- [Documentation Let's Encrypt](https://letsencrypt.org/docs/)
- [Certbot Documentation](https://certbot.eff.org/)
- [Nginx SSL Configuration](https://nginx.org/en/docs/http/configuring_https_servers.html)

