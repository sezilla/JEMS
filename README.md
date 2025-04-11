# JEMS - Multi-Project Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.22.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3.10-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-3.x-47A5C5?style=for-the-badge&logo=laravel&logoColor=white)](https://filamentphp.com)

JEMS is a comprehensive multi-project based management system built with Laravel 11. It provides an intuitive interface for managing multiple projects, tasks, and teams efficiently.

## 🚀 Features

- **Multi-Project Management**: Handle multiple projects simultaneously
- **Task Management**: Create, assign, and track tasks
- **Role-Based Access Control**: Manage permissions with Shield
- **Trello Integration**: Connect with Trello for enhanced project visualization
- **Real-time Updates**: Using Laravel Reverb for real-time notifications
- **Queue Management**: Background processing for better performance

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- [Composer](https://getcomposer.org/Composer-Setup.exe)
- [Node.js](https://nodejs.org/) (v22.8.0 or higher)
- [XAMPP](https://www.apachefriends.org/) (with PHP 8.3.10) or [Laragon](https://laragon.org/)
- Git

- Setup python API [JEMS-python_ai](https://github.com/sezilla/JEMS-python_ai.git) (v3.10.6 or higher)

## 🔧 Installation

### Step 1: Clone the Repository

```bash
# Clone to htdocs folder if using XAMPP, or www folder if using Laragon
git clone https://github.com/sezilla/jem-v0.09.git
cd jem
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Configure Environment

```bash
# Create environment file
cp .env.example .env
```

Open `.env` and configure your database settings:
get trello environments on [Trello power-ups](https://trello.com/power-ups/admin)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jem
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://127.0.0.1:8000

TRELLO_API_KEY=
TRELLO_API_TOKEN=
TRELLO_WORKSPACE_ID=
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Set Up Storage Link

```bash
php artisan storage:link
```

### Step 6: Database Migration and Seeding

```bash
# Run migrations and seed the database
php artisan migrate
php artisan db:seed

# If you encounter errors, try fresh migration
php artisan migrate:fresh --seed
```

### Step 7: Install and Configure Shield

```bash
# Install Shield (answer 'yes' to all prompts)
php artisan shield:install --fresh
php artisan shield:generate
```

### Step 8: Seed Additional Data

```bash
php artisan db:seed --class=Roles
php artisan db:seed --class=PackageTaskSeeder
php artisan db:seed --class=SkillTask
```

### Step 9: Build Assets

```bash
npm run build
```

### Step 10: Run the Application

Open three separate terminal windows and run:

```bash
# Terminal 1: Start the web server
php artisan serve

# Terminal 2: Start Reverb for real-time features
php artisan reverb:start

# Terminal 3: Start queue worker for background processing
php artisan queue:work --queue=messages,default
```

Visit `http://127.0.0.1:8000` in your browser to access the application.

## 👨‍💻 Default Admin Access

Use these credentials to access the admin dashboard:

- **Email**: admin@email.com
- **Password**: password

## 🔄 Refreshing the Application

If you need to reset and reload the application completely:

Development (with dummy accounts)

```bash
php artisan migrate:fresh --seed
php artisan shield:install --fresh
php artisan shield:generate
php artisan db:seed --class=DevSeeder
```

For Production

```bash
php artisan migrate:fresh --seed
php artisan shield:install --fresh
php artisan shield:generate
php artisan db:seed --class=ProdSeeder
```

## 🧹 Troubleshooting

### If styles are not working properly:

```bash
npm run build
npm run dev
```

### If Trello integration is not working:

```bash
php artisan config:clear
php artisan cache:clear
```

### Clear all cache:

```bash
php artisan optimize:clear
```

## ⚠️ Important Notes

- **STRICTLY NO COMPOSER UPDATE**: This may break dependencies
- Always communicate with the team before making significant code changes

## 🧩 Trello Integration

To set up Trello integration:

1. Visit [Trello Power-Ups Admin](https://trello.com/power-ups/admin)
2. Configure your Power-Up settings
3. Connect your JEM instance with your Trello boards

## 🤝 Contributing

Please communicate with the team before making any changes to avoid breaking the project. Use the issue tracker for bugs and feature requests.

## 📄 License

This project is proprietary software. All rights reserved.

old:

```bash
php artisan migrate:fresh
php artisan db:seed
php artisan shield:install --fresh
php artisan shield:generate
php artisan db:seed --class=Roles
php artisan db:seed --class=PackageTaskSeeder
php artisan db:seed --class=SkillTask
```
