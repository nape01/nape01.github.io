# Data Flow Diagram (DFD) - NB Connect Platform

## Level 0 DFD (Context Diagram)

```
┌──────────────┐
│   Customer   │
└──────┬───────┘
       │
       │ 1.0 Browse/Search Products
       │ 2.0 Register/Login
       │ 3.0 Add to Cart
       │ 4.0 Place Order
       │ 5.0 Send Message
       │ 6.0 Write Review
       │
       ▼
┌─────────────────────────────────┐
│    C2C E-Commerce System       │
│                                 │
│  ┌─────────────────────────┐    │
│  │   Process 1.0: User     │    │
│  │   Authentication        │    │
│  └─────────────────────────┘    │
│  ┌─────────────────────────┐    │
│  │   Process 2.0: Product │    │
│  │   Management           │    │
│  └─────────────────────────┘    │
│  ┌─────────────────────────┐    │
│  │   Process 3.0: Order    │    │
│  │   Processing            │    │
│  └─────────────────────────┘    │
│  ┌─────────────────────────┐    │
│  │   Process 4.0: Cart     │    │
│  │   Management            │    │
│  └─────────────────────────┘    │
│  ┌─────────────────────────┐    │
│  │   Process 5.0: Message  │    │
│  │   System                │    │
│  └─────────────────────────┘    │
│  ┌─────────────────────────┐    │
│  │   Process 6.0: Review   │    │
│  │   System                │    │
│  └─────────────────────────┘    │
└─────────────────────────────────┘
       │
       │
       ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

## Level 1 DFD

### Process 1.0: User Authentication
```
┌──────────────┐
│   Customer   │
└──────┬───────┘
       │
       │ 1.1 Register
       │ 1.2 Login
       │ 1.3 Update Profile
       │
       ▼
┌─────────────────────────┐
│  Process 1.0: User      │
│  Authentication         │
└────────┬────────────────┘
         │
         │ 1.4 Validate User
         │ 1.5 Store User Data
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

### Process 2.0: Product Management
```
┌──────────────┐     ┌──────────────┐
│   Customer   │     │    Seller    │
└──────┬───────┘     └──────┬───────┘
       │                     │
       │ 2.1 Browse Products │ 2.3 List Product
       │ 2.2 Search Products│ 2.4 Update Product
       │                     │ 2.5 Delete Product
       ▼                     ▼
┌─────────────────────────────────┐
│  Process 2.0: Product          │
│  Management                     │
└────────┬────────────────────────┘
         │
         │ 2.6 Retrieve Products
         │ 2.7 Store Product Data
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

### Process 3.0: Order Processing
```
┌──────────────┐     ┌──────────────┐
│   Customer   │     │    Seller    │
└──────┬───────┘     └──────┬───────┘
       │                     │
       │ 3.1 Place Order      │ 3.4 Update Order Status
       │ 3.2 View Orders     │ 3.5 View Sales
       │ 3.3 Track Order     │
       ▼                     ▼
┌─────────────────────────────────┐
│  Process 3.0: Order            │
│  Processing                     │
└────────┬────────────────────────┘
         │
         │ 3.6 Create Order
         │ 3.7 Update Order
         │ 3.8 Retrieve Orders
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

### Process 4.0: Cart Management
```
┌──────────────┐
│   Customer   │
└──────┬───────┘
       │
       │ 4.1 Add to Cart
       │ 4.2 Remove from Cart
       │ 4.3 Update Quantity
       │ 4.4 View Cart
       │ 4.5 Clear Cart
       │
       ▼
┌─────────────────────────┐
│  Process 4.0: Cart      │
│  Management             │
└────────┬────────────────┘
         │
         │ 4.6 Update Cart Data
         │ 4.7 Retrieve Cart
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

### Process 5.0: Message System
```
┌──────────────┐     ┌──────────────┐
│   Customer   │     │    Seller    │
└──────┬───────┘     └──────┬───────┘
       │                     │
       │ 5.1 Send Message    │ 5.3 Send Message
       │ 5.2 View Messages   │ 5.4 View Messages
       ▼                     ▼
