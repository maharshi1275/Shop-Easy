# ShopEasy — PHP Online Shopping App

A simple e-commerce site built with HTML, CSS, and PHP. Uses JSON files for storage (no MySQL required).

## Features

- Product catalog with search and product detail pages
- Shopping cart (session-based) with quantity update and remove
- Checkout with shipping address and payment method (demo)
- User accounts: register, login, logout, per-user order history
- Admin panel: dashboard, product CRUD (with image upload), order management

## Requirements

- PHP 8.0+ (uses `match`-free code but `str_starts_with`, arrow functions, etc.)
- No database needed — data is stored in `data/*.json`

## Run locally

From the project root:

```bash
php -S localhost:8000
```

Then open <http://localhost:8000>.

## Default admin login

- Email: `admin@shop.local`
- Password: `admin123`

Change this immediately by registering a new user, then editing `data/users.json` to set `"is_admin": true` for your user.

## File layout

```
commerce/
├── config/config.php          App config + session bootstrap
├── includes/
│   ├── functions.php          Helpers: JSON I/O, auth, cart
│   ├── header.php / footer.php
├── assets/css/style.css
├── data/                      JSON "database" (gitignore in real use)
│   ├── users.json
│   ├── products.json
│   └── orders.json
├── uploads/                   Uploaded product images
├── admin/                     Admin panel pages
├── index.php                  Catalog
├── product.php                Detail page
├── cart.php                   Cart + cart actions
├── checkout.php               Checkout form
├── register.php / login.php / logout.php
└── orders.php                 User order history
```

## Security notes

This app uses:
- `password_hash` / `password_verify` for credentials
- `htmlspecialchars` on all output (`e()` helper)
- File locks on JSON read/write
- File-type checks on image upload
- Admin-only routes gated by `require_admin()`

It does **not** implement CSRF tokens or rate limiting — add those before any real deployment. The `data/` directory has an Apache `.htaccess` that denies direct access; for nginx/other servers, restrict access to that path in server config.
