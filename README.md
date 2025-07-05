# Julies Bakeshop POS System – SAAD Extension

An enterprise-grade, modular Point-of-Sale extension built for Julies Bakeshop as part of our System Analysis & Design (SAAD) deliverables. This PHP & JavaScript codebase enhances the original IM 2 POS with secure authentication, dynamic order management, real-time reporting, and a scalable architecture ready for production.

---

## 🛠️ Key Features

### 1. Secure Multi-Role Authentication  
- **Login & Session Management** with PHP sessions  
- **Role-Based Access Control** (Admin, Cashier, Manager)  
- **Password Hashing** (bcrypt) and account lockout policies  

### 2. Dynamic Order & Inventory Management  
- **Real-Time DataTables** for order entry, inventory lookups, and sales history  
- **AJAX-Driven CRUD** operations in `Actions.php` for lightning-fast responsiveness  
- **Category & Product Management** with nested menu support  

### 3. Comprehensive Reporting Dashboard  
- **Sales Summaries** by date range, category, and cashier  
- **Low-Stock Alerts** and automated reorder notifications  
- **Printable Reports** (PDF export via JavaScript libraries)  

### 4. Maintenance & Uptime Control  
- **Graceful Maintenance Mode** (`maintenance.php`) with custom “Temporarily Closed” messaging  
- **Automated Database Backups** on scheduled intervals  

### 5. Scalable, Modular Codebase  
/
├── .vscode/ # IDE settings
├── css/ # SCSS/CSS styles (modular SCSS workflow)
├── js/ # Custom JS, plugins, DataTables, select2
├── bsms/images/ # Julies Bakeshop assets
├── Font-Awesome-master/ # Icon library
├── select2/ # Enhanced dropdown components
├── database/ # MySQL schema scripts & seed data
├── Actions.php # Centralized AJAX handlers
├── DBConnection.php # PDO-based database abstraction
├── login.php # Authentication flow
├── home.php # Main dashboard
├── manage_account.php # Admin user management
├── manage_category.php # Product category CRUD
├── manage_product.php # Product catalog CRUD
├── manage_shifts.php # Cashier shift scheduling
└── maintenance.php # Downtime handler
