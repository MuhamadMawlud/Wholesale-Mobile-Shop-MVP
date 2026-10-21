# Wholesale Mobile Shop MVP

A B2B wholesale web platform for mobile phones and accessories built with Laravel and MySQL.

## Description

The **Wholesale Mobile Shop MVP** is designed specifically for business-to-business wholesale operations. It enforces strict wholesale pricing and order access controls:
- **Visitors / Guests:** Can browse the product catalog (names, descriptions, categories, stock availability), but wholesale prices are completely shielded on both frontend and backend.
- **Registration:** New business registrations are created with a `pending` status.
- **Approval Gate:** Customers with `pending` or `rejected` status cannot log in. An administrator must review and approve registrations.
- **Approved Customers:** Can log in, view wholesale prices, manage a cart, and place bulk wholesale orders.
- **Administrators:** Manage customer approvals/rejections, product inventory, wholesale pricing, and order fulfillment.

## Technology Stack

- **Backend Framework:** Laravel (PHP 8.5)
- **Templating / UI:** Blade, HTML5, Semantic CSS3, Vanilla JavaScript
- **Database:** MySQL (via XAMPP)
- **Authentication:** Laravel Session-based Auth & Middleware Gates
- **Version Control:** Git & GitHub

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/MuhamadMawlud/Wholesale-Mobile-Shop-MVP.git
   cd Wholesale-Mobile-Shop-MVP
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure database settings in `.env` match your MySQL server:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wholesale_shop
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Create Database & Run Migrations:**
   Ensure MySQL is running (e.g. via XAMPP), create the database, and run migrations:
   ```bash
   php artisan migrate
   ```

5. **Start Local Development Server:**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

## Ticket Status Table

| Ticket # | Title | Status |
| :--- | :--- | :---: |
| Ticket #0 | Laravel project setup, database, and baseline | Not done |
| Ticket #1 | Database schema, migrations, and core models | Not done |
| Ticket #2 | Database seeders (Admin, categories, sample products) | Not done |
| Ticket #3 | Customer registration with pending approval state | Not done |
| Ticket #4 | Authentication and approval status login gate | Not done |
| Ticket #5 | Public catalog browsing with wholesale price shielding | Not done |
| Ticket #6 | Admin customer approval and rejection management | Not done |
| Ticket #7 | Admin product catalog, pricing, and stock CRUD | Not done |
| Ticket #8 | Wholesale shopping cart management | Not done |
| Ticket #9 | Order placement, stock deduction, and customer order history | Not done |
| Ticket #10 | Admin order fulfillment and status workflow | Not done |
