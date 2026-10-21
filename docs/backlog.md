# Project Backlog: Wholesale Mobile Shop MVP

This document outlines the complete development backlog for the **Wholesale Mobile Shop MVP**, a B2B wholesale web platform built with Laravel, Blade, semantic CSS/JavaScript, and MySQL.

---

## Ticket Status Overview

| Ticket ID | Title | Priority | Status | Dependencies |
| :--- | :--- | :---: | :---: | :---: |
| **T0** | Laravel Project Setup, Database, and Baseline | High | **Done** | None |
| **T1** | Public Catalog Without Prices | High | **Not done** | T0 |
| **T2** | Customer Registration with Status "Pending" | High | **Not done** | T1 |
| **T3** | Login Restricted to Approved Customers | High | **Not done** | T2 |
| **T4** | Admin Approves or Rejects Customers | High | **Not done** | T3 |
| **T5** | Prices Visible Only to Approved Customers (Server-Side) | High | **Not done** | T4 |
| **T6** | Shopping Cart | High | **Not done** | T5 |
| **T7** | Order Placement with Server-Side Validation of Stock and Prices | High | **Not done** | T6 |
| **T8** | Admin Product, Price, and Stock Management | Medium | **Not done** | T4, T1 |
| **T9** | Admin Order View and Customer Order History | Medium | **Not done** | T7, T4 |
| **T10** | Final Testing, README Update, and Presentation Notes | Medium | **Not done** | T1–T9 |

---

## Ticket Details

---

### Ticket T0: Laravel Project Setup, Database, and Baseline

* **Priority**: High
* **Status**: **Done**
* **Dependencies**: None
* **Context**: Establish a clean working baseline with Laravel 13 on PHP 8.5, connect to the local XAMPP MySQL database (`wholesale_shop`), run initial framework migrations, configure environment files, and push the initial commit.
* **Task**:
  1. Inspect environment, verify PHP version compatibility.
  2. Install Laravel framework skeleton without overwriting `.git`.
  3. Create `wholesale_shop` database using XAMPP MySQL client.
  4. Configure `.env` with MySQL credentials.
  5. Run `php artisan migrate` to verify database connection.
  6. Confirm `.env` and `vendor/` are ignored in `.gitignore`.
  7. Commit initial project baseline and push to GitHub `main` branch.
* **Acceptance Criteria**:
  * **AC1**: `php artisan --version` reports Laravel version.
  * **AC2**: Database `wholesale_shop` exists and migrations succeed.
  * **AC3**: `.env` is ignored and `git status` is clean.
  * **AC4**: `README.md` exists with required sections.
  * **AC5**: `php artisan serve` responds with `HTTP 200 OK`.
* **Constraints**: No starter kits (Breeze/Jetstream) or extra packages.
* **Output & Verification**: Verified through terminal commands and HTTP requests. Status: Completed.

---

### Ticket T1: Public Catalog Without Prices

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T0
* **Context**: Under Business Rule 1, visitors can browse the mobile phone and accessories catalog (name, image, description, category, and stock availability badge) without seeing wholesale prices. Price shielding starts at the public catalog layer. Test environment isolation must also be confirmed.
* **Task**:
  1. Inspect and report database settings in `phpunit.xml` to verify tests run on SQLite in-memory (`:memory:`) and do not run against or alter the development database `wholesale_shop`.
  2. Create `categories` table migration (`id`, `name`, `slug`, `timestamps`) and `Category` model.
  3. Create `products` table migration (`id`, `category_id`, `name`, `slug`, `description`, `image`, `price`, `stock`, `timestamps`) and `Product` model.
  4. Create `CategorySeeder` and `ProductSeeder` populated with realistic wholesale mobile phones (e.g. flagship smartphones, budget phones) and accessories (chargers, cases, screen protectors).
  5. Build a responsive base layout (`layouts/app.blade.php`) with navigation and clean, restrained styling.
  6. Create `CatalogController` to handle catalog listing (`index`) with category filtering, and product detail view (`show`). For guest requests, explicitly do NOT select the `price` column from the database query (select only `id`, `category_id`, `name`, `slug`, `description`, `image`, `stock`), ensuring price data never reaches the view layer.
  7. Create Blade views `catalog/index.blade.php` and `catalog/show.blade.php`, rendering product cards with name, category, image placeholder, description, and stock status. Wholesale price must be strictly omitted.
  8. Add an automated Feature Test (`CatalogTest`) asserting that the page returns `200 OK`, shows product details and stock, and that seeded wholesale price values or currency symbols do not appear anywhere in the response HTML.
