# 🚗 Chankas Car - CNG Workshop Management System

> **Versión en español disponible**: [README.md](README.md)

<p align="center">
  <img src="public/favicon.png" alt="Chankas Car Logo" width="150">
</p>

<p align="center">
  Comprehensive management system for Compressed Natural Gas (CNG) conversion workshops
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1.33-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0.44-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/AdminLTE-3.15-0073e6?style=for-the-badge" alt="AdminLTE">
</p>

---

## 📋 Description

Chankas Car is a complete web-based system designed to efficiently manage operations of a CNG vehicle conversion workshop. It handles everything from vehicle reception to technician commission payments, including inventory, suppliers, and report generation.

## ✨ Main Features

### 🔐 Authentication & Authorization
- **Modern responsive login** with custom design
- **Role-based system**: Admin, Cashier, Technician, Read-Only
- **Authorization middleware** with granular permissions
- **User management** with active/inactive states

### 👥 Staff Management
- Employee registration with positions
- User-to-employee assignment
- Technician and administrative staff control
- Work history per technician

### 🔧 Work Order Management
- Work registration with multiple services
- Automatic customer search by license plate
- Automatic commission calculation for technicians
- Work, reception, and recertification dates
- Special view for technicians: "My Work Orders"
- Recurring customer indicator

### 💰 Payment Management
- Balance calculation per technician
- Partial and full payment registration
- **Collapsible payment history** with DataTables
- Current month statistics
- PDF report export

### 📦 Inventory
- Real-time stock control
- Low stock visual alerts
- Stock types: Countable and Query
- Supplier relationship
- Parts usage per work order

### 🏢 Suppliers
- Supplier management
- Associated items per supplier
- Complete contact information

### 📊 Reports & PDFs
- **Work/sale detail** in PDF with custom branding
- Professional design with corporate colors
- Complete information: services, parts, totals

### 📈 Advanced Audit System
- **Statistics dashboard** with charts
- Complete system action logging
- **Advanced filters**: user, action, module, dates, text search
- **Top most active users**
- Detailed log view with recorded changes
- Donut chart with action distribution
- Excel export ready

### 🎨 User Interface
- **Custom theme** with corporate color palette
- **SweetAlert2** for confirmations and notifications
- **DataTables** with Spanish translation
- **Responsive design** for mobile and tablets
- **Custom favicon** on all pages
- Automatic flash alerts (success/error/info)

### 🔄 Backup System
- Configured with **Spatie Laravel Backup**
- Database backups with Gzip compression
- Complete file backups
- Available artisan commands
- Complete guide in `BACKUP_GUIDE.md`

## 🎨 Color Palette

```css
--primary-dark: #1a3a47;   /* Main dark blue */
--primary-blue: #1e5a7a;   /* Medium blue */
--primary-light: #6db3c8;  /* Light blue/cyan */
--accent-yellow: #fbc02d;  /* Accent yellow */
--dark-gray: #3d3d3d;      /* Dark gray */
```

## 🛠️ Technologies Used

### Backend
- **Laravel 10.49.1** - PHP Framework
- **PHP 8.1.33** - Programming Language
- **MySQL 8.0.44** - Database
- **Laravel Breeze** - Authentication
- **Spatie Laravel Backup 8.8** - Backup System

### Frontend
- **AdminLTE 3.x** - Admin Template
- **Tailwind CSS** - Utility-first CSS Framework
- **Vite** - Build tool and asset bundling
- **jQuery** - DOM Manipulation
- **DataTables 1.11.5** - Interactive Tables
- **SweetAlert2 v11** - Modern Alerts
- **Chart.js v3** - Charts and Statistics
- **Font Awesome 6** - Icons

### PDFs
- **DomPDF (barryvdh/laravel-dompdf v3.1)** - PDF Generation

## 📁 Project Structure

