# Changelog

> **Versión en español disponible**: [CHANGELOG.md](CHANGELOG.md)

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.1] - 2025-11-26

### Added
- 🔍 **Select2 globally enabled**
  - Smart search in work order forms
  - Improved interface for service selection
  - Spanish translation of messages
  - Auto-initialization on dynamic services

- ⚡ **Smart phone number autocomplete**
  - Real-time AJAX search when typing license plate (500ms debounce)
  - Works in both create and edit work orders
  - Visual indicators: new vs existing customer
  - Reduces data entry errors

- 🛡️ **Brute force attack protection (Rate Limiting)**
  - **Login**: Maximum 5 attempts per minute
  - **Registration**: Maximum 3 registrations every 10 minutes
  - **Password recovery**: Maximum 3 attempts every 10 minutes
  - **Authenticated routes**: 120 requests per minute
  - **AJAX searches**: 30 requests per minute
  - Custom 429 error page with countdown timer
  - Automatic temporary blocking of suspicious IPs

### Improved
- 🛡️ **Delete validation with referential integrity**
  - **Customers**: Prevents deletion if has associated work orders
  - **Employees**: Checks work orders and payments before deletion
  - **Services**: Protects services with usage history
  - **Inventory**: Blocks items used in work orders
  - Descriptive messages with specific counters
  - Visual "Protected" button when deletion is not allowed

- 🎨 **Visual improvements**
  - Improved preloader without circular border
  - Logo animation changed from wobble to pulse
  - Select2 with consistent height in Bootstrap 4

- 📊 **Performance optimizations**
  - Services alphabetically sorted in selects
  - Work order count preloaded in services view
  - `trabajoServicios` relationship added to Service model

### Fixed
- 🐛 Generic error messages replaced with specific ones
- 🐛 Improved cascade deletion validation
- 🐛 Select2 not initializing on dynamically added services

---

## [1.0.0] - 2025-11-25

### Added
- ✨ **Custom authentication system**
  - Modern login with responsive design and animations
  - Gradients and glassmorphism effects
  - Chankas Car corporate color palette
  - Loading spinner on login

- 🔐 **Role and permission system**
  - CheckRole middleware implemented
  - 4 roles: Admin, Cashier, Technician, Read-Only
  - Role-protected routes
  - Granular view permissions

- 👥 **Employee and position management**
  - Complete CRUD for employees
  - Position management
  - User-employee relationship
  - Unique ID validation

- 🔧 **Work order management**
  - Work registration with multiple services
  - Automatic customer search by license plate
  - Automatic commission calculation
  - Work, reception, and recertification dates
  - Inventory parts usage in work orders
  - Observations per work order
  - Recurring customer indicator (visits)

- 👨‍🔧 **Special view for technicians**
  - "/my-work-orders" route exclusive for technicians
  - View of work orders assigned to authenticated technician
  - Earnings summary per work order
  - Automatically calculated totals

- 💰 **Technician payment system**
  - Pending balance calculation
  - Partial and full payment registration
  - Collapsible payment history with DataTables
  - Current month statistics
  - Total payments made
  - PDF export

- 🧾 **PDF generation**
  - Professional work/sale detail
  - Corporate color design
  - Complete information: customer, services, parts, totals
  - Signature section
  - Direct download from view

- 📦 **Inventory management**
  - Complete CRUD for items
  - Real-time stock control
  - Visual low stock alerts
  - Stock types: Countable and Query
  - Configurable units of measure
  - Supplier relationship
  - Purchase and sale prices

- 🏢 **Supplier management**
  - Complete CRUD
  - Contact information
  - Associated item counter
  - Custom route parameter support

- 💼 **Customer management**
  - Search by license plate
  - Work history per customer
  - Contact phone
  - Automatic update when creating work order

- 📊 **Advanced audit system**
  - Dashboard with 4 main statistics
  - Donut chart with Chart.js
  - Top 5 most active users
  - Collapsible advanced filters
  - Text search in descriptions
  - Improved table view with color badges
  - Complete detail view with history
  - Before/after changes table
  - IP and user agent logging
  - Excel export ready

- 🎨 **Custom theme**
  - adminlte-theme.css file with corporate palette
  - Colors: #1a3a47, #6db3c8, #fbc02d
  - Custom sidebar
  - Cards with colored borders
  - Styled buttons
  - Tables with hover effects

- 🔔 **Notification system**
  - SweetAlert2 v11 integrated
  - Reusable helpers in sweetalert-helpers.js
  - Custom deletion confirmations
  - Automatic alerts for flash messages
  - Functions: confirmarEliminacion(), mostrarExito(), mostrarError(), mostrarInfo()

- 📱 **Responsive design**
  - Base layout with favicon on all pages
  - Mobile and tablet adaptable
  - Responsive DataTables
  - Collapsible menu

- 💾 **Backup system**
  - Spatie Laravel Backup v8.8 configured
  - Database backups with Gzip compression
  - Complete file backups
  - Optimized exclusions
  - Available artisan commands
  - Complete guide in BACKUP_GUIDE.md

- 📚 **Documentation**
  - Complete and professional README.md
  - BACKUP_GUIDE.md with detailed instructions
  - CHANGELOG.md for change history
  - Code comments

### Changed
- 🔄 **Main views migrated to layouts.base**
  - Customers, Work Orders, Employees, Services
  - Inventory, Suppliers
  - Activity Logs
  - Removed manual Bootstrap alerts
  - Using @push('scripts') instead of @section('js')

- 🔧 **Routes reorganized**
  - Grouped by permission level
  - Admin: full access
  - Admin + Cashier: operational management
  - Technician: only their work orders

- 🎯 **Improved delete buttons**
  - Replaced native confirm() with SweetAlert
  - Custom messages per module
  - Unique IDs in forms

### Fixed
- 🐛 **Proveedor pluralization bug**
  - Added getRouteKeyName() returning 'id_proveedor'
  - Custom parameters in route resource
  - Laravel no longer generates "proveedore"

- 🐛 **Incomplete JavaScript in trabajo/create**
  - Added event handler for btn-agregar-servicio
  - calcularTotales() function implemented
  - Functional service select

- 🐛 **Missing imports in TrabajoController**
  - TrabajoInventario imported
  - Inventario imported
  - Barryvdh\DomPDF\Facade\Pdf imported

- 🐛 **Favicon not visible**
  - Renamed favicon.ico to favicon.png (correct format)
  - Base layout with multiple links
  - Cache busting with version

### Security
- 🔒 Role middleware implemented on all sensitive routes
- 🔒 Active user verification in CheckRole
- 🔒 CSRF tokens in all forms
- 🔒 Input validation in controllers
- 🔒 Activity logs for auditing

## Upcoming Versions

### [1.1.0] - Planned
- [ ] Real-time stock validation
- [ ] Dashboard with statistics and charts
- [ ] Quick global search
- [ ] Recertification reminders
- [ ] Excel log export
- [ ] Complete vehicle history
- [ ] Quote system

### [1.2.0] - Planned
- [ ] Advanced financial reports
- [ ] WhatsApp Business integration
- [ ] Multi-currency (Bs/USD)
- [ ] Frequent query caching
- [ ] Complete unit tests

---

**Developed by**: Pedro Antonio López Chumacero - Chanka's Development Team - Sucre, Bolivia  
**Note**: Versions prior to 1.0.0 are not documented as this is the first official release.