* **Acceptance Criteria**:
  * **AC1**: `php artisan migrate --seed` creates and seeds `categories` and `products` tables.
  * **AC2**: Visiting `/` renders the catalog with product name, category badge, short description, and stock availability indicator (e.g. "In Stock (45 available)" or "Out of Stock").
  * **AC3**: Filtering by category (via query parameter or UI pills) filters product cards accurately.
  * **AC4**: Visiting `/products/{slug}` displays the full product detail page.
  * **AC5**: The catalog controller query for guest requests does not select the `price` column from the database, guaranteeing price data never reaches the Blade view.
  * **AC6**: Automated test confirms that actual seeded price values from `ProductSeeder` and currency symbols (`$`, `€`, `USD`) do not appear anywhere in the rendered HTML output.
  * **AC7**: Database configuration in `phpunit.xml` uses in-memory SQLite and test suite execution leaves the development database `wholesale_shop` completely untouched.
* **Constraints**:
  * Do NOT select or pass wholesale price columns to views for guest visitors.
  * Do NOT display wholesale prices in HTML markup, data attributes, or hidden fields.
  * Do NOT build authentication, cart, or checkout in this ticket.
  * Do NOT use heavy front-end frameworks (Tailwind/Bootstrap/Vue); use clean, readable semantic CSS.
* **Output & Verification**:
  * Inspect `phpunit.xml` to verify `<env name="DB_CONNECTION" value="sqlite"/>` and `<env name="DB_DATABASE" value=":memory:"/>`.
  * Run `php artisan test --filter=CatalogTest`.
  * Open `http://127.0.0.1:8000/` in browser to visually verify catalog cards and category filtering.
  * Fetch the public catalog via `curl -s http://127.0.0.1:8000/` and verify the output contains none of the seeded price values (e.g., checking for the exact price numbers seeded in `ProductSeeder`) and no currency symbols (`$`).

---

### Ticket T2: Customer Registration with Status "Pending"

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T1
* **Context**: Under Business Rule 2, wholesale business clients must register before they can be considered for purchasing. New registrations must default to status `pending` and must not be automatically logged in. Mass-assignment security must protect sensitive status attributes.
* **Task**:
  1. Create a migration to add `company_name`, `phone`, and `status` (`enum: ['pending', 'approved', 'rejected']`, default `'pending'`) to the `users` table.
  2. Update `User` model with `$fillable` attributes (`name`, `email`, `password`, `company_name`, `phone`), ensuring `status` and `is_admin` are explicitly omitted from `$fillable` (not mass-assignable), and add status helper methods (`isPending()`, `isApproved()`, `isRejected()`).
  3. Create registration Blade view (`auth/register.blade.php`) collecting: Contact Name, Email, Company/Business Name, Phone Number, Password, and Password Confirmation.
  4. Create `RegisterController` with `showRegistrationForm` and `register` methods. Validate required fields, email uniqueness, phone format, and password length.
  5. Store the new user with hashed password and status explicitly set to `pending` by the backend.
  6. Redirect to `/login` with a clear flash notice: "Registration submitted successfully! Your account is currently pending administrator review and approval."
  7. Write automated tests in `RegistrationTest` covering:
     - Valid registration creating a user with `status = 'pending'`.
     - Confirming the user is not authenticated upon registration.
     - Validation error handling for duplicate email, invalid phone, password mismatch.
     - Security test: asserting that posting `status=approved` or `is_admin=1` to registration has no effect, preserving `status = 'pending'`.
