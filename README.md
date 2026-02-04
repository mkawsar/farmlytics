# CowKeeper 🐄

**CowKeeper** is a modern, interactive Cow Management System built with **Laravel + Inertia.js + Vue 3**.  
Track your cows, milk production, feed, breeding, and health records, all in a sleek SPA dashboard. Perfect for dairy and cattle farms looking to manage their herd efficiently.

---

## 🚀 Features

- **Cattle Management:** Add, edit, and track cows with beed, age, gender, color, and health status.
- **Milk Production:** Record daily milk yield and analyze production trends.
- **Feed Management:** Track feed type, amount, and feeding schedules.
- **Breeding & Reproduction:** Manage mating, pregnancy, and calving records.
- **Health & Vaccinations:** Track vet visits, treatments, and vaccination schedules.
- **Reports & Analytics:** Visual dashboards with charts for milk yield, health, and costs.
- **User Roles:** Admin, Manager, and Farm Worker with controlled access.
- **Interactive SPA:** Smooth, fast frontend powered by **Inertia.js + Vue 3**.
- **Optional UUIDs:** Globally unique IDs for distributed farm management (if needed).

---

## 🛠 Tech Stack

- **Backend:** Laravel 12+
- **Frontend:** Inertia.js + Vue 3
- **Database:** MySQL
- **Styling:** Tailwind CSS
- **Charts & Analytics:** ApexCharts / Chart.js
- **Authentication:** Laravel Breeze with Inertia

---

## ⚡ Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/cowkeeper.git
cd cowkeeper
```

2. Install PHP dependencies:
```bash
composer install
```
3. Install Node.js dependencies:
```bash
npm install
npm run dev
```

4. Copy `.env` file and configure database:
```bash
cp .env.example .env
php artisan key:generate
```
5. Run migrations:
```bash
php artisan migrate
```

6. Serve the application:
```bash
php artisan serve
```

## 🏗 Project Structure
```
app/
 ├─ Console/          # Artisan commands
 ├─ Constants/        # Application constants
 ├─ Domain/           # Domain logic and value objects
 ├─ Enums/            # PHP enums
 ├─ Exceptions/       # Exception handlers
 ├─ Helper/           # Helper classes
 ├─ Http/
    ├─ Controllers/  # API controllers
    ├─ Middleware/   # Custom middleware
    ├─ Requests/     # Form request validation
    └─ Resources/    # Resources
 ├─ Models/           # Eloquent models
 ├─ Repositories/     # Repository classes
 └─ Services/         # Service classes
resources/js/
 ├─ Pages/       # Inertia pages
 └─ Components/  # Vue components (Navbar, Tables, Charts)
```

## Debug Mode

For local development, enable debug mode in `.env`:

```bash
APP_DEBUG=true
APP_ENV=local
```

**Note**: Never enable debug mode in production!
