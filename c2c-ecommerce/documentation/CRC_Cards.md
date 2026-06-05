# Class Responsibility Collaborator (CRC) Cards - NB Connect Platform

## User Class
**Class Name:** User

**Responsibilities:**
- Register new user account
- Login authentication
- Update profile information
- Manage user roles (customer, seller, admin)
- Track user status (active, inactive, suspended)

**Collaborators:**
- Database (stores user data)
- Product (seller relationship)
- Order (buyer relationship)
- Cart (shopping cart items)
- Message (communication)

---

## Product Class
**Class Name:** Product

**Responsibilities:**
- Create product listings
- Update product information
- Delete product listings
- Manage product status (available, sold, reserved, inactive)
- Track product inventory
- Display product details

**Collaborators:**
- User (seller relationship)
- Category (product categorization)
- Order (order items)
- Cart (shopping cart)
- Review (product reviews)

---

## Order Class
**Class Name:** Order

**Responsibilities:**
- Create new orders
- Update order status (pending, confirmed, shipped, delivered, cancelled)
- Track order details
- Calculate order totals
- Manage shipping information

**Collaborators:**
- User (buyer and seller)
- Product (order items)
- OrderItem (individual items in order)
- Database (order storage)

---

## Cart Class
**Class Name:** Cart

**Responsibilities:**
- Add items to cart
- Remove items from cart
- Update item quantities
- Calculate cart total
- Clear cart after checkout

**Collaborators:**
- User (cart owner)
- Product (cart items)
- Database (cart storage)

---

## Category Class
**Class Name:** Category

**Responsibilities:**
- Create product categories
- Update category information
- Delete categories
- Organize products by category
- Support hierarchical categories

**Collaborators:**
- Product (categorized items)
- Database (category storage)

---

## Message Class
**Class Name:** Message

**Responsibilities:**
- Send messages between users
- Receive messages
- Mark messages as read/unread
- Track message history

**Collaborators:**
- User (sender and receiver)
- Product (product-related messages)
- Database (message storage)

---

## Review Class
**Class Name:** Review

**Responsibilities:**
- Create product reviews
- Update review information
- Delete reviews
- Calculate average ratings
- Display review details

**Collaborators:**
- User (review author)
- Product (reviewed item)
- Database (review storage)

---

## Admin Class
**Class Name:** Admin

**Responsibilities:**
- Manage user accounts (CRUD operations)
- Manage product listings (approve/reject)
- Manage orders (update status)
- Generate reports and analytics
- Manage categories

**Collaborators:**
- User (user management)
- Product (product management)
- Order (order management)
- Category (category management)
- Database (data storage)

---

## OrderItem Class
**Class Name:** OrderItem

**Responsibilities:**
- Track individual items in orders
- Store quantity and price
- Link to product and order

**Collaborators:**
- Order (parent order)
- Product (product reference)
- Database (order item storage)
