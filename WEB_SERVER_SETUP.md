# Web Server Setup Guide

## Overview

This guide covers the setup and configuration of web servers (Nginx and Apache) for the HEAC CMS application, including SSL certificates, PHP-FPM, and security hardening.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Nginx Setup](#nginx-setup)
3. [Apache Setup](#apache-setup)
4. [PHP-FPM Configuration](#php-fpm-configuration)
5. [SSL Certificate Setup](#ssl-certificate-setup)
6. [Security Hardening](#security-hardening)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring and Logging](#monitoring-and-logging)
9. [Troubleshooting](#troubleshooting)

## Prerequisites

### System Requirements

- Ubuntu 20.04 LTS or later (or equivalent)
- Root or sudo access
- Domain name pointing to server IP
- Minimum 2GB RAM
- 20GB disk space

### Required Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y software-properties-common curl wget git unzip
```

## Nginx Setup

### 1. Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
```

### 2. Configure Nginx

```bash
# Copy configuration file
sudo cp nginx.conf /etc/nginx/sites-available/heac-cms

# Update server_name in the config
sudo nano /etc/nginx/sites-available/heac-cms
# Replace heac.example.com with your actual domain

# Create symlink to enable site
sudo ln -s /etc/nginx/sites-available/heac-cms /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### 3. Nginx Optimization

Edit `/etc/nginx/nginx.conf`:

```nginx
user www-data;
worker_processes auto;
worker_rlimit_nofile 65535;
pid /run/nginx.pid;

events {
    worker_connections 4096;
    use epoll;
    multi_accept on;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;
    
    # Buffer Settings
    client_body_buffer_size 128k;
    client_max_body_size 20M;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 16k;
    
    # Timeout Settings
    client_body_timeout 12;
    client_header_timeout 12;
    send_timeout 10;
    
    # Include other configs
    include /etc/nginx/mime.types;
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

## Apache Setup

### 1. Install Apache

```bash
# Install Apache
sudo apt install -y apache2

# Start and enable Apache
sudo systemctl start apache2
sudo systemctl enable apache2

# Check status
sudo systemctl status apache2
```

### 2. Enable Required Modules

```bash
# Enable required Apache modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo a2enmod proxy
sudo a2enmod proxy_fcgi
sudo a2enmod setenvif

# Restart Apache
sudo systemctl restart apache2
```

### 3. Configure Apache

```bash
# Copy configuration file
sudo cp apache.conf /etc/apache2/sites-available/heac-cms.conf

# Update ServerName in the config
sudo nano /etc/apache2/sites-available/heac-cms.conf
# Replace heac.example.com with your actual domain

# Disable default site
sudo a2dissite 000-default.conf

# Enable HEAC CMS site
sudo a2ensite heac-cms.conf

# Test configuration
sudo apache2ctl configtest

# Reload Apache
sudo systemctl reload apache2
```

### 4. Apache Optimization

Edit `/etc/apache2/apache2.conf`:

```apache
# Timeout Settings
Timeout 300
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5

# MPM Prefork Settings (if using mod_php)
<IfModule mpm_prefork_module>
    StartServers 5
    MinSpareServers 5
    MaxSpareServers 10
    MaxRequestWorkers 150
    MaxConnectionsPerChild 3000
</IfModule>

# MPM Event Settings (recommended with PHP-FPM)
<IfModule mpm_event_module>
    StartServers 3
    MinSpareThreads 25
    MaxSpareThreads 75
    ThreadLimit 64
    ThreadsPerChild 25
    MaxRequestWorkers 150
    MaxConnectionsPerChild 3000
</IfModule>
```

## PHP-FPM Configuration

### 1. Install PHP and Extensions

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml \
    php8.2-bcmath php8.2-redis php8.2-intl php8.2-imagick

# Start and enable PHP-FPM
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

### 2. Configure PHP-FPM Pool

```bash
# Copy pool configuration
sudo cp php-fpm.conf /etc/php/8.2/fpm/pool.d/heac-cms.conf

# Remove default pool (optional)
sudo mv /etc/php/8.2/fpm/pool.d/www.conf /etc/php/8.2/fpm/pool.d/www.conf.bak

# Create log directory
sudo mkdir -p /var/log/php-fpm
sudo chown www-data:www-data /var/log/php-fpm

# Test configuration
sudo php-fpm8.2 -t

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 3. PHP Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
; Performance
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
upload_max_filesize = 20M
post_max_size = 20M

; Error Handling (Production)
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php-fpm/error.log

; OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
opcache.enable_cli = 0

; Security
expose_php = Off
allow_url_fopen = On
allow_url_include = Off

; Session
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379?database=0"
session.gc_maxlifetime = 7200
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1

; Date
date.timezone = UTC
```

## SSL Certificate Setup

### Option 1: Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt install -y certbot

# For Nginx
sudo apt install -y python3-certbot-nginx

# For Apache
sudo apt install -y python3-certbot-apache

# Obtain certificate (Nginx)
sudo certbot --nginx -d heac.example.com -d www.heac.example.com

# Obtain certificate (Apache)
sudo certbot --apache -d heac.example.com -d www.heac.example.com

# Test automatic renewal
sudo certbot renew --dry-run

# Set up automatic renewal (already configured by certbot)
sudo systemctl status certbot.timer
```

### Option 2: Custom SSL Certificate

```bash
# Create directory for certificates
sudo mkdir -p /etc/ssl/heac-cms

# Copy your certificate files
sudo cp fullchain.pem /etc/ssl/heac-cms/
sudo cp privkey.pem /etc/ssl/heac-cms/

# Set permissions
sudo chmod 600 /etc/ssl/heac-cms/privkey.pem
sudo chmod 644 /etc/ssl/heac-cms/fullchain.pem

# Update paths in nginx.conf or apache.conf
```

### SSL Best Practices

1. **Use Strong Ciphers**: Only TLS 1.2 and 1.3
2. **Enable HSTS**: Force HTTPS for all requests
3. **OCSP Stapling**: Improve SSL performance
4. **Certificate Monitoring**: Set up expiration alerts

## Security Hardening

### 1. Firewall Configuration

```bash
# Install UFW
sudo apt install -y ufw

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### 2. Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Create local configuration
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit configuration
sudo nano /etc/fail2ban/jail.local
```

Add these jails:

```ini
[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/heac-cms-error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/heac-cms-error.log

[php-url-fopen]
enabled = true
port = http,https
logpath = /var/log/php-fpm/heac-cms-error.log
```

```bash
# Restart Fail2Ban
sudo systemctl restart fail2ban

# Check status
sudo fail2ban-client status
```

### 3. File Permissions

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/heac-cms

# Set directory permissions
sudo find /var/www/heac-cms -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/heac-cms -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/heac-cms/storage
sudo chmod -R 775 /var/www/heac-cms/bootstrap/cache

# Protect sensitive files
sudo chmod 600 /var/www/heac-cms/.env
```

### 4. Disable Directory Listing

Already configured in nginx.conf and apache.conf with:
- Nginx: `autoindex off;`
- Apache: `Options -Indexes`

## Performance Optimization

### 1. Enable HTTP/2

Already enabled in configurations:
- Nginx: `listen 443 ssl http2;`
- Apache: Requires `mod_http2` module

### 2. Enable Brotli Compression (Optional)

```bash
# For Nginx
sudo apt install -y nginx-module-brotli

# Add to nginx.conf
brotli on;
brotli_comp_level 6;
brotli_types text/plain text/css application/json application/javascript text/xml application/xml;
```

### 3. Configure Caching

Already configured in nginx.conf and apache.conf with:
- Static assets: 1 year cache
- Media files: 1 month cache
- HTML: No cache

### 4. Enable FastCGI Cache (Nginx)

Add to nginx.conf:

```nginx
fastcgi_cache_path /var/cache/nginx levels=1:2 keys_zone=HEAC:100m inactive=60m;
fastcgi_cache_key "$scheme$request_method$host$request_uri";
```

## Monitoring and Logging

### 1. Log Rotation

Create `/etc/logrotate.d/heac-cms`:

```
/var/log/nginx/heac-cms-*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        [ -f /var/run/nginx.pid ] && kill -USR1 `cat /var/run/nginx.pid`
    endscript
}

/var/log/php-fpm/heac-cms-*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        /usr/lib/php/php-fpm-socket-helper reload /run/php/php8.2-fpm.sock
    endscript
}
```

### 2. Monitoring Tools

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Monitor Nginx
sudo tail -f /var/log/nginx/heac-cms-access.log

# Monitor PHP-FPM
sudo tail -f /var/log/php-fpm/heac-cms-error.log

# Check PHP-FPM status
curl http://localhost/fpm-status
```

## Troubleshooting

### Common Issues

#### 1. 502 Bad Gateway (Nginx)

```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check socket permissions
ls -la /var/run/php/

# Check error logs
sudo tail -f /var/log/nginx/heac-cms-error.log
```

#### 2. 500 Internal Server Error

```bash
# Check PHP error logs
sudo tail -f /var/log/php-fpm/heac-cms-error.log

# Check Laravel logs
sudo tail -f /var/www/heac-cms/storage/logs/laravel.log

# Check permissions
ls -la /var/www/heac-cms/storage
```

#### 3. Permission Denied

```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/heac-cms

# Fix permissions
sudo chmod -R 775 /var/www/heac-cms/storage
sudo chmod -R 775 /var/www/heac-cms/bootstrap/cache
```

#### 4. SSL Certificate Issues

```bash
# Test SSL configuration
sudo nginx -t
sudo apache2ctl configtest

# Check certificate validity
sudo certbot certificates

# Renew certificate manually
sudo certbot renew
```

### Performance Issues

```bash
# Check server resources
htop
free -h
df -h

# Check PHP-FPM pool status
curl http://localhost/fpm-status

# Check slow queries
sudo tail -f /var/log/php-fpm/heac-cms-slow.log

# Monitor connections
netstat -an | grep :80 | wc -l
```

## Health Checks

### Automated Health Check Script

Create `/usr/local/bin/heac-health-check.sh`:

```bash
#!/bin/bash

# Check Nginx
if ! systemctl is-active --quiet nginx; then
    echo "Nginx is down!"
    sudo systemctl restart nginx
fi

# Check PHP-FPM
if ! systemctl is-active --quiet php8.2-fpm; then
    echo "PHP-FPM is down!"
    sudo systemctl restart php8.2-fpm
fi

# Check application
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://heac.example.com)
if [ "$HTTP_CODE" != "200" ] && [ "$HTTP_CODE" != "302" ]; then
    echo "Application health check failed: HTTP $HTTP_CODE"
fi
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/heac-health-check.sh

# Add to crontab (run every 5 minutes)
sudo crontab -e
*/5 * * * * /usr/local/bin/heac-health-check.sh >> /var/log/heac-health-check.log 2>&1
```

## Additional Resources

- [Nginx Documentation](https://nginx.org/en/docs/)
- [Apache Documentation](https://httpd.apache.org/docs/)
- [PHP-FPM Documentation](https://www.php.net/manual/en/install.fpm.php)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)

## Support

For issues or questions:
- Check logs: `/var/log/nginx/`, `/var/log/php-fpm/`, `/var/www/heac-cms/storage/logs/`
- Review configuration files
- Contact system administrator
