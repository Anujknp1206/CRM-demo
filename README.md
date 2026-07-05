# CRM Management System

A modern, scalable Customer Relationship Management (CRM) platform built with Laravel for managing customers, leads, quotations, orders, payments, inventory, and business operations from a single dashboard.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8%2B-4479A1?style=flat&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📖 Overview

CRM Management System is a complete business solution developed using Laravel 11. It helps organizations streamline customer management, sales, inventory, quotations, payments, and reporting through a secure and user-friendly admin panel.

The project follows clean architecture principles, reusable components, role-based access control, and modular development practices, making it suitable for startups, agencies, and growing businesses.

---

## ✨ Features

### Authentication
- Secure Login
- Password Reset
- Remember Me
- Session Management

### Dashboard
- Business Analytics
- Sales Overview
- Revenue Summary
- Customer / Lead / Order / Payment Statistics
- Quick Navigation
- Recent Activities

### User Management
- Admin & Staff Management
- Customer Management
- User Profile Management
- Status Management

### Role & Permission
- Role Management
- Permission Management
- Role Assignment
- Access Control & Middleware Protection

### Lead Management
- Create / Update / Delete Leads
- Lead Status & Source Tracking
- Follow-up Tracking
- Search & Filter
- Duplicate Prevention

### Customer Management
- Customer Profiles
- Contact & Address Information
- Business Details
- Customer History

### Product Management
- Product Categories & Brands
- Product Variants & Images
- Product Pricing
- Stock Information

### Quotation Management
- Create / Edit Quotations
- Generate & Print PDF
- Tax Calculation
- Discount Management
- Currency Support

### Order Management
- Order Creation & Status Tracking
- Delivery Tracking
- Invoice Generation
- Order History

### Payment Management
- Payment Recording & History
- Pending Payments
- Payment Status
- Transaction Logs

### Inventory Management
- Store & Stock Management
- Low Stock Alerts
- Inventory Reports
- Product Availability

### Reports
- Sales, Customer, Lead, Payment & Inventory Reports

### Activity Logs
- User Activities
- Login Logs
- CRUD Logs
- Audit Trail

### Settings
- Website & Company Information
- Logo Management
- SEO Settings
- Maintenance Mode
- Email Configuration

### Utilities
- AJAX Tables
- Image Upload
- PDF Export
- Responsive Design
- Data Validation & Flash Notifications

---

## 🛠 Tech Stack

| Technology       | Version |
|------------------|---------|
| Laravel          | 11      |
| PHP              | 8.2+    |
| MySQL            | 8+      |
| Bootstrap        | 5       |
| JavaScript       | ES6     |
| jQuery           | Latest  |
| HTML5            | ✓       |
| CSS3             | ✓       |
| Blade Template   | ✓       |
| Font Awesome     | Latest  |

---

## 📂 Project Structure

```
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vendor/
```

---

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Anujknp1206/CRM-demo.git
   ```

2. **Move into project**
   ```bash
   cd crm-management-system
   ```

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure your database** inside `.env`

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **(Optional) Seed the database**
   ```bash
   php artisan db:seed
   ```

9. **Create storage link**
   ```bash
   php artisan storage:link
   ```

10. **Run the application**
    ```bash
    php artisan serve
    ```

11. Visit **http://127.0.0.1:8000**

---

## 🔐 Default Credentials

| Field    | Value              |
|----------|--------------------|
| Email    | test@crmsystem.com |
| Password | 12345678           |

> ⚠️ Change these credentials before deploying to production.

---

## 📊 Modules

- Authentication
- Dashboard
- User Management
- Role & Permission
- Lead Management
- Customer Management
- Product Management & Categories
- Quotations
- Orders
- Payments
- Inventory
- Reports
- Activity Logs
- Settings

---

## 📈 Future Improvements

- REST API
- Mobile Application
- Multi-Tenant Support
- Email Automation
- SMS Integration
- WhatsApp Integration
- Notification System
- Advanced Analytics
- AI Sales Assistant
- Customer Portal

---

## 🤝 Contributing

Contributions are welcome!

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push the branch
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **MIT License**.

---

## 👨‍💻 Author

**Anuj Yadav**
Backend Developer | Laravel Developer

- GitHub: [Anujknp1206](https://github.com/Anujknp1206)
- LinkedIn: [anuj-yadav](https://www.linkedin.com/in/anujyadav1206)
- Demo Link =[Demo](https://crm-demo-production-n5tcyr.laravel.cloud/)

---

## ⭐ Support

If you found this project useful, consider giving it a ⭐ on GitHub!