# Context Diagram

## System: NB Connect Platform

```
┌─────────────────────────────────────────────────────────────────┐
│                         NB Connect Platform                      │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Customer   │  │    Seller    │  │    Admin     │          │
│  │   Interface  │  │  Interface   │  │  Interface   │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                 │                 │                   │
│         │                 │                 │                   │
│  ┌──────▼─────────────────▼─────────────────▼───────┐          │
│  │                                                     │          │
│  │              NB Connect System                     │          │
│  │                                                     │          │
│  │  ┌─────────────┐  ┌─────────────┐  ┌───────────┐ │          │
│  │  │   User      │  │  Product    │  │   Order   │ │          │
│  │  │ Management  │  │ Management  │  │Management │ │          │
│  │  └─────────────┘  └─────────────┘  └───────────┘ │          │
│  │                                                     │          │
│  │  ┌─────────────┐  ┌─────────────┐  ┌───────────┐ │          │
│  │  │  Cart       │  │  Message    │  │  Review   │ │          │
│  │  │ Management  │  │ Management  │  │Management │ │          │
│  │  └─────────────┘  └─────────────┘  └───────────┘ │          │
│  │                                                     │          │
│  └─────────────────────────────────────────────────────┘          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
         │                             │
         │                             │
         ▼                             ▼
┌─────────────────┐           ┌─────────────────┐
│   MySQL Database│           │   File System   │
│                 │           │   (Images)      │
└─────────────────┘           └─────────────────┘
```

## External Entities

### 1. Customer
- **Description:** Individual users who browse and purchase products from sellers
- **Interactions:**
  - Register account
  - Login to system
  - Browse products
  - Search products
  - View product details
  - Add items to cart
  - Place orders
  - Track order status
  - Send messages to sellers
  - Write product reviews
  - Manage profile

### 2. Seller
- **Description:** Individual users who list products for sale on the platform
- **Interactions:**
  - Register account
  - Login to system
  - List new products
  - Manage product listings
  - Update product information
  - Delete products
  - View sales reports
  - Receive messages from buyers
  - Respond to messages
  - Manage orders
  - Update order status

### 3. Administrator
- **Description:** System administrators who manage the platform and oversee operations
- **Interactions:**
  - Login to admin panel
  - Manage user accounts (CRUD)
  - Manage product listings (approve/reject)
  - Manage orders
  - Manage categories
  - Generate reports and analytics
  - Monitor system performance
  - Handle user disputes
  - Suspend/activate users

## System Boundaries

### Internal Components
- User Management System
- Product Management System
- Order Management System
- Cart Management System
- Message Management System
- Review Management System
- Category Management System
- Authentication & Authorization System
- Search & Filtering System
- Reporting & Analytics System

### External Systems
- MySQL Database (data persistence)
- File System (image storage)
- Email System (notifications - optional)

## Data Flows

### Customer → System
- Registration data
- Login credentials
- Search queries
- Cart operations
- Order details
- Messages
- Reviews
- Profile updates

### Seller → System
- Product listings
- Product updates
- Order status updates
- Messages
- Profile updates

### Admin → System
- User management commands
- Product management commands
- Order management commands
- Category management commands
- Report generation requests

### System → External Entities
- Product listings
- Order confirmations
- Messages
- Notifications
- Reports
- Analytics data

### System → MySQL Database
- User data storage
- Product data storage
- Order data storage
- Transaction data storage
- Message data storage
- Review data storage

### System → File System
- Product image uploads
- Image retrieval
