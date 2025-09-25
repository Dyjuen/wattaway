# Laravel 12 Deployment Guide for Coolify

This guide will help you deploy your Laravel 12 application to an Ubuntu server using Coolify as the deployment panel with MySQL as the database.

## Prerequisites

- Ubuntu server with Coolify installed
- Domain name pointing to your server
- Git repository with your Laravel application
- **Node.js 22.12.0 or higher** (required for Vite 7.x compatibility)
- PHP 8.3 or higher

## Project Configuration

### 1. Database Configuration

Your project has been configured to use MySQL instead of SQLite. The default database connection has been updated in `config/database.php`.

### 2. Environment Configuration

- `.env.example` - Contains the default environment variables
- `.env.production` - Contains production-specific environment variables

### 3. Deployment Files

The following deployment files have been created:

- `coolify.json` - Coolify deployment configuration
- `Dockerfile` - Docker container configuration
- `docker-compose.yml` - Local development environment
- `nginx.conf` - Nginx configuration for production
- `setup-permissions.sh` - Script to set proper file permissions

## Deployment Steps

### Step 1: Prepare Your Repository

1. Commit all changes to your Git repository:
   ```bash
   git add .
   git commit -m "Prepare for deployment with Coolify and MySQL"
   git push origin main
   ```

### Step 2: Set Up Coolify

1. Log into your Coolify dashboard
2. Click "New Project"
3. Select "From Git Repository"
4. Enter your repository URL
5. Choose the branch (main)
6. **Important**: Ensure Node.js version is set to 22 in the build settings
7. Click "Deploy"

### Step 3: Configure Environment Variables

**Important**: Environment variables are separated into build-time and runtime-only for optimal deployment.

#### Why This Separation?
- **Build-time variables**: Available during the build process, affect dependency installation and compilation
- **Runtime-only variables**: Only available when the application is running, prevents build-time issues with production settings

In Coolify, go to your project's Environment Variables section and add:

### Build-time Environment Variables
```env
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=wattaway
DB_USERNAME=wattaway_user
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
NODE_VERSION=22
```

### Runtime-only Environment Variables
Set these as "Runtime only" in Coolify:
```env
APP_ENV=production
APP_DEBUG=false
```

### Step 4: Generate Application Key

**Note**: The application key and other setup commands are now run as post-deployment commands automatically.

### Step 5: Verify Deployment

After deployment completes, check that your application is running:

1. **Check application health**: `curl https://your-domain.com/health`
2. **Check database connectivity**: The migrations should have run automatically
3. **Verify file permissions**: The setup script should have run automatically

## Manual Setup (If Post-deployment Commands Fail)

If the automatic post-deployment commands fail, follow these manual steps in Coolify's terminal:

### Step 1: Generate Application Key
```bash
php artisan key:generate
```

### Step 2: Run Database Migrations
```bash
php artisan migrate --force
```

### Step 3: Set File Permissions
```bash
./setup-permissions.sh
```

### Step 4: Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Verify Everything Works
```bash
# Check application health
curl https://your-domain.com/health

# Check if migrations ran successfully
php artisan migrate:status
```

To test your application locally with MySQL before deployment:

1. Copy `.env.example` to `.env` and update the database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_DATABASE=wattaway
   DB_USERNAME=wattaway_user
   DB_PASSWORD=wattaway_password
   ```

2. Start the Docker environment:
   ```bash
   docker-compose up -d
   ```

3. Run migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```

4. Access your application at `http://localhost:8000`
5. Access phpMyAdmin at `http://localhost:8080`

## Production Optimizations

Your application includes several production optimizations:

- **Vite Build Optimizations**: Minified CSS/JS with chunk splitting
- **Caching**: Config, route, and view caching enabled
- **Security Headers**: Added security headers in nginx.conf
- **Gzip Compression**: Enabled for better performance
- **Static Asset Caching**: Long-term caching for static files

## Monitoring and Maintenance

### Health Check

Your application includes a health check endpoint at `/health`.

### Logs

- Application logs: `/storage/logs/laravel.log`
- Web server logs: Check Coolify dashboard

### Database Backups

Set up regular database backups in Coolify:

1. Go to your project settings
2. Navigate to "Backups"
3. Configure automatic backups for your MySQL service

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Verify database credentials in environment variables
   - Ensure MySQL service is running
   - Check database host and port settings

2. **Permission Issues**:
   - Run `./setup-permissions.sh` to fix file permissions
   - Ensure storage directories are writable

3. **Asset Loading Issues**:
   - Run `npm run build` to rebuild assets
   - Clear browser cache
   - Check for 404 errors on static assets

4. **Node.js Version Error**:
   - Ensure your deployment platform uses Node.js 22.12.0 or higher
   - The project requires Node.js >=22.12.0 for Vite 7.x compatibility
   - Check your deployment platform's Node.js version settings
   - If using Coolify, verify the Node.js version in project settings

5. **Post-deployment Command Failed**:
   - **Issue**: Container runtime error during post-deployment commands
   - **Symptoms**: Deployment succeeds but migrations/permissions fail to run
   - **Solutions**:
     - Run commands manually in Coolify terminal (see manual steps below)
     - Check container logs: `docker logs <container-name>`
     - Ensure all services (MySQL, Redis) are healthy before deployment
     - Try redeploying after fixing any service issues

### Debug Mode

To enable debug mode temporarily for troubleshooting:

```bash
php artisan config:clear
# Update APP_DEBUG=true in environment variables
php artisan config:cache
```

## Security Considerations

1. **Environment Variables**: Never commit `.env` files to version control
2. **Database Passwords**: Use strong, unique passwords
3. **File Permissions**: Keep sensitive files secure
4. **HTTPS**: Enable SSL/TLS in Coolify
5. **Firewall**: Configure firewall rules appropriately

## Support

For additional support:

- Laravel Documentation: https://laravel.com/docs
- Coolify Documentation: https://coolify.io/docs
- Laravel Community: https://laravel.com/community

---

**Note**: Remember to replace `your-domain.com` with your actual domain name and update all placeholder values with your specific configuration.
