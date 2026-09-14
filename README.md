# Computer Shop Management System (PHP + MySQL, MVC)

A small web-based project for managing the daily activities of a computer shop,
with 4 roles: **admin, vendor, seller, customer**. Written in plain PHP with
procedural `mysqli` and prepared statements. No frameworks, no Composer, no
build step. Copy it into XAMPP and it runs.

---

## 1. Install (XAMPP)

1. Copy the `computer_shop` folder into `C:\xampp\htdocs\`
   so it becomes `htdocs/computer_shop/`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin` → **Import** → choose `schema.sql` → **Go**.
4. Open `http://localhost/computer_shop/`.
5. Sign in with one of the test accounts (see section 8).

If your MySQL uses a password, change `DB_PASS` in `config/config.php`.

---

## 2. Folder structure

```
computer_shop/
├── index.php                          Front controller: the ONLY entry point (router)
├── schema.sql                         Schema + sample data
├── README.md
│
├── config/
│   └── config.php                     DB connection, session settings, app constants
│
├── models/                            M — every SQL query lives here
│   ├── user_model.php                 all 4 roles (one users table)
│   ├── product_model.php              catalogue + stock (vendor + seller products)
│   ├── order_model.php                customer orders + payment
│   ├── damage_model.php               vendor damage reports
│   ├── delivery_model.php             vendor delivery status
│   ├── review_model.php               customer reviews
│   └── announcement_model.php         admin announcements
│
├── controllers/                       C — request handling, validation, decisions
│   ├── auth_controller.php            login / register / logout
│   ├── admin_controller/
│   ├── vendor_controller/
│   │   ├── add_product_process.php
│   │   ├── damage_product_process.php
│   │   ├── delivery_status_process.php
│   │   ├── edit_product_process.php
│   │   └── navbar.php
│   ├── seller_controller/
│   ├── customer_controller/
│   └── ajax_controller.php            all JSON endpoints
│
├── views/                             V — HTML only
│   ├── partials/                      header.php, footer.php (shared layout)
│   ├── auth/                          login.php, register.php
│   ├── admin/                         dashboard.php
│   ├── vendor/
│   │   ├── add_product.php
│   │   ├── check_product.php
│   │   ├── damage_product.php
│   │   ├── delete_product.php
│   │   ├── delivery_status.php
│   │   ├── edit_product.php
│   │   └── manage_products.php
│   ├── seller/                        dashboard.php
│   └── customer/                      dashboard.php
│
└── assets/
    ├── vendor_style.css
    └── js/app.js                      validation, escaping, live search, AJAX
```

**The MVC rule used throughout:** a view never runs a query, and a model never
prints HTML. The controller sits in the middle: it reads `$_POST`, validates,
calls the model, then `require`s the view.

---

## 3. How the router works

Every URL looks like this:

```
index.php?page=<dashboard>&action=<what to do>&id=<row id>
```

| URL | What happens |
| --- | --- |
| `index.php?page=login` | Login page |
| `index.php?page=register` | Signup page |
| `index.php?page=admin` | Admin dashboard |
| `index.php?page=vendor&action=edit&id=4` | Load product 4 into the vendor's edit form |
| `index.php?page=seller&action=discount&id=7` | Apply a discount to product 7 |
| `index.php?page=customer&action=order` | Place an order |
| `index.php?page=ajax&action=search_products&q=laptop` | Returns JSON |
| `index.php?page=logout` | Sign out |

`index.php` loads config → models → controllers, checks the session timeout,
then sends the request to one controller. `require_role('admin')` blocks
anyone who is not an admin before the controller even starts.

---

## 4. The four roles

Vendor and seller are **independent** — each manages its own separate product
listings; a vendor does not supply stock to a seller.

| Role | Manages (CRUD) | Feature 1 | Feature 2 | Feature 3 |
| --- | --- | --- | --- | --- |
| **Admin** | User accounts (all roles) | Approve / reject new vendor sign-ups | Post system-wide announcements | Total revenue overview |
| **Vendor** | Own products | Report damaged stock | Track delivery status per order | — |
| **Seller** | Own products | Sell summary / sales report | Apply product discounts | Live stock notification (low-stock alert) |
| **Customer** | My orders | Place an order | Choose a payment method | Leave a product review |

### How the roles connect

- A **customer** places an order and chooses a payment method → the order is
  recorded against the relevant product.
- The **admin** reviews new **vendor** sign-ups and approves or rejects them
  before the vendor account becomes active.
- Vendors and sellers each manage their own catalogue independently — there is
  no hand-off of stock between them.
- The **admin** can see total revenue across all sales, and posts announcements
  visible to every role.

---

## 5. Requirement checklist

| Requirement | Where to look |
| --- | --- |
| **MVC** | `models/`, `controllers/`, `views/`, routed by `index.php` |
| **DB (MySQLi procedural)** | every function in `models/` uses `mysqli_prepare` |
| **Auth (session)** | `controllers/auth_controller.php`, `config/config.php` |
| **PHP validation** | the `if / elseif` chain at the top of every controller action |
| **JS validation** | `validateForm()` in `assets/js/app.js`, called by `onsubmit` |
| **AJAX / JSON** | `controllers/ajax_controller.php` |
| **UI (HTML/CSS)** | `views/`, `assets/vendor_style.css` |
| **Basic web security** | see section 6 |
| **Feature completeness** | CRUD + search + features per role |

---

## 6. Security

| Attack | Defence | File |
| --- | --- | --- |
| SQL injection | Prepared statements everywhere — user text is never glued into SQL | all `models/` |
| Stolen passwords | `password_hash()` on save, `password_verify()` on login | `user_model.php` |
| Idle machines | Automatic sign-out after the session timeout, with `session_regenerate_id(true)` on login | `config.php`, `auth_controller.php` |
| Wrong role | `require_role()` before the controller runs | `index.php` |

---

## 7. Settings you can change

All in `config/config.php`:

```php
define('SESSION_TIMEOUT', 1800); // idle sign-out, in seconds (30 minutes)
define('LOW_STOCK',       3);    // a product at or below this triggers the seller's low-stock alert
define('CURRENCY',        '৳');  // symbol shown next to prices
```

---

## 8. Test accounts

| Role | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `admin123` |
| Vendor | `vendor` | `vendor123` |
| Seller | `seller` | `seller123` |
| Customer | `customer` | `customer123` |

---
"Copyright (c) 2026. All rights reserved."
---
