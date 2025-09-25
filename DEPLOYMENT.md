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

In Coolify, go to your project's Environment Variables section and add:

```env
APP_NAME=Wattaway
APP_ENV=production
APP_KEY=your-generated-app-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wattaway
DB_USERNAME=wattaway_user
DB_PASSWORD=your-secure-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 4: Generate Application Key

Run this command in Coolify's terminal:

```bash
php artisan key:generate
```

**Note**: If you need to get the key value for environment variables, use:
```bash
php artisan key:generate --show
```

### Step 5: Run Database Migrations

```bash
php artisan migrate --force
```

### Step 6: Set Storage Permissions

```bash
./setup-permissions.sh
```

### Step 7: Build Assets

```bash
npm run build
```

## Local Development with Docker

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

5. **Migration Errors**:
   - Run `php artisan migrate:status` to check migration status
   - Use `php artisan migrate:rollback` if needed
   - Check database user permissions

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
