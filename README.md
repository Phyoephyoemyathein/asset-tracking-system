# 📦 Asset & Warehouse Tracking System

A robust, feature-rich inventory and warehouse management web application built with **PHP Laravel**, **Tailwind CSS**, and **Alpine.js**. Designed to streamline asset tracking, QR code generation, live barcode scanning, inter-warehouse transfers, and comprehensive reporting.

---

## ✨ Key Features

- **📊 Interactive Dashboard & Analytics:** Real-time metrics tracking total assets, available quantities, categories, and warehouses with integrated Chart.js visualizations.
- **🏷️ Automated QR Code Generation:** Instant QR code generation for every registered asset with a click-to-view modal preview and full-dataset print capabilities.
- **📸 Live Camera QR & Barcode Scanner:** Built-in mobile-responsive live camera scanner using `html5-qrcode` for fast asset lookup and verification.
- **📦 Stock In / Stock Out & Transfers:** Efficient tracking of stock adjustments, item check-ins/check-outs, and inter-warehouse asset transfers.
- **📋 Comprehensive Activity Logs:** Full audit trail logging user actions, system modifications, asset registrations, and status updates for accountability.
- **📜 Transaction History:** Detailed logs of asset movements and quantity changes over time.
- **🔍 Advanced Filtering & Search:** Filter inventory by categories, warehouses, date ranges, and custom keyword searches (Name/SKU).
- **📄 Professional Reporting & Export:** Generate structured PDF stock summaries, monthly/yearly movement reports, and Excel exports.
- **⚠️ Low Stock Alerts:** Automated warnings highlighting assets running at or below minimum stock levels.
- **👥 Role-Based Access Control (RBAC):** Distinct permissions for `Admin` and `Staff` roles.
- **🌙 Dark Mode Support:** Seamless toggle between light and dark themes using Tailwind CSS and Alpine.js.

---

## 🛠️ Tech Stack

- **Backend:** PHP 8+, Laravel Framework, Eloquent ORM
- **Frontend:** Tailwind CSS, Alpine.js, Blade Templates, Vite
- **Data Visualization & Scanning:** Chart.js, HTML5-QRCode
- **Database & Version Control:** MySQL, Git & GitHub

---

## ⚙️ Installation & Setup

To run this project on your local machine, follow these steps:

```bash
git clone [https://github.com/Phyoephyoemyathein/asset-tracking-system.git](https://github.com/Phyoephyoemyathein/asset-tracking-system.git)
cd asset-tracking-system
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