```
chankascar/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controllers (14 modules)
│   │   └── Middleware/       # CheckRole middleware
│   ├── Models/               # Eloquent Models (14 models)
│   └── Traits/               # LogsActivity trait
├── database/
│   ├── migrations/           # 23 migrations
│   └── seeders/
├── resources/
│   ├── css/
│   │   ├── adminlte-theme.css   # Custom theme
│   │   ├── app.css
│   │   └── palette.css
│   ├── js/
│   └── views/
│       ├── layouts/
│       │   └── base.blade.php   # Base layout with favicon and SweetAlert
│       ├── auth/
│       │   └── login.blade.php  # Custom login
│       ├── trabajos/
│       ├── clientes/
│       ├── empleados/
│       ├── pagos/
│       ├── activity-logs/       # Audit system
│       └── ...
├── public/
│   ├── favicon.png              # System favicon
│   └── js/
│       └── sweetalert-helpers.js  # SweetAlert helpers
├── config/
│   ├── adminlte.php
│   └── backup.php               # Backup configuration
├── routes/
│   └── web.php                  # Routes with role middleware
├── BACKUP_GUIDE.md              # Backup guide
└── README.md
```

## 🚀 Installation

### Prerequisites
- PHP >= 8.1.33
- Composer >= 2.0
- Node.js >= 16.x and NPM
- MySQL >= 8.0.44
- Web server (Apache/Nginx)
- Operating System: Linux (Tested on Xubuntu 22.04 LTS)
- PHP Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, GD

### Installation Steps

1. **Clone the repository**
```bash
git clone https://github.com/Chanka-Dev/chankas-car.git
cd chankas-car
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Configure .env file**
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chankascar_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Create the database**
```bash
mysql -u root -p
CREATE DATABASE chankascar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Run seeders (optional)**
```bash
php artisan db:seed
```

8. **Compile assets**
```bash
npm run build
# or for development:
npm run dev
```

9. **Set permissions**
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

10. **Start the server**
```bash
php artisan serve
```

Access at: `http://localhost:8000`

## 👤 Default Users

If you ran the seeders, you can use:

- **Admin**: admin@chankascar.com / password
- **Cashier**: cajero@chankascar.com / password
- **Technician**: tecnico@chankascar.com / password

## 📝 Usage

### Roles & Permissions

#### Administrator
- Full system access
- User and employee management
- Service, position, and inventory configuration
- Activity log access
- Supplier and expense management

#### Cashier
- Customer and work order management
- Technician payment registration and calculation
- PDF generation
- Inventory consultation

#### Technician
- "My Work Orders" view with assigned jobs
- Earned commission consultation
- Work order PDF downloads

### Useful Artisan Commands

```bash
# Create database backup
php artisan backup:run --only-db

# Create full backup
php artisan backup:run

# List backups
php artisan backup:list

# Clean old backups
php artisan backup:clean

# Check backup status
php artisan backup:monitor
```

## 🔧 Development

### Compile assets in development mode
```bash
npm run dev
```

### Compile assets for production
```bash
npm run build
```

### Run tests
```bash
php artisan test
```

## 📦 Backups

The system includes a complete backup system. See detailed guide in [BACKUP_GUIDE.md](BACKUP_GUIDE.md)

**Main commands:**
- `php artisan backup:run --only-db` - Database only backup
- `php artisan backup:run` - Full backup
- Location: `storage/app/Chankas Car/`

## 🤝 Contributing

Contributions are welcome. Please:

1. Fork the project
2. Create a feature branch (`git checkout -b feature/NewFeature`)
3. Commit your changes (`git commit -m 'Add new feature'`)
4. Push to the branch (`git push origin feature/NewFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Pedro Antonio López Chumacero**  
*Chanka's Development Team*

## 🙏 Acknowledgments

- Laravel Framework
- AdminLTE Template
- Laravel Developer Community
- All project contributors

---

<p align="center">
  Developed with 💪 by <strong>Pedro Antonio López Chumacero</strong><br>
  Chanka's Development Team - Sucre, Bolivia
</p>
