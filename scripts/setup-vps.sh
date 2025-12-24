#!/bin/bash

# ========================================
# Script de configuration initiale du VPS
# eMart/Fikaso
# ========================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() { echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"; }
success() { echo -e "${GREEN}✅ $1${NC}"; }
error() { echo -e "${RED}❌ $1${NC}"; exit 1; }
warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }

echo "=========================================="
echo "  Configuration initiale du VPS"
echo "  eMart/Fikaso"
echo "=========================================="
echo ""

# Vérifier si on est root
if [ "$EUID" -ne 0 ]; then 
    error "Ce script doit être exécuté en tant que root (sudo)"
fi

# 1. Mise à jour du système
log "Mise à jour du système..."
apt-get update
apt-get upgrade -y
success "Système mis à jour"

# 2. Installation des dépendances
log "Installation des dépendances..."
apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    software-properties-common \
    git \
    ufw \
    fail2ban \
    htop \
    ncdu \
    unzip
success "Dépendances installées"

# 3. Installation de Docker
log "Installation de Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
    success "Docker installé"
else
    success "Docker déjà installé"
fi

# 4. Installation de Docker Compose
log "Installation de Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep 'tag_name' | cut -d\" -f4)
    curl -L "https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    success "Docker Compose installé"
else
    success "Docker Compose déjà installé"
fi

# 5. Configuration du firewall
log "Configuration du firewall UFW..."
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
success "Firewall configuré"

# 6. Configuration de Fail2Ban
log "Configuration de Fail2Ban..."
systemctl enable fail2ban
systemctl start fail2ban

cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = ssh
logpath = %(sshd_log)s
backend = %(sshd_backend)s
EOF

systemctl restart fail2ban
success "Fail2Ban configuré"

# 7. Optimisation du système pour Docker
log "Optimisation du système..."
cat >> /etc/sysctl.conf << EOF

# Docker optimizations
vm.max_map_count=262144
fs.file-max=65535
net.core.somaxconn=1024
net.ipv4.ip_forward=1
EOF

sysctl -p
success "Système optimisé"

# 8. Création de l'utilisateur de déploiement
log "Création de l'utilisateur de déploiement..."
read -p "Nom d'utilisateur pour le déploiement [deployer]: " DEPLOY_USER
DEPLOY_USER=${DEPLOY_USER:-deployer}

if id "$DEPLOY_USER" &>/dev/null; then
    warning "L'utilisateur $DEPLOY_USER existe déjà"
else
    useradd -m -s /bin/bash "$DEPLOY_USER"
    usermod -aG docker "$DEPLOY_USER"
    success "Utilisateur $DEPLOY_USER créé"
fi

# 9. Configuration des répertoires
log "Création des répertoires de projet..."
mkdir -p /home/$DEPLOY_USER/fikaso
chown -R $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/fikaso
success "Répertoires créés"

# 10. Configuration de la clé SSH
log "Configuration SSH..."
read -p "Voulez-vous configurer une clé SSH pour $DEPLOY_USER ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    mkdir -p /home/$DEPLOY_USER/.ssh
    chmod 700 /home/$DEPLOY_USER/.ssh
    
    echo "Collez votre clé publique SSH:"
    read SSH_KEY
    echo "$SSH_KEY" > /home/$DEPLOY_USER/.ssh/authorized_keys
    chmod 600 /home/$DEPLOY_USER/.ssh/authorized_keys
    chown -R $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/.ssh
    
    success "Clé SSH configurée"
fi

# 11. Sécurisation SSH
log "Sécurisation SSH..."
cp /etc/ssh/sshd_config /etc/ssh/sshd_config.backup

sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config

systemctl restart sshd
success "SSH sécurisé"

# 12. Configuration des limites de ressources
log "Configuration des limites de ressources..."
cat >> /etc/security/limits.conf << EOF
* soft nofile 65535
* hard nofile 65535
* soft nproc 65535
* hard nproc 65535
EOF
success "Limites de ressources configurées"

# 13. Installation de htpasswd pour Traefik
log "Installation d'Apache2 Utils pour htpasswd..."
apt-get install -y apache2-utils
success "Apache2 Utils installé"

# 14. Configuration du swap (si pas assez de RAM)
log "Configuration du swap..."
if [ $(swapon --show | wc -l) -eq 0 ]; then
    read -p "Taille du swap en Go [2]: " SWAP_SIZE
    SWAP_SIZE=${SWAP_SIZE:-2}
    
    fallocate -l ${SWAP_SIZE}G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    
    success "Swap de ${SWAP_SIZE}Go configuré"
else
    success "Swap déjà configuré"
fi

# 15. Installation de monitoring (optionnel)
log "Installation de monitoring..."
read -p "Voulez-vous installer ctop pour monitorer Docker ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    wget https://github.com/bcicen/ctop/releases/download/v0.7.7/ctop-0.7.7-linux-amd64 -O /usr/local/bin/ctop
    chmod +x /usr/local/bin/ctop
    success "ctop installé"
fi

# 16. Configuration de la rotation des logs Docker
log "Configuration de la rotation des logs Docker..."
cat > /etc/docker/daemon.json << EOF
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  }
}
EOF

systemctl restart docker
success "Rotation des logs configurée"

# 17. Configuration de backup automatique
log "Configuration des backups automatiques..."
mkdir -p /home/$DEPLOY_USER/backups
chown -R $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/backups

cat > /home/$DEPLOY_USER/backup-cron.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/home/$(whoami)/backups/$(date +'%Y-%m-%d_%H-%M-%S')"
mkdir -p "$BACKUP_DIR"
cd ~/fikaso
docker-compose exec -T mysql mysqldump -u root -p"${MYSQL_ROOT_PASSWORD}" --all-databases > "${BACKUP_DIR}/all_databases.sql"
# Garder seulement les 7 derniers backups
find /home/$(whoami)/backups -type d -mtime +7 -exec rm -rf {} +
EOF

chmod +x /home/$DEPLOY_USER/backup-cron.sh
chown $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/backup-cron.sh

# Ajouter au crontab de l'utilisateur (backup quotidien à 3h du matin)
(crontab -u $DEPLOY_USER -l 2>/dev/null; echo "0 3 * * * /home/$DEPLOY_USER/backup-cron.sh") | crontab -u $DEPLOY_USER -

success "Backups automatiques configurés"

# Résumé
echo ""
echo "=========================================="
echo "  ✅ Configuration terminée!"
echo "=========================================="
echo ""
echo "Informations importantes:"
echo "  - Utilisateur de déploiement: $DEPLOY_USER"
echo "  - Répertoire de projet: /home/$DEPLOY_USER/fikaso"
echo "  - Firewall: Ports 80, 443 et SSH ouverts"
echo "  - Docker et Docker Compose installés"
echo "  - Fail2Ban configuré"
echo "  - Backups automatiques: Tous les jours à 3h"
echo ""
echo "Prochaines étapes:"
echo "  1. Connectez-vous en tant que $DEPLOY_USER"
echo "  2. Clonez votre repository dans /home/$DEPLOY_USER/fikaso"
echo "  3. Configurez le fichier .env"
echo "  4. Exécutez le script de déploiement"
echo ""
echo "Commandes utiles:"
echo "  - Surveiller Docker: ctop"
echo "  - Voir les logs: docker-compose logs -f"
echo "  - État du firewall: ufw status"
echo "  - État de Fail2Ban: fail2ban-client status"
echo ""
success "Tout est prêt pour le déploiement! 🚀"

