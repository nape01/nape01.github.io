# Enhanced Entity Relationship Diagram (EERD) - NB Connect Platform

## Entity Relationships

### Users Table
- **Primary Key:** user_id (INT, AUTO_INCREMENT)
- **Attributes:**
  - username (VARCHAR(50), UNIQUE, NOT NULL)
  - email (VARCHAR(100), UNIQUE, NOT NULL)
  - password (VARCHAR(255), NOT NULL)
  - full_name (VARCHAR(100), NOT NULL)
  - phone (VARCHAR(20))
  - address (TEXT)
  - role (ENUM: 'customer', 'seller', 'admin', DEFAULT 'customer')
  - status (ENUM: 'active', 'inactive', 'suspended', DEFAULT 'active')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
  - updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Relationships:**
- One-to-Many with Products (as seller)
- One-to-Many with Orders (as buyer)
- One-to-Many with Orders (as seller)
- One-to-Many with Cart
- One-to-Many with Messages (as sender)
- One-to-Many with Messages (as receiver)
- One-to-Many with Reviews

---

### Categories Table
- **Primary Key:** category_id (INT, AUTO_INCREMENT)
- **Attributes:**
  - category_name (VARCHAR(100), NOT NULL)
  - description (TEXT)
  - parent_id (INT, NULL, SELF-REFERENCE to category_id)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Self-referencing (parent-child categories)
- One-to-Many with Products

---

### Products Table
- **Primary Key:** product_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - seller_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
  - category_id (INT, REFERENCES categories(category_id), ON DELETE CASCADE)
- **Attributes:**
  - title (VARCHAR(200), NOT NULL)
  - description (TEXT)
  - price (DECIMAL(10,2), NOT NULL)
  - quantity (INT, DEFAULT 1)
  - image_url (VARCHAR(500))
  - condition_status (ENUM: 'new', 'used', 'refurbished', DEFAULT 'used')
  - status (ENUM: 'available', 'sold', 'reserved', 'inactive', DEFAULT 'available')
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
  - updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Relationships:**
- Many-to-One with Users (seller)
- Many-to-One with Categories
- One-to-Many with OrderItems
- One-to-Many with Cart
- One-to-Many with Reviews

---

### Orders Table
- **Primary Key:** order_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - buyer_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
  - seller_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
- **Attributes:**
  - total_amount (DECIMAL(10,2), NOT NULL)
  - status (ENUM: 'pending', 'confirmed', 'shipped', 'delivered', 'cancelled', DEFAULT 'pending')
  - shipping_address (TEXT, NOT NULL)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
  - updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Relationships:**
- Many-to-One with Users (buyer)
- Many-to-One with Users (seller)
- One-to-Many with OrderItems

---

### OrderItems Table
- **Primary Key:** order_item_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - order_id (INT, REFERENCES orders(order_id), ON DELETE CASCADE)
  - product_id (INT, REFERENCES products(product_id), ON DELETE CASCADE)
- **Attributes:**
  - quantity (INT, NOT NULL)
  - price (DECIMAL(10,2), NOT NULL)

**Relationships:**
- Many-to-One with Orders
- Many-to-One with Products

---

### Cart Table
- **Primary Key:** cart_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - user_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
  - product_id (INT, REFERENCES products(product_id), ON DELETE CASCADE)
- **Attributes:**
  - quantity (INT, DEFAULT 1)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- **Unique Constraint:** (user_id, product_id)

**Relationships:**
- Many-to-One with Users
- Many-to-One with Products

---

### Messages Table
- **Primary Key:** message_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - sender_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
  - receiver_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
  - product_id (INT, REFERENCES products(product_id), ON DELETE SET NULL)
- **Attributes:**
  - subject (VARCHAR(200))
  - message (TEXT, NOT NULL)
  - is_read (BOOLEAN, DEFAULT FALSE)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Many-to-One with Users (sender)
- Many-to-One with Users (receiver)
- Many-to-One with Products (optional)

---

### Reviews Table
- **Primary Key:** review_id (INT, AUTO_INCREMENT)
- **Foreign Keys:**
  - product_id (INT, REFERENCES products(product_id), ON DELETE CASCADE)
  - user_id (INT, REFERENCES users(user_id), ON DELETE CASCADE)
- **Attributes:**
  - rating (INT, CHECK: rating >= 1 AND rating <= 5, NOT NULL)
  - comment (TEXT)
  - created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Many-to-One with Products
- Many-to-One with Users

---

## Relationship Summary

| Entity | Relationship | Related Entity | Cardinality |
|--------|-------------|----------------|-------------|
| Users | sells | Products | 1:N |
| Users | buys (as buyer) | Orders | 1:N |
| Users | sells (as seller) | Orders | 1:N |
| Users | has | Cart | 1:N |
| Users | sends | Messages | 1:N |
| Users | receives | Messages | 1:N |
| Users | writes | Reviews | 1:N |
| Categories | contains | Products | 1:N |
| Categories | parent of | Categories | 1:N (self) |
| Products | belongs to | Users (seller) | N:1 |
| Products | belongs to | Categories | N:1 |
| Products | has | OrderItems | 1:N |
| Products | has | Cart items | 1:N |
| Products | has | Reviews | 1:N |
| Orders | belongs to | Users (buyer) | N:1 |
| Orders | belongs to | Users (seller) | N:1 |
| Orders | contains | OrderItems | 1:N |
| OrderItems | belongs to | Orders | N:1 |
| OrderItems | belongs to | Products | N:1 |
| Cart | belongs to | Users | N:1 |
| Cart | belongs to | Products | N:1 |
| Messages | from | Users (sender) | N:1 |
| Messages | to | Users (receiver) | N:1 |
| Messages | about | Products | N:1 (optional) |
| Reviews | for | Products | N:1 |
| Reviews | by | Users | N:1 |