* **Acceptance Criteria**:
  * **AC1**: Migration cleanly adds `company_name`, `phone`, and `status` to `users` table.
  * **AC2**: Visiting `/register` presents a responsive B2B registration form.
  * **AC3**: Submitting valid form data creates a new record in `users` with `status = 'pending'`.
  * **AC4**: User is NOT logged in after registration (`auth()->check()` remains `false`).
  * **AC5**: User is redirected to `/login` with an informative pending status message.
  * **AC6**: Submitting duplicate email or mismatched passwords returns validation error feedback.
  * **AC7**: `status` and `is_admin` are NOT mass-assignable (omitted from `User::$fillable`), and submitting `status=approved` or `is_admin=1` in the registration payload has no effect, leaving the created user with `status = 'pending'`.
* **Constraints**:
  * `status` and `is_admin` must NOT be mass-assignable (must NOT be in `User::$fillable`).
  * Do NOT use Laravel Breeze, Fortify, or Jetstream. Implement standard controller-based authentication.
  * Do NOT automatically log in the registrant.
  * Do NOT allow the registration form or client request to specify or override the `status` field.
* **Output & Verification**:
  * Run `php artisan test --filter=RegistrationTest`.
  * Fill out the form in browser at `http://127.0.0.1:8000/register`.
  * Check the database record: `/Applications/XAMPP/xamppfiles/bin/mysql -u root wholesale_shop -e "SELECT name, email, company_name, status FROM users;"`.

---

### Ticket T3: Login Restricted to Approved Customers

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T2
* **Context**: Under Business Rule 3, pending and rejected customers must be blocked from logging in. Only approved customers are allowed into the authenticated session. Credentials and account status must be validated BEFORE a session is established.
* **Task**:
  1. Create login Blade view (`auth/login.blade.php`) with email, password, and remember me fields.
  2. Create `LoginController` with `showLoginForm`, `login`, and `logout` actions.
  3. Validate credentials and check account status BEFORE creating a session:
     - Verify credentials via `Auth::validate($credentials)` without logging the user in.
     - If credentials are invalid, return standard authentication error.
     - Retrieve the user by email (`$user = User::where('email', $request->email)->first()`).
     - Check account status before establishing session:
       - If `user->isPending()`: do NOT log in; redirect back with error: "Your account is pending administrator approval."
       - If `user->isRejected()`: do NOT log in; redirect back with error: "Your account registration has been rejected. Please contact support."
       - If `user->isApproved()`: log in the user via `Auth::login($user)`, regenerate session, and redirect to `/`.
  4. Implement `POST /logout` route that invalidates the session and regenerates CSRF token.
  5. Update navigation bar in `layouts/app.blade.php` to display Login/Register links for guests, and User Name + Logout button for authenticated users.
  6. Write automated tests in `LoginTest` for approved customer (success), pending customer (blocked), rejected customer (blocked), invalid password (blocked), and logout.
* **Acceptance Criteria**:
  * **AC1**: Visiting `/login` renders the login form.
  * **AC2**: Approved customer credentials validate and authenticate via `Auth::login()`, redirecting to the catalog.
  * **AC3**: Pending customer credentials fail with explicit notice: "Your account is pending administrator approval." No session is created.
  * **AC4**: Rejected customer credentials fail with explicit notice: "Your account registration has been rejected. Please contact support." No session is created.
  * **AC5**: Logging out terminates session and returns user to guest state.
  * **AC6**: Invalid email/password combinations display standard authentication error.
* **Constraints**:
  * Do NOT use the "Auth::attempt then logout" pattern; evaluate user status BEFORE calling `Auth::login()`.
  * Do NOT allow pending or rejected users to create an active session.
  * Do NOT include administrator logic or roles in this ticket (`is_admin` is introduced in T4).
* **Output & Verification**:
  * Seed sample users (`approved@wholesale.test`, `pending@wholesale.test`, `rejected@wholesale.test`).
  * Run `php artisan test --filter=LoginTest`.
  * Attempt login with each account type in browser and verify redirection, lack of active session for unapproved users, and displayed flash messages.

---

