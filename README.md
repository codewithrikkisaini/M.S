<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Livewire-4.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white"/>
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge"/>
</p>

<h1 align="center">🏨 Merahkie PMS Lite</h1>
<p align="center"><b>A modern, full-featured Hotel Property Management System built with Laravel 13 + Livewire 4</b></p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-tech-stack">Tech Stack</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-demo-credentials">Demo</a> •
  <a href="#-screenshots">Screenshots</a>
</p>

---

## ✨ Features

### 🏠 Room Management
- Add, edit, and manage rooms with room types and pricing
- Real-time room availability status (Available / Occupied / Maintenance / Housekeeping)
- Room status updates linked to reservations, housekeeping, and maintenance tickets

### 📅 Reservations
- Create and manage guest reservations with full room + guest details
- Booking calendar view for visual availability overview
- Reservation status tracking (Pending → Confirmed → Checked In → Checked Out)
- Housekeeping & maintenance alerts shown on reservation cards

### 👥 Guest Management
- Complete guest profiles with contact details
- Guest history and stay records
- Search & filter functionality

### ✅ Check-In / Check-Out
- Streamlined check-in flow for confirmed reservations
- Auto-generated checkout summary with nights stayed, subtotal, tax, and grand total
- Invoice auto-generated on checkout

### 🧾 Invoices
- Automatic invoice generation on guest checkout
- PDF download with hotel branding pulled from settings
- View invoices in-browser or download as PDF

### 🧹 Housekeeping
- Ticket-based housekeeping management per room
- Status tracking (Pending → In Progress → Done)
- Visible on Rooms page and Reservation cards

### 🔧 Maintenance
- Maintenance ticket system per room
- Priority levels (Low / Medium / High / Critical)
- Visible on Rooms page and Reservation cards

### 📊 Reports
- Daily revenue reports
- Occupancy statistics
- Checkout & invoice summaries

### ⚙️ Settings (Dynamic)
- Hotel name, address, phone, email, website — all saved to database
- System preferences: currency, date format, check-in/check-out times, timezone
- Invoice settings: prefix and footer text
- Notification toggles: email & SMS
- All settings reflected **globally** across the entire application (sidebar, page titles, invoice PDFs, etc.)

### 👤 User Management & Roles
- Admin and Receptionist roles
- Role-based access control (admin-only pages hidden from staff)
- User status management (Active / Inactive)

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend Framework | Laravel 13 |
| PHP Version | PHP 8.4 |
| Frontend Reactivity | Livewire 4 |
| Styling | Tailwind CSS 4 |
| UI Interactions | Alpine.js |
| PDF Generation | barryvdh/laravel-dompdf |
| Icons | Font Awesome 6 |
| Fonts | Google Fonts (Inter) |
| Database | MySQL |
| Alerts/Toasts | SweetAlert2 |
| Calendar | FullCalendar 5 |

---

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/codewithrikkisaini/Merahkie-PMS-Lite.git
cd Merahkie-PMS-Lite

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=merahkie_pms
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations + seeders
php artisan migrate --seed

# 8. Build frontend assets
npm run build

# 9. Start the development server
php artisan serve
```

> App will be live at: **http://127.0.0.1:8000**

---

## 🔐 Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| 🛡️ Admin | `admin@merahkie.com` | `123456` |
| 🧑‍💼 Receptionist | `receptionist@merahkie.com` | `123456` |

> You can also use the **Quick Login buttons** on the login page for one-click access.

---

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/       # Invoice, Auth controllers
│   ├── Models/                 # Eloquent models (Guest, Room, Reservation, etc.)
│   ├── Providers/              # AppServiceProvider (global hotel settings)
│   └── Services/               # Business logic (ReservationService)
├── database/
│   ├── migrations/             # All table migrations
│   └── seeders/                # Demo data seeders
├── resources/
│   ├── css/app.css             # Tailwind + custom styles
│   ├── js/app.js               # Alpine.js, Livewire, FullCalendar
│   └── views/
│       ├── layouts/            # app.blade.php, sidebar, navbar
│       ├── auth/               # Login page
│       ├── components/         # All Livewire components (inline class syntax)
│       └── invoices/           # Invoice PDF template
└── routes/web.php              # All application routes
```

---

## ⚙️ How Dynamic Settings Work

Settings are stored in a `settings` table as key-value pairs. The `AppServiceProvider` injects hotel info (`$hotelName`, `$hotelEmail`, etc.) into **every view** using `View::composer('*', ...)`.

To update hotel info across the entire system:
1. Go to **Settings → Hotel Info**
2. Update the hotel name (or any other field)
3. Click **Save Hotel Info**
4. Instantly reflected in: sidebar logo, browser title, footer, login page, and invoice PDFs ✅

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

<p align="center">Made with ❤️ by <a href="https://github.com/codewithrikkisaini">@codewithrikkisaini</a></p>
# M.S
