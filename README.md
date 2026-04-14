# 🛒 MT.com Online Shop — E-Commerce DBMS Project
**PHP + MySQL + XAMPP**

---

## 📁 My Project Structure

```
ecommerce/
├── index.html          ← Main frontend (Shop + Admin)
├── ecommerce.sql       ← Database schema + sample data
├── config/
│   └── db.php          ← MySQL connection settings
└── api/
    ├── categories.php  ← Categories API
    ├── products.php    ← Products CRUD API
    ├── customers.php   ← Customers API
    └── orders.php      ← Orders + order items API
```

---


## 🗄️ Database Tables

| Table | Description |
|-------|-------------|
| `categories` | Product categories |
| `products` | Products with price, stock |
| `customers` | Registered customers |
| `orders` | Order header (customer + total) |
| `order_items` | Order line items (product + qty) |

### Relationships (Foreign Keys):
```
categories ──< products
customers  ──< orders ──< order_items >── products
```

---