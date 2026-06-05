# Use Case Diagram

## System: NB Connect Platform

```
┌──────────────┐
│   Customer   │
└──────┬───────┘
       │
       │ ┌──────────────────────────────────────────────┐
       │ │ Use Cases for Customer                       │
       │ │                                               │
       │ │ 1. Register Account                           │
       │ │ 2. Login                                     │
       │ │ 3. Browse Products                            │
       │ │ 4. Search Products                           │
       │ │ 5. View Product Details                      │
       │ │ 6. Add to Cart                               │
       │ │ 7. View Cart                                 │
       │ │ 8. Place Order                               │
       │ │ 9. Track Order Status                        │
       │ │ 10. Send Message to Seller                   │
       │ │ 11. View Messages                            │
       │ │ 12. Write Product Review                     │
       │ │ 13. View Reviews                             │
       │ │ 14. Update Profile                           │
       │ │ 15. Logout                                   │
       │ └──────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────┐
│                   NB Connect System                        │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Register Account            │  │
│  │              Precondition: None                    │  │
│  │              Postcondition: User account created   │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Login                       │  │
│  │              Precondition: User account exists      │  │
│  │              Postcondition: User authenticated     │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Browse Products             │  │
│  │              Precondition: None                    │  │
│  │              Postcondition: Products displayed     │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Search Products             │  │
│  │              Precondition: None                    │  │
│  │              Postcondition: Search results shown   │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Add to Cart                 │  │
│  │              Precondition: User logged in          │  │
│  │              Postcondition: Item added to cart     │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Use Case: Place Order                 │  │
│  │              Precondition: User logged in,         │  │
│  │                           Cart not empty           │  │
│  │              Postcondition: Order created          │  │
│  └───────────────────────────────────────────────────┘  │
│                                                           │
└─────────────────────────────────────────────────────────┘
       ▲
       │
       │ ┌──────────────────────────────────────────────┐
       │ │ Use Cases for Seller                         │
       │ │                                               │
       │ │ 1. Register Account                           │
       │ │ 2. Login                                     │
       │ │ 3. List Product                              │
       │ │ 4. Manage Products                           │
       │ │ 5. Update Product Information                │
       │ │ 6. Delete Product                            │
       │ │ 7. View Sales Reports                        │
       │ │ 8. Receive Messages                           │
       │ │ 9. Send Messages                             │
       │ │ 10. Update Order Status                      │
       │ │ 11. View Orders                              │
       │ │ 12. Update Profile                           │
       │ │ 13. Logout                                   │
       │ └──────────────────────────────────────────────┘
       │
┌──────┴───────┐
│    Seller    │
└──────────────┘

       ▲
       │
       │ ┌──────────────────────────────────────────────┐
       │ │ Use Cases for Administrator                  │
       │ │                                               │
       │ │ 1. Login                                     │
       │ │ 2. Manage User Accounts                      │
       │ │ 3. Create User Account                       │
       │ │ 4. Update User Account                       │
       │ │ 5. Delete User Account                       │
       │ │ 6. Suspend User Account                      │
       │ │ 7. Activate User Account                     │
       │ │ 8. Manage Product Listings                   │
       │ │ 9. Approve Product                           │
       │ │ 10. Reject Product                           │
       │ │ 11. Delete Product                           │
       │ │ 12. Manage Orders                            │
       │ │ 13. Update Order Status                      │
       │ │ 14. Manage Categories                        │
       │ │ 15. Add Category                             │
       │ │ 16. Delete Category                          │
       │ │ 17. Generate Reports                         │
       │ │ 18. View Analytics                           │
       │ │ 19. Logout                                   │
       │ └──────────────────────────────────────────────┘
       │
┌──────┴───────┐
│  Admin       │
└──────────────┘
```

## Use Case Descriptions

### Customer Use Cases

#### UC1: Register Account
- **Actor:** Customer
- **Description:** New users can register an account to access platform features
- **Preconditions:** None
- **Main Flow:**
  1. Customer navigates to registration page
  2. Customer enters username, email, password, full name, phone, address
  3. System validates input data
  4. System creates user account
  5. System redirects to login page
- **Postconditions:** User account created successfully
- **Alternative Flows:**
  - If username/email already exists, display error message
  - If validation fails, display error message

#### UC2: Login
- **Actor:** Customer, Seller, Admin
- **Description:** Users can login to access their accounts
- **Preconditions:** User account exists and is active
- **Main Flow:**
  1. User navigates to login page
  2. User enters username/email and password
  3. System validates credentials
  4. System creates session
  5. System redirects to appropriate dashboard
- **Postconditions:** User authenticated and logged in
- **Alternative Flows:**
  - If credentials invalid, display error message
  - If account suspended, display error message

#### UC3: Browse Products
- **Actor:** Customer
- **Description:** Customers can browse available products on the platform
- **Preconditions:** None
- **Main Flow:**
  1. Customer navigates to products page
  2. System displays available products
  3. Customer can filter by category, price, condition
  4. Customer can view product details