### Ticket T4: Admin Approves or Rejects Customers

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T3
* **Context**: Under Business Rule 4 & 6, an administrator manages customer registrations by reviewing applications and approving or rejecting them. Access to admin features must be protected by an admin-only middleware, and the customer management list must display only customer accounts (`is_admin = false`).
* **Task**:
  1. Create a migration adding `is_admin` boolean column (default `false`) to `users` table. Ensure `is_admin` is NOT in `User::$fillable`.
  2. Seed a default admin account (`admin@wholesale.test`, password `password123`, `is_admin = true`, `status = 'approved'`).
  3. Create `AdminMiddleware` checking `auth()->check() && auth()->user()->is_admin`. Register alias `'admin'`.
  4. Update `LoginController` to allow administrators (`is_admin = true`) to log in regardless of customer-specific status gates and redirect to `/admin/customers`.
  5. Create `Admin\CustomerController` with actions:
     - `index`: list customer registrations with status badges and filter tabs (All, Pending, Approved, Rejected), strictly filtering where `is_admin = false` so administrators are excluded from the customer list.
     - `approve`: update customer status to `'approved'` via atomic `PATCH /admin/customers/{user}/approve`.
     - `reject`: update customer status to `'rejected'` via atomic `PATCH /admin/customers/{user}/reject`.
  6. Create admin layout (`layouts/admin.blade.php`) and customer management view (`admin/customers/index.blade.php`) with quick "Approve" and "Reject" action buttons.
  7. Protect all `/admin/*` routes with `auth` and `admin` middleware.
  8. Write automated tests in `AdminCustomerTest` covering:
     - Guest access to `/admin/*` redirects to `/login`.
     - Authenticated non-admin access to `/admin/*` returns `403 Forbidden`.
     - Admin customer table displays only users with `is_admin = false`.
     - Admin can approve/reject pending customers.
     - Security check: asserting that `is_admin` is not mass-assignable via public requests.
* **Acceptance Criteria**:
  * **AC1**: Admin user can log in and access `/admin/customers`.
  * **AC2**: Guests attempting to access `/admin/*` are redirected to `/login`. Authenticated non-admin users attempting to access `/admin/*` receive `403 Forbidden`.
  * **AC3**: Admin customer table displays only customer accounts (`is_admin = false`), excluding administrators, and shows business name, applicant name, email, phone, current status, and registration date.
  * **AC4**: Clicking "Approve" changes target customer status to `approved` in the database with success feedback.
  * **AC5**: Clicking "Reject" changes target customer status to `rejected` in the database with success feedback.
  * **AC6**: A customer approved by the admin can immediately log in on the storefront.
  * **AC7**: `is_admin` is NOT mass-assignable (omitted from `User::$fillable`), and posting `is_admin=1` in registration or public endpoints has no effect.
* **Constraints**:
  * `is_admin` must NOT be mass-assignable (must NOT be in `User::$fillable`).
  * Do NOT display administrator accounts in the customer management list (`where('is_admin', false)`).
  * Do NOT allow non-admins to access admin endpoints via direct URL manipulation.
  * Do NOT build complex role systems or permission matrix libraries; a simple `is_admin` boolean meets MVP requirements cleanly.
* **Output & Verification**:
  * Run `php artisan test --filter=AdminCustomerTest`.
  * Log in as admin at `http://127.0.0.1:8000/login`, navigate to `/admin/customers`, verify admin accounts do not appear in the customer list, approve a pending user, and verify that the user can subsequently log in.

---

