# Architecture Docker - Fikaso

## Vue d'ensemble

Cette architecture Docker déploie 4 applications sur un serveur VPS avec les composants suivants :

```
┌─────────────────────────────────────────────────────────┐
│                    Nginx Reverse Proxy                  │
│                  (Port 80, 443)                         │
└───────────────┬─────────────────────────────────────────┘
                │
    ┌───────────┼───────────┬──────────────┐
    │           │           │              │
    ▼           ▼           ▼              ▼
┌─────────┐ ┌─────────┐ ┌─────────┐  ┌─────────┐
│ Admin   │ │ Store   │ │ Website │  │ Landing │
│ Panel   │ │ Panel   │ │ Panel   │  │ Panel   │
│         │ │         │ │         │  │         │
│ Nginx   │ │ Nginx   │ │ Nginx   │  │ Nginx   │
│ :8081   │ │ :8082   │ │ :8083   │  │ :8084   │
└────┬────┘ └────┬────┘ └────┬────┘  └─────────┘
     │           │           │
     ▼           ▼           ▼
┌─────────┐ ┌─────────┐ ┌─────────┐
│ Admin   │ │ Store   │ │ Website │
│ PHP-FPM │ │ PHP-FPM │ │ PHP-FPM │
│ :9000   │ │ :9000   │ │ :9000   │
└────┬────┘ └────┬────┘ └────┬────┘
     │           │           │
     └───────────┼───────────┘
                 │
                 ▼
         ┌───────────────┐
         │    MySQL 8.0  │
         │   Port 3306   │
         └───────────────┘
```

## Services Docker

### 1. MySQL (`fikaso_mysql`)
- **Image** : mysql:8.0
- **Port** : 3306
- **Volumes** : 
  - `mysql_data` : Données persistantes
  - `./init-db` : Scripts d'initialisation
- **Bases de données** :
  - `fikaso_admin`
  - `fikaso_store`
  - `fikaso_website`

### 2. Admin Panel

#### PHP-FPM (`fikaso_admin_panel`)
- **Image** : php:8.2-fpm (custom build)
- **Port** : 9000 (interne)
- **Volumes** :
  - Code source monté
  - `admin_storage` : Stockage Laravel
  - `admin_bootstrap_cache` : Cache Laravel

#### Nginx (`fikaso_admin_nginx`)
- **Image** : nginx:alpine
- **Port** : 8081 (exposé)
- **Configuration** : `nginx/admin.conf`
- **Proxy vers** : `admin_panel:9000`

### 3. Store Panel

#### PHP-FPM (`fikaso_store_panel`)
- **Image** : php:8.2-fpm (custom build)
- **Port** : 9000 (interne)
- **Volumes** :
  - Code source monté
  - `store_storage` : Stockage Laravel
  - `store_bootstrap_cache` : Cache Laravel

#### Nginx (`fikaso_store_nginx`)
- **Image** : nginx:alpine
- **Port** : 8082 (exposé)
- **Configuration** : `nginx/store.conf`
- **Proxy vers** : `store_panel:9000`

### 4. Website Panel

#### PHP-FPM (`fikaso_website_panel`)
- **Image** : php:8.2-fpm (custom build)
- **Port** : 9000 (interne)
- **Volumes** :
  - Code source monté
  - `website_storage` : Stockage Laravel
  - `website_bootstrap_cache` : Cache Laravel

#### Nginx (`fikaso_website_nginx`)
- **Image** : nginx:alpine
- **Port** : 8083 (exposé)
- **Configuration** : `nginx/website.conf`
- **Proxy vers** : `website_panel:9000`

### 5. Landing Panel

#### Nginx (`fikaso_landing_nginx`)
- **Image** : nginx:alpine
- **Port** : 8084 (exposé)
- **Configuration** : `nginx/landing.conf`
- **Type** : Site statique HTML

### 6. Nginx Reverse Proxy (`fikaso_nginx_proxy`)
- **Image** : nginx:alpine
- **Ports** : 80, 443
- **Configuration** : `nginx/proxy.conf`
- **Fonction** : Router les requêtes vers les bons services selon le domaine

## Réseau Docker

Tous les services sont connectés au réseau `fikaso_network` (bridge), permettant la communication interne entre conteneurs.

## Volumes persistants

- `mysql_data` : Base de données MySQL
- `admin_storage` : Fichiers uploadés Admin Panel
- `store_storage` : Fichiers uploadés Store Panel
- `website_storage` : Fichiers uploadés Website Panel
- `*_bootstrap_cache` : Cache Laravel pour chaque application

## Optimisations

### Nginx
- Compression Gzip activée
- Cache des fichiers statiques (1 an)
- Headers de sécurité
- Buffer sizes optimisés

### PHP-FPM
- Extensions nécessaires installées
- Opcache activé
- Timeouts configurés

### MySQL
- UTF8MB4 par défaut
- Character set optimisé
- Authentication plugin configuré

## Sécurité

- Les fichiers `.env` ne sont pas dans les images Docker
- Les volumes de stockage sont persistants
- Nginx bloque l'accès aux fichiers cachés
- Headers de sécurité configurés
- Isolation réseau via Docker network

## Performance

- Chaque application a son propre conteneur PHP-FPM
- Nginx sert les fichiers statiques directement
- Cache Laravel optimisé
- Compression Gzip activée
- Connexions MySQL poolées

## Scalabilité

Pour augmenter la capacité :
1. Augmenter les workers PHP-FPM dans les Dockerfiles
2. Ajouter des répliques de conteneurs PHP-FPM
3. Utiliser un load balancer devant Nginx
4. Optimiser MySQL avec des réplicas en lecture