- **Postconditions:** Products displayed to customer

#### UC4: Search Products
- **Actor:** Customer
- **Description:** Customers can search for specific products
- **Preconditions:** None
- **Main Flow:**
  1. Customer enters search terms
  2. System searches database
  3. System displays matching products
- **Postconditions:** Search results displayed

#### UC5: Add to Cart
- **Actor:** Customer
- **Description:** Customers can add products to their shopping cart
- **Preconditions:** Customer logged in, product available
- **Main Flow:**
  1. Customer views product details
  2. Customer clicks "Add to Cart"
  3. System validates product availability
  4. System adds item to cart
  5. System updates cart count
- **Postconditions:** Item added to cart

#### UC6: Place Order
- **Actor:** Customer
- **Description:** Customers can place orders for items in their cart
- **Preconditions:** Customer logged in, cart not empty
- **Main Flow:**
  1. Customer views cart
  2. Customer enters shipping address
  3. Customer confirms order
  4. System creates order(s)
  5. System clears cart
  6. System displays order confirmation
- **Postconditions:** Order created and processed

#### UC7: Send Message to Seller
- **Actor:** Customer
- **Description:** Customers can send messages to sellers about products
- **Preconditions:** Customer logged in
- **Main Flow:**
  1. Customer clicks "Contact Seller"
  2. Customer enters message
  3. System sends message to seller
- **Postconditions:** Message sent successfully

#### UC8: Write Product Review
- **Actor:** Customer
- **Description:** Customers can write reviews for purchased products
- **Preconditions:** Customer logged in
- **Main Flow:**
  1. Customer navigates to product page
  2. Customer enters rating and comment
  3. System submits review
- **Postconditions:** Review submitted

### Seller Use Cases

#### UC9: List Product
- **Actor:** Seller
- **Description:** Sellers can list new products for sale
- **Preconditions:** Seller logged in
- **Main Flow:**
  1. Seller navigates to "Sell Item" page
  2. Seller enters product details
  3. Seller uploads product image
  4. System validates data
  5. System creates product listing
- **Postconditions:** Product listed successfully

#### UC10: Update Order Status
- **Actor:** Seller
- **Description:** Sellers can update order status for their sales
- **Preconditions:** Seller logged in, order exists
- **Main Flow:**
  1. Seller views order details
  2. Seller selects new status
  3. System updates order status
- **Postconditions:** Order status updated

#### UC11: View Sales Reports
- **Actor:** Seller
- **Description:** Sellers can view their sales performance
- **Preconditions:** Seller logged in
- **Main Flow:**
  1. Seller navigates to sales reports
  2. System displays sales data
- **Postconditions:** Sales reports displayed

### Administrator Use Cases

#### UC12: Manage User Accounts
- **Actor:** Administrator
- **Description:** Admins can manage all user accounts
- **Preconditions:** Admin logged in
- **Main Flow:**
  1. Admin navigates to user management
  2. Admin views all users
  3. Admin can create, update, delete, suspend, activate users
- **Postconditions:** User accounts managed

#### UC13: Manage Product Listings
- **Actor:** Administrator
- **Description:** Admins can manage all product listings
- **Preconditions:** Admin logged in
- **Main Flow:**
  1. Admin navigates to product management
  2. Admin views all products
  3. Admin can approve, reject, delete products
- **Postconditions:** Product listings managed

#### UC14: Generate Reports
- **Actor:** Administrator
- **Description:** Admins can generate system reports
- **Preconditions:** Admin logged in
- **Main Flow:**
  1. Admin navigates to reports section
  2. Admin selects report type
  3. System generates report
- **Postconditions:** Report generated and displayed

## Relationships Between Use Cases

### Include Relationships
- **Place Order** includes **Validate Cart**
- **Register Account** includes **Validate User Input**
- **List Product** includes **Validate Product Data**

### Extend Relationships
- **Login** extends **Forgot Password** (optional)
- **Place Order** extends **Apply Coupon** (optional)
- **View Products** extends **Filter Products** (optional)

### Generalization Relationships
- **User** is a generalization of **Customer**, **Seller**, and **Administrator**
- **Manage Account** is a generalization of **Update Profile**, **Change Password**

## Actor Descriptions

### Customer
- **Description:** Individual users who browse and purchase products from sellers
- **Goals:** Find desired products, make secure purchases, communicate with sellers, leave reviews
- **Characteristics:** Can browse without login, must login to purchase

### Seller
- **Description:** Individual users who list products for sale on the platform
- **Goals:** List products, manage inventory, process orders, communicate with buyers, track sales
- **Characteristics:** Can also act as customers

### Administrator
- **Description:** System administrators who manage the platform
- **Goals:** Manage users, moderate content, generate reports, ensure system integrity
- **Characteristics:** Has full system access, can perform all administrative functions