### Ticket T5: Prices Visible Only to Approved Customers (Server-Side)

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T4
* **Context**: Under Business Rules 1, 5, and 7, approved customers can see wholesale prices, but visitors and unapproved customers cannot. This price shielding must be strictly enforced server-side in the Blade rendering layer and controllers. An "approved" middleware is also introduced here to provide reusable request-level status enforcement across future tickets.
* **Task**:
  1. Create `EnsureCustomerIsApproved` middleware (alias `'approved'`) that verifies `auth()->check() && (auth()->user()->isApproved() || auth()->user()->is_admin)`. If not authenticated, redirect to `/login`; if authenticated but not approved, abort with `403 Forbidden`.
  2. Update `CatalogController` and Blade views (`catalog/index.blade.php`, `catalog/show.blade.php`):
     - For authenticated and approved users (`auth()->check() && (auth()->user()->isApproved() || auth()->user()->is_admin)`): select and display wholesale price clearly formatted (e.g., `$420.00`).
     - For guests: do NOT select or pass wholesale price to views; display a prominent badge "Wholesale Price: Available to approved customers — [Log in to view] / [Register]".
  3. Ensure no hidden HTML attributes (`data-price`, CSS `display:none`, JavaScript variables) contain the price for guests.
  4. Ensure administrators also have visibility of wholesale prices on the storefront.
  5. Write automated tests in `PriceShieldTest` asserting:
     - Guest response body does NOT contain the price string.
     - Approved customer response body DOES contain the formatted wholesale price.
     - The `'approved'` middleware allows approved customers/admins and denies unapproved accounts.
* **Acceptance Criteria**:
  * **AC1**: Visiting `/` as a guest renders product cards without any wholesale price values or hidden price fields.
  * **AC2**: Visiting `/products/{slug}` as a guest shows full specs but replaces price with a wholesale login prompt.
  * **AC3**: Logging in as an approved customer immediately reveals wholesale unit prices on both catalog and product detail pages.
  * **AC4**: Server-side tests confirm wholesale price numbers are completely omitted from the HTML payload delivered to guests.
  * **AC5**: The `'approved'` middleware exists, checks customer status on every request, and is registered for use on customer-restricted routes.
* **Constraints**:
  * Do NOT hide prices using client-side JavaScript or CSS (`display:none`). Price shielding must occur on the server before HTML is sent.
  * Do NOT expose prices in public API endpoints or JSON responses.
* **Output & Verification**:
  * Run `php artisan test --filter=PriceShieldTest`.
  * In browser, view a product as guest (verify price is absent). Log in as approved customer (verify price is visible).
  * Run `curl -s http://127.0.0.1:8000/products/{slug}` (using a real seeded product slug and its seeded price) to verify no price appears for guest requests, then repeat with an authenticated session cookie to confirm the price appears.

---

### Ticket T6: Shopping Cart

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T5
* **Context**: Under Business Rule 5, approved customers can add items to a shopping cart to prepare bulk wholesale orders. Guests and unapproved users cannot access or use the cart; routes are protected by the `'approved'` middleware.
* **Task**:
  1. Create `cart_items` table migration (`id`, `user_id`, `product_id`, `quantity`, `timestamps`) with foreign key constraints.
  2. Create `CartItem` model with relationships: `belongsTo(User)` and `belongsTo(Product)`.
  3. Create `CartController` with actions:
     - `index`: view cart contents with line item unit prices, quantities, line totals, and grand total.
     - `store`: add product to cart (or increment quantity if already present), validating product exists and quantity does not exceed available stock.
     - `update`: update quantity of an existing cart item (`quantity >= 1` and `<= stock`).
     - `destroy`: remove an item from the cart.
  4. Protect all cart routes (`/cart`, `/cart/items/*`) with `auth` and `'approved'` middleware.
  5. Create `cart/index.blade.php` view with an itemized table, quantity controls, "Remove" button, total summary, and "Proceed to Order" button.
  6. Add an "Add to Cart" form (with quantity input) on product cards and product detail view, visible only to approved logged-in customers.
  7. Add cart item count badge to the main navigation bar.
  8. Write automated tests in `CartTest` covering: adding items, quantity updates, stock limit enforcement, item removal, and preventing guest/unapproved access via `'approved'` middleware.
* **Acceptance Criteria**:
  * **AC1**: Only approved logged-in customers can access `/cart` or submit to cart endpoints. Guests are redirected to `/login`, and unapproved users receive `403 Forbidden` (enforced by `'approved'` middleware).
  * **AC2**: Clicking "Add to Cart" adds item to user's cart in the database.
  * **AC3**: Adding the same product again increments the existing row's quantity rather than creating duplicates.
  * **AC4**: Cart view displays correct line totals (`price * quantity`) and grand total.
  * **AC5**: Customer can update quantity or remove items from their cart.
  * **AC6**: Adding or updating to a quantity greater than available inventory stock is rejected with an error message.
