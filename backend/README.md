# 🛍️ Shopping API - Backend System

A complete e-commerce backend API built with Laravel 12, featuring multi-language support (Kurdish, Arabic, English), advanced order management, and comprehensive admin controls.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Database Structure](#-database-structure)
- [API Endpoints](#-api-endpoints)
- [Authentication](#-authentication)
- [Multi-Language Support](#-multi-language-support)
- [Settings System](#-settings-system)
- [Order Tracking](#-order-tracking)
- [Address Book](#-address-book)
- [Usage Examples](#-usage-examples)

---

## ✨ Features

### Core Features
- ✅ **Authentication & Authorization** (Sanctum)
- ✅ **Multi-language Support** (Kurdish, Arabic, English)
- ✅ **Role-based Access Control** (Admin/User)
- ✅ **File Upload System** (Images with validation)
- ✅ **Firebase Cloud Messaging** (Push Notifications)
- ✅ **Cache System** (Settings & Performance)

### E-commerce Features
- ✅ **Product Management** (CRUD with multi-language)
- ✅ **Category Management** (CRUD with multi-language)
- ✅ **Shopping Cart System**
- ✅ **Order Management**
- ✅ **Order Tracking Timeline**
- ✅ **Address Book** (Multiple shipping addresses)
- ✅ **Coupon & Discount System**
- ✅ **Review & Rating System** (5-star)
- ✅ **Favorites/Wishlist**

### Admin Features
- ✅ **Dashboard with Statistics**
- ✅ **Order Management & Tracking**
- ✅ **Product & Category Management**
- ✅ **Coupon Management**
- ✅ **Banner/Slider Management**
- ✅ **Dynamic Settings Control**
- ✅ **Notification System**
- ✅ **User Management**

### Advanced Features
- ✅ **Dynamic Settings** (App config, colors, shipping fees)
- ✅ **Search & Filter** (Products with price range, sorting)
- ✅ **Featured Items**
- ✅ **Stock Management**
- ✅ **Monthly Revenue Charts**
- ✅ **Top Selling Items & Customers**

---

## 🛠️ Tech Stack

- **Framework:** Laravel 12.40.2
- **PHP:** 8.5.0
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **Notifications:** Firebase Cloud Messaging (FCM)
- **Storage:** Local Storage (configurable)
- **Cache:** File-based (configurable)

---

## 📦 Installation

### Prerequisites
- PHP >= 8.5
- Composer
- MySQL
- Node.js & NPM (for assets)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/ibrahmcode/api_shop.git
cd api_shop
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_shop
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure Firebase** (for notifications)
- Place your `firebase-credentials.json` in the root directory
- Update `.env`:
```env
FIREBASE_CREDENTIALS=firebase-credentials.json
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Start the server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

---

## 🗄️ Database Structure

### Tables (15)

| Table | Description |
|-------|-------------|
| `users` | User accounts with role (admin/user) |
| `categories` | Product categories (multi-language) |
| `items` | Products/Items (multi-language) |
| `orders` | Customer orders |
| `order_items` | Order line items |
| `order_tracking` | Order status timeline |
| `addresses` | User shipping addresses |
| `carts` | Shopping cart items |
| `reviews` | Product reviews & ratings |
| `coupons` | Discount coupons |
| `user_favorite_items` | User favorites/wishlist |
| `notifications` | User notifications |
| `fcm_tokens` | Firebase device tokens |
| `settings` | Dynamic app settings |
| `banners` | Homepage sliders/banners |

---

## 🔌 API Endpoints

### Total: **100 Endpoints**
- **Public Routes:** 15 endpoints
- **User Routes:** 48 endpoints
- **Admin Routes:** 37 endpoints

### Public Endpoints

#### Authentication
```http
POST   /api/register
POST   /api/login
```

#### Settings & Configuration
```http
GET    /api/settings
GET    /api/settings/config
GET    /api/settings/{key}
GET    /api/content/{page}
GET    /api/languages
```

#### Banners
```http
GET    /api/banners
GET    /api/banners/{banner}
```

#### Categories & Items
```http
GET    /api/categories
GET    /api/categories/{category}
GET    /api/categories/{category}/items
GET    /api/categories/{category}/items/{item}
GET    /api/items/search
```

#### Reviews
```http
GET    /api/items/{item}/reviews
```

---

### User Endpoints (Requires Authentication)

#### Authentication
```http
POST   /api/user/logout
```

#### Profile Management
```http
GET    /api/user/profile
PUT    /api/user/profile
POST   /api/user/profile/change-password
POST   /api/user/profile/avatar
DELETE /api/user/profile/avatar
```

#### Address Book
```http
GET    /api/user/addresses
POST   /api/user/addresses
GET    /api/user/addresses/default
GET    /api/user/addresses/{address}
PUT    /api/user/addresses/{address}
DELETE /api/user/addresses/{address}
POST   /api/user/addresses/{address}/set-default
```

#### Shopping Cart
```http
GET    /api/user/cart
POST   /api/user/cart
PUT    /api/user/cart/{cart}
DELETE /api/user/cart/{cart}
POST   /api/user/cart/clear
GET    /api/user/cart/count
```

#### Orders
```http
GET    /api/user/orders
POST   /api/user/orders
GET    /api/user/orders/{order}
PATCH  /api/user/orders/{order}/status
GET    /api/user/orders/{order}/tracking
POST   /api/user/orders/{order}/cancel
DELETE /api/user/orders/{order}
```

#### Favorites
```http
GET    /api/user/favorites
POST   /api/user/favorites
DELETE /api/user/favorites/{item}
GET    /api/user/favorites/check/{item}
POST   /api/user/favorites/toggle
```

#### Reviews
```http
GET    /api/user/reviews
POST   /api/user/items/{item}/reviews
PUT    /api/user/reviews/{review}
DELETE /api/user/reviews/{review}
```

#### Coupons
```http
POST   /api/user/coupons/validate
```

#### Notifications
```http
GET    /api/user/notifications
GET    /api/user/notifications/unread-count
POST   /api/user/notifications/{id}/read
POST   /api/user/notifications/mark-all-read
```

---

### Admin Endpoints (Requires Admin Role)

#### Dashboard
```http
GET    /api/admin/dashboard
GET    /api/admin/dashboard/monthly-revenue
GET    /api/admin/dashboard/top-selling-items
GET    /api/admin/dashboard/recent-orders
GET    /api/admin/dashboard/order-status-distribution
GET    /api/admin/dashboard/top-customers
```

#### Categories
```http
GET    /api/admin/categories
POST   /api/admin/categories
GET    /api/admin/categories/{category}
PUT    /api/admin/categories/{category}
DELETE /api/admin/categories/{category}
```

#### Items
```http
GET    /api/admin/categories/{category}/items
POST   /api/admin/categories/{category}/items
GET    /api/admin/categories/{category}/items/{item}
POST   /api/admin/categories/{category}/items/{item}
DELETE /api/admin/categories/{category}/items/{item}
```

#### Orders
```http
GET    /api/admin/orders
GET    /api/admin/orders/statistics
GET    /api/admin/orders/{order}
PATCH  /api/admin/orders/{order}/status
DELETE /api/admin/orders/{order}
```

#### Coupons
```http
GET    /api/admin/coupons
POST   /api/admin/coupons
GET    /api/admin/coupons/{coupon}
PUT    /api/admin/coupons/{coupon}
DELETE /api/admin/coupons/{coupon}
PATCH  /api/admin/coupons/{coupon}/toggle
```

#### Settings
```http
GET    /api/admin/settings
GET    /api/admin/settings/groups
GET    /api/admin/settings/{key}
PUT    /api/admin/settings/{key}
POST   /api/admin/settings/bulk-update
POST   /api/admin/settings/clear-cache
```

#### Banners
```http
GET    /api/admin/banners
POST   /api/admin/banners
GET    /api/admin/banners/{banner}
POST   /api/admin/banners/{banner}
DELETE /api/admin/banners/{banner}
PATCH  /api/admin/banners/{banner}/toggle
POST   /api/admin/banners/reorder
```

#### Notifications
```http
GET    /api/admin/notifications
GET    /api/admin/notifications/users
POST   /api/admin/notifications/send-to-user
POST   /api/admin/notifications/send-to-all
```

---

## 🔐 Authentication

### Registration
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "fcm_token": "device_token_here" (optional)
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123",
  "fcm_token": "device_token_here" (optional)
}

Response:
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|xyz..."
  }
}
```

### Using Token
```http
GET /api/user/profile
Authorization: Bearer 1|xyz...
```

---

## 🌐 Multi-Language Support

### Supported Languages
- **Kurdish (ku)** - Default
- **Arabic (ar)**
- **English (en)**

### Usage
Send language preference in header:
```http
Accept-Language: ku
```

Or as query parameter:
```http
GET /api/categories?lang=ar
```

### Language Response
```json
{
  "success": true,
  "locale": "ku",
  "data": [
    {
      "id": 1,
      "name": "خواردن",
      "name_ar": "طعام",
      "name_en": "Food"
    }
  ]
}
```

### Get Available Languages
```http
GET /api/languages

Response:
{
  "success": true,
  "data": [
    {
      "code": "ku",
      "name": "کوردی",
      "name_en": "Kurdish",
      "is_default": true,
      "rtl": true
    },
    {
      "code": "ar",
      "name": "عربي",
      "name_en": "Arabic",
      "is_default": false,
      "rtl": true
    },
    {
      "code": "en",
      "name": "English",
      "name_en": "English",
      "is_default": false,
      "rtl": false
    }
  ]
}
```

---

## ⚙️ Settings System

### Dynamic Configuration
The app uses a powerful settings system that allows admin to control everything without code changes.

### Setting Groups
1. **General** - App name, logo, currency, language
2. **Appearance** - 7 customizable colors
3. **Contact** - Phone, email, address, WhatsApp
4. **Social Media** - Facebook, Instagram, Twitter, YouTube
5. **Shipping** - Fees, free shipping threshold, tax rate
6. **Content** - About us, terms, privacy, return policy

### Get All Settings
```http
GET /api/settings/config

Response:
{
  "success": true,
  "data": {
    "app": {
      "name": "Shopping App",
      "logo": "url/to/logo.png",
      "currency": "IQD",
      "language": "ku",
      "supported_languages": ["ku", "ar", "en"]
    },
    "colors": {
      "primary": "#FF6B6B",
      "secondary": "#4ECDC4",
      "background": "#F7F7F7",
      "text": "#333333",
      "success": "#4CAF50",
      "error": "#F44336",
      "warning": "#FF9800"
    },
    "contact": {
      "phone": "+964 770 123 4567",
      "email": "info@shopping.com",
      "address": "Erbil, Kurdistan",
      "whatsapp": "+964 770 123 4567"
    },
    "social": {
      "facebook": "https://facebook.com/...",
      "instagram": "https://instagram.com/...",
      "twitter": "https://twitter.com/...",
      "youtube": "https://youtube.com/..."
    },
    "shipping": {
      "fee": 5000,
      "free_above": 50000,
      "tax_rate": 0
    },
    "features": {
      "reviews_enabled": true,
      "coupons_enabled": true,
      "featured_items_count": 10
    }
  }
}
```

### Update Setting (Admin)
```http
PUT /api/admin/settings/primary_color
Authorization: Bearer {admin_token}

{
  "value": "#FF0000"
}
```

---

## 📦 Order Tracking

### Order Status Flow
1. **pending** - چاوەڕوانی (Pending)
2. **confirmed** - پەسەندکراوە (Confirmed)
3. **processing** - لە ئامادەکردندایە (Processing)
4. **shipped** - نێردراوە (Shipped)
5. **delivered** - گەیشتووە (Delivered)
6. **cancelled** - هەڵوەشێنراوەتەوە (Cancelled)

### Get Order Tracking
```http
GET /api/user/orders/{order_id}/tracking

Response:
{
  "success": true,
  "data": {
    "order_id": 5,
    "current_status": "shipped",
    "tracking_history": [
      {
        "id": 1,
        "status": "pending",
        "status_label": "چاوەڕوانی",
        "note": "Order created",
        "created_at": "2025-12-05 10:00:00",
        "created_at_human": "2 hours ago"
      },
      {
        "id": 2,
        "status": "confirmed",
        "status_label": "پەسەندکراوە",
        "note": "Order confirmed by admin",
        "created_at": "2025-12-05 10:30:00",
        "created_at_human": "1 hour ago"
      },
      {
        "id": 3,
        "status": "processing",
        "status_label": "لە ئامادەکردندایە",
        "note": "Preparing your order",
        "created_at": "2025-12-05 11:00:00",
        "created_at_human": "30 minutes ago"
      },
      {
        "id": 4,
        "status": "shipped",
        "status_label": "نێردراوە",
        "note": "Order shipped via courier",
        "created_at": "2025-12-05 12:00:00",
        "created_at_human": "just now"
      }
    ]
  }
}
```

---

## 📍 Address Book

### Features
- Multiple shipping addresses per user
- Set default address
- Full address information
- Label addresses (home, work, other)

### Add Address
```http
POST /api/user/addresses

{
  "label": "home",
  "recipient_name": "محمد احمد",
  "phone": "+964 770 123 4567",
  "city": "هەولێر",
  "area": "ئەنکاوە",
  "street_address": "شەقامی ٦٠ مەتری، ژمارە ١٢٣",
  "additional_info": "نزیک پارکی شاندەر",
  "postal_code": "44001",
  "is_default": true
}
```

### Get All Addresses
```http
GET /api/user/addresses

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "label": "home",
      "recipient_name": "محمد احمد",
      "phone": "+964 770 123 4567",
      "city": "هەولێر",
      "area": "ئەنکاوە",
      "street_address": "شەقامی ٦٠ مەتری، ژمارە ١٢٣",
      "additional_info": "نزیک پارکی شاندەر",
      "postal_code": "44001",
      "is_default": true,
      "full_address": "شەقامی ٦٠ مەتری، ژمارە ١٢٣, ئەنکاوە, هەولێر, 44001"
    }
  ]
}
```

### Set Default Address
```http
POST /api/user/addresses/{address_id}/set-default
```

---

## 💡 Usage Examples

### 1. Create Order
```http
POST /api/user/orders

{
  "items": [
    {
      "item_id": 1,
      "quantity": 2
    },
    {
      "item_id": 5,
      "quantity": 1
    }
  ],
  "shipping_address": "شەقامی ٦٠ مەتری، هەولێر",
  "phone": "+964 770 123 4567",
  "notes": "پێم بڵێن کاتێک گەیشت"
}
```

### 2. Apply Coupon
```http
POST /api/user/coupons/validate

{
  "code": "SUMMER2025",
  "subtotal": 50000
}

Response:
{
  "success": true,
  "data": {
    "valid": true,
    "coupon": {
      "code": "SUMMER2025",
      "type": "percentage",
      "value": 20
    },
    "discount_amount": 10000,
    "final_amount": 40000
  }
}
```

### 3. Add Review
```http
POST /api/user/items/{item_id}/reviews

{
  "rating": 5,
  "comment": "بەرهەمێکی زۆر باشە، پێشنیاری دەکەم"
}
```

### 4. Search Products
```http
GET /api/items/search?q=laptop&min_price=500000&max_price=1000000&sort=price_asc
```

### 5. Get Dashboard Statistics (Admin)
```http
GET /api/admin/dashboard

Response:
{
  "success": true,
  "data": {
    "total_revenue": 5000000,
    "total_orders": 150,
    "pending_orders": 12,
    "total_customers": 45,
    "total_products": 89,
    "low_stock_items": 5
  }
}
```

---

## 📱 Firebase Cloud Messaging

### Setup
1. Place `firebase-credentials.json` in root directory
2. Configure in `.env`:
```env
FIREBASE_CREDENTIALS=firebase-credentials.json
```

### Automatic Token Management
- Tokens are automatically saved on register/login
- Tokens are removed on logout
- Multiple devices per user supported

### Send Notification (Admin)
```http
POST /api/admin/notifications/send-to-all

{
  "title": "عرض جديد!",
  "body": "خصم 50% على جميع المنتجات",
  "data": {
    "type": "promotion",
    "category_id": 5
  }
}
```

---

## 🎨 Color Customization

Admin can customize 7 colors:
- **Primary Color** - Main brand color
- **Secondary Color** - Accent color
- **Background Color** - App background
- **Text Color** - Text color
- **Success Color** - Success messages (green)
- **Error Color** - Error messages (red)
- **Warning Color** - Warning messages (orange)

```http
PUT /api/admin/settings/primary_color
{
  "value": "#FF6B6B"
}
```

---

## 📊 Statistics & Analytics

### Available Statistics
- Total revenue
- Monthly revenue (last 12 months)
- Total orders by status
- Top selling items
- Top customers by spend
- Low stock alerts
- Recent orders

### Example
```http
GET /api/admin/dashboard/monthly-revenue

Response:
{
  "success": true,
  "data": [
    { "month": "2025-01", "revenue": 450000 },
    { "month": "2025-02", "revenue": 520000 },
    { "month": "2025-03", "revenue": 680000 }
  ]
}
```

---

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ API token authentication (Sanctum)
- ✅ Role-based access control
- ✅ Input validation on all endpoints
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection
- ✅ CSRF protection
- ✅ Rate limiting (configurable)

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

## 👨‍💻 Developer

**Ibrahm Code**
- GitHub: [@ibrahmcode](https://github.com/ibrahmcode)

---

## 🙏 Acknowledgments

- Laravel Framework
- Firebase Cloud Messaging
- Kurdistan Region - Iraq

---

## 📞 Support

For support, email: support@shopping.com

---

**Made with ❤️ in Kurdistan**

**سوپاس بۆ بەکارهێنانت! / شكراً لاستخدامك! / Thank you for using!** 🎉
