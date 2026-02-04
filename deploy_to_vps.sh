#!/bin/bash
# ============================================
# SISMIK DEPLOYMENT SCRIPT
# Domain: sismik.smansera.app
# VPS: 76.13.192.177
# ============================================

set -e  # Stop on error

echo "🚀 Starting SISMIK Deployment..."
echo "================================"

# Step 1: Create directory and clone repository
echo ""
echo "📁 Step 1: Cloning repository..."
sudo mkdir -p /var/www
cd /var/www
sudo rm -rf sismik 2>/dev/null || true
sudo git clone https://github.com/sismiksmansera/sismik.git
cd sismik

# Step 2: Install Composer dependencies
echo ""
echo "📦 Step 2: Installing Composer dependencies..."
sudo composer install --optimize-autoloader --no-dev --no-interaction

# Step 3: Create .env file
echo ""
echo "⚙️ Step 3: Creating .env configuration..."
sudo tee .env > /dev/null << 'ENVFILE'
APP_NAME=SISMIK
APP_ENV=production
APP_KEY=base64:ObnpPfh5Gt3fy9kRk/9rieGDXY0MBA3Hre0yxZk8b2E=
APP_DEBUG=false
APP_URL=https://sismik.smansera.app

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simas_db
DB_USERNAME=sismik
DB_PASSWORD=Sismiksmansera1#

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
ENVFILE

# Step 4: Set permissions
echo ""
echo "🔐 Step 4: Setting file permissions..."
sudo chown -R www-data:www-data /var/www/sismik
sudo chmod -R 755 /var/www/sismik
sudo chmod -R 775 /var/www/sismik/storage
sudo chmod -R 775 /var/www/sismik/bootstrap/cache

# Step 5: Laravel optimization
echo ""
echo "⚡ Step 5: Optimizing Laravel..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan storage:link

# Step 6: Create Nginx configuration
echo ""
echo "🌐 Step 6: Configuring Nginx..."
sudo tee /etc/nginx/sites-available/sismik > /dev/null << 'NGINXCONF'
server {
    listen 80;
    server_name sismik.smansera.app;
    root /var/www/sismik/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINXCONF

# Step 7: Enable site and restart Nginx
echo ""
echo "🔄 Step 7: Enabling site and restarting Nginx..."
sudo ln -sf /etc/nginx/sites-available/sismik /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Done!
echo ""
echo "============================================"
echo "✅ DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "============================================"
echo ""
echo "🌐 Your site: https://sismik.smansera.app"
echo ""
echo "📋 Next steps:"
echo "   1. Point your domain DNS to: 76.13.192.177"
echo "   2. Setup SSL with: sudo certbot --nginx -d sismik.smansera.app"
echo ""