* **Constraints**:
  * Do NOT create guest/session-only carts; carts belong strictly to authenticated approved customer accounts.
  * Do NOT implement promo codes, multi-currency conversions, or tax engine integrations.
* **Output & Verification**:
  * Run `php artisan test --filter=CartTest`.
  * Log in as an approved customer, add 5 units of a phone, verify cart line total and grand total calculation.
  * Attempt to add 9999 units (exceeding stock) and verify the backend validation error.

---

### Ticket T7: Order Placement with Server-Side Validation of Stock and Prices

* **Priority**: High
* **Status**: **Not done**
* **Dependencies**: T6
* **Context**: Under Business Rule 5 & 7, approved customers submit wholesale orders. The backend must validate available inventory, lock in current wholesale prices, deduct stock atomically within a database transaction, and clear the cart. Checkout routes are protected by `'approved'` middleware, and order confirmation access must already be scoped strictly to the order owner.
* **Task**:
  1. Create `orders` migration (`id`, `user_id`, `order_number`, `total_amount`, `status` [pending, processing, completed, cancelled], `shipping_address`, `contact_phone`, `notes`, `timestamps`).
  2. Create `order_items` migration (`id`, `order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `timestamps`).
  3. Create `Order` and `OrderItem` models with relationships (`User hasMany Order`, `Order hasMany OrderItem`, `OrderItem belongsTo Product`).
  4. Protect checkout and order submission routes with `auth` and `'approved'` middleware.
  5. Create `OrderController` with actions:
     - `create`: checkout view showing shipping address form and order summary from current cart.
     - `store`: process order inside a `DB::transaction()`:
       - Verify cart is not empty.
       - Re-query each product directly from the database and lock for update.
       - Verify requested quantity `<= product->stock`. If insufficient, throw exception and abort.
       - Calculate total amount using fresh database prices (never trust submitted client values).
       - Create `Order` record with generated unique order number (e.g. `ORD-20261022-XXXX`).
       - Create `OrderItem` snapshots (storing product name and unit price at time of purchase).
       - Decrement product stock: `$product->decrement('stock', $item->quantity)`.
       - Delete user's cart items.
     - `show`: order confirmation screen displaying order number, summary, and current status. Enforce that `$order->user_id === auth()->id()`; if not, abort with `403 Forbidden` or `404 Not Found`.
  6. Create Blade views `orders/checkout.blade.php` and `orders/confirmation.blade.php`.
  7. Write automated tests in `OrderPlacementTest` for:
     - Successful order placement.
     - Stock deduction and cart clearing.
     - Rollback on out-of-stock items.
     - Price snapshot verification.
     - Order confirmation authorization (Customer A cannot view Customer B's order confirmation, returning `403/404`).
* **Acceptance Criteria**:
  * **AC1**: Approved customer can visit `/checkout` and view their order summary (enforced by `'approved'` middleware).
  * **AC2**: Submitting checkout with shipping details creates `orders` and `order_items` records and generates unique order number.
  * **AC3**: Product stock in `products` table is decremented by ordered quantities.
  * **AC4**: User's cart is completely cleared upon successful order placement.
  * **AC5**: If another process reduces stock before checkout finishes, transaction rolls back and displays out-of-stock notification.
  * **AC6**: Order items permanently store the unit price at purchase time so future product price edits do not alter past orders.
  * **AC7**: Order confirmation `show` action strictly verifies that the order belongs to the authenticated customer; viewing another user's order confirmation returns `403 Forbidden` or `404 Not Found`.
* **Constraints**:
  * Do NOT integrate third-party payment gateways (Stripe/PayPal). Wholesale orders follow a purchase-order / invoice workflow.
  * Do NOT allow checkout if cart is empty.
* **Output & Verification**:
  * Run `php artisan test --filter=OrderPlacementTest`.
  * Place an order for 2 units of an item with stock 10. Verify stock in database becomes 8.
  * Verify `orders` and `order_items` tables contain complete records with accurate totals.
  * Attempt to view another customer's order confirmation URL and verify `403/404`.

---

### Ticket T8: Admin Product, Price, and Stock Management

* **Priority**: Medium
* **Status**: **Not done**
* **Dependencies**: T4, T1
* **Context**: Under Business Rule 6, the administrator manages product catalog details, wholesale unit prices, and available inventory stock levels. Deletion must protect historical order records.
* **Task**:
  1. Create `Admin\ProductController` with standard CRUD actions:
     - `index`: paginated table of products showing name, category, price, stock, and edit/delete actions.
     - `create` & `store`: form and validation to add new products (category, name, description, image URL/path, wholesale price, stock).
     - `edit` & `update`: form and validation to edit existing product information, wholesale price, and inventory stock.
     - `destroy`: delete a product, strictly preventing deletion if any existing order items reference the product (returning an error notification).
  2. Create admin product Blade views:
     - `admin/products/index.blade.php`
     - `admin/products/create.blade.php`
     - `admin/products/edit.blade.php`
  3. Validate fields: `name` (required, string), `category_id` (exists:categories,id), `price` (numeric, min:0.01), `stock` (integer, min:0).
  4. Add admin navigation tabs to switch between Customers and Products (Orders navigation tab is introduced in T9).
  5. Write automated tests in `AdminProductTest` covering:
     - Product creation.
     - Editing wholesale price and inventory stock.
     - Form validation errors.
     - Non-admin access denial (`403 Forbidden`).
     - Product deletion prevention when order items reference the product.
* **Acceptance Criteria**:
  * **AC1**: Admin can access `/admin/products` and view all catalog items with their prices and stock.
  * **AC2**: Admin can create a new mobile phone or accessory, which immediately appears on the public catalog.
  * **AC3**: Admin can update wholesale price and stock; updated values reflect immediately on the storefront.
  * **AC4**: Form enforces required validation (positive price, non-negative integer stock).
  * **AC5**: Non-admin users attempting to access `/admin/products/*` receive `403 Forbidden`.
  * **AC6**: Attempting to delete a product that has existing order items referencing it is prevented, returning a protective error message.
* **Constraints**:
  * `destroy` must NOT delete products referenced by past order items to protect order history integrity.
  * Do NOT build complex multi-warehouse inventory systems or variant matrices (color/storage matrix). Keep product schema straightforward.
* **Output & Verification**:
  * Run `php artisan test --filter=AdminProductTest`.
  * In admin panel, change a product's price from `$500.00` to `$475.00` and stock from `10` to `25`.
  * Log in as approved customer and confirm updated price and stock on the storefront.
  * Attempt to delete a product that is linked to an existing order and verify deletion is prevented.

---

### Ticket T9: Admin Order View and Customer Order History

* **Priority**: Medium
* **Status**: **Not done**
* **Dependencies**: T7, T4
* **Context**: Under Business Rules 5 and 6, approved customers must be able to view their own past wholesale orders, while administrators can review all wholesale orders and update their fulfillment status. Customer order routes are protected by `'approved'` middleware, and the `show` action reuses the ownership check established in T7.
* **Task**:
  1. Protect customer order history routes (`/orders`, `/orders/{id}`) with `auth` and `'approved'` middleware.
  2. Implement customer order history in `OrderController`:
     - `index`: list orders belonging strictly to `auth()->user()->orders()`.
     - `show`: view details of a specific order belonging to the authenticated customer, reusing the ownership check established in T7 (`$order->user_id === auth()->id()`).
  3. Create customer views `orders/index.blade.php` and `orders/show.blade.php`.
  4. Enforce strict authorization: a customer attempting to view an order belonging to another user must be denied with `403 Forbidden` or `404 Not Found`.
  5. Create `Admin\OrderController` with actions:
     - `index`: list all orders across all customers with filter by status (`pending`, `processing`, `completed`, `cancelled`).
     - `show`: view complete order details, customer contact/company info, and line items.
     - `updateStatus`: update order status via `PATCH /admin/orders/{order}/status`.
  6. Add "Orders" navigation tab to the admin navbar (joining Customers and Products).
  7. Create admin order views `admin/orders/index.blade.php` and `admin/orders/show.blade.php`.
  8. Write automated tests in `OrderAccessTest` verifying customer scoping, cross-customer data isolation (reusing T7 ownership validation), and admin status updates.
* **Acceptance Criteria**:
  * **AC1**: Customer visiting `/orders` sees only their own past orders with order numbers, dates, status, and grand total.
  * **AC2**: Customer visiting `/orders/{id}` sees line items, quantities, unit prices, and shipping details for their own order, reusing the ownership check from T7.
  * **AC3**: Customer attempting to view another customer's order ID receives `403 Forbidden` or `404 Not Found`.
  * **AC4**: Admin visiting `/admin/orders` sees all wholesale orders across all customers.
  * **AC5**: Admin can update order status (e.g. from `pending` to `processing` or `completed`) with immediate feedback.
* **Constraints**:
  * Do NOT build external shipping carrier tracking APIs or invoice PDF rendering engines. Standard clean Blade views satisfy MVP requirements.
* **Output & Verification**:
  * Run `php artisan test --filter=OrderAccessTest`.
  * Create Order A with Customer 1, Order B with Customer 2.
  * Log in as Customer 1, verify only Order A is visible and attempting to open Order B URL returns 403/404.
  * Log in as Admin, verify both orders appear and update Order A to `completed`.

---

### Ticket T10: Final Testing, README Update, and Presentation Notes

* **Priority**: Medium
* **Status**: **Not done**
* **Dependencies**: T1, T2, T3, T4, T5, T6, T7, T8, T9
* **Context**: Perform final end-to-end verification of all MVP business rules, verify clean zero-warning test suite, update the main `README.md`, and produce presentation notes for oral defense. Note that each ticket's row in the `README.md` status table is updated incrementally as that ticket is tested and committed; T10 verifies that all rows are marked "Done".
* **Task**:
  1. Run the entire automated test suite (`php artisan test`) and verify 100% pass rate.
  2. Verify that running `php artisan migrate:fresh --seed` from scratch builds a fully functional demo environment with:
     - Seeded Admin account (`admin@wholesale.test` / `password123`)
     - Seeded Approved Customer (`approved@wholesale.test` / `password123`)
     - Seeded Pending Customer (`pending@wholesale.test` / `password123`)
     - Seeded Rejected Customer (`rejected@wholesale.test` / `password123`)
     - Realistic catalog of mobile phones and accessories across multiple categories.
  3. Update `README.md`:
     - Verify Ticket Status Table reflects that all tickets T0 through T10 are "Done" (noting each prior ticket was marked done upon its own commit).
     - Document demo accounts and testing workflows for each of the 7 business rules.
  4. (Optional) Create `docs/presentation_notes.md` tailored for a master's student defense:
     - Explaining why server-side price shielding was chosen over frontend concealment.
     - Explaining the database transaction model for stock reservation and price locking.
     - Key files to show during the presentation and live demonstration walkthrough steps.
* **Acceptance Criteria**:
  * **AC1**: Full test suite passes completely with zero failures (`php artisan test`).
  * **AC2**: Fresh database migration and seed (`php artisan migrate:fresh --seed`) completes without errors.
  * **AC3**: `README.md` accurately documents demo accounts, setup commands, and all 7 business rules with all tickets marked "Done".
  * **AC4** *(Optional)*: `docs/presentation_notes.md` is present, well-structured, and provides clear, professional answers to expected examiner questions.
* **Constraints**:
  * Do NOT add new features or external packages in this ticket. Focus entirely on verification, documentation, and presentation readiness.
* **Output & Verification**:
  * Run `php artisan test` and verify output.
  * Run `php artisan migrate:fresh --seed` and verify database population.
  * Review `README.md` and optional `docs/presentation_notes.md` for clarity and completeness.