┌─────────────────────────────────┐
│  Process 5.0: Message          │
│  System                         │
└────────┬────────────────────────┘
         │
         │ 5.5 Store Message
         │ 5.6 Retrieve Messages
         │ 5.7 Mark as Read
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

### Process 6.0: Review System
```
┌──────────────┐
│   Customer   │
└──────┬───────┘
       │
       │ 6.1 Write Review
       │ 6.2 View Reviews
       │
       ▼
┌─────────────────────────┐
│  Process 6.0: Review    │
│  System                 │
└────────┬────────────────┘
         │
         │ 6.3 Store Review
         │ 6.4 Retrieve Reviews
         │ 6.5 Calculate Rating
         │
         ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

## Data Store Descriptions

### D1: User Database
- **Description:** Stores all user account information
- **Data Elements:** user_id, username, email, password, full_name, phone, address, role, status, created_at, updated_at
- **Accessed by:** Process 1.0, Process 2.0, Process 3.0, Process 4.0, Process 5.0, Process 6.0

### D2: Product Database
- **Description:** Stores all product listings
- **Data Elements:** product_id, seller_id, category_id, title, description, price, quantity, image_url, condition_status, status, created_at, updated_at
- **Accessed by:** Process 2.0, Process 3.0, Process 4.0, Process 6.0

### D3: Order Database
- **Description:** Stores all order information
- **Data Elements:** order_id, buyer_id, seller_id, total_amount, status, shipping_address, created_at, updated_at
- **Accessed by:** Process 3.0

### D4: Cart Database
- **Description:** Stores shopping cart items
- **Data Elements:** cart_id, user_id, product_id, quantity, created_at
- **Accessed by:** Process 4.0

### D5: Message Database
- **Description:** Stores user messages
- **Data Elements:** message_id, sender_id, receiver_id, product_id, subject, message, is_read, created_at
- **Accessed by:** Process 5.0

### D6: Review Database
- **Description:** Stores product reviews
- **Data Elements:** review_id, product_id, user_id, rating, comment, created_at
- **Accessed by:** Process 6.0

## Data Flow Descriptions

### Customer → System
- **Registration Data:** username, email, password, full_name, phone, address
- **Login Credentials:** username/email, password
- **Search Query:** search terms, category filters, price range, condition
- **Cart Operations:** product_id, quantity
- **Order Data:** shipping_address, payment information
- **Message Data:** receiver_id, subject, message, product_id
- **Review Data:** product_id, rating, comment

### Seller → System
- **Product Data:** title, description, price, quantity, category_id, condition_status, image_url
- **Order Status Updates:** order_id, new_status
- **Message Data:** receiver_id, subject, message

### Admin → System
- **User Management Commands:** user_id, action (create, update, delete, activate, suspend)
- **Product Management Commands:** product_id, action (approve, reject, delete)
- **Order Management Commands:** order_id, status update
- **Category Management Commands:** category_id, category_name, description, action (create, delete)
- **Report Requests:** report type, date range

### System → Database
- **User Storage:** INSERT, UPDATE, DELETE, SELECT on users table
- **Product Storage:** INSERT, UPDATE, DELETE, SELECT on products table
- **Order Storage:** INSERT, UPDATE, DELETE, SELECT on orders table
- **Cart Storage:** INSERT, UPDATE, DELETE, SELECT on cart table
- **Message Storage:** INSERT, UPDATE, DELETE, SELECT on messages table
- **Review Storage:** INSERT, UPDATE, DELETE, SELECT on reviews table

### System → External Entities
- **Product Listings:** product details, images, prices
- **Order Confirmations:** order_id, total_amount, estimated delivery
- **Messages:** message content, sender information
- **Notifications:** order updates, new messages
- **Reports:** sales data, user statistics, product analytics
