# NB Connect

A Customer-to-Customer (C2C) e-commerce platform built with PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap. This platform enables secure online transactions between individual buyers and sellers in South Africa.

## Features

### Customer Features
- User registration and authentication
- Browse and search products
- View product details
- Add items to shopping cart
- Place orders and track order status
- Send messages to sellers
- Write product reviews
- Manage profile

### Seller Features
- List products for sale
- Manage product listings
- View sales reports
- Communicate with buyers
- Update order status
- Track inventory

### Admin Features
- User management (CRUD operations)
- Product moderation (approve/reject)
- Order management
- Category management
- Generate reports and analytics
- Role-Based Access Control (RBAC)

## Technical Stack

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **Security:** Prepared statements, password hashing, session management

## Project Structure

```
c2c-ecommerce/
├── admin/                  # Admin panel
│   ├── auth_check.php     # Admin authentication
│   ├── header.php         # Admin header
│   ├── footer.php         # Admin footer
│   ├── index.php          # Admin dashboard
│   ├── users.php          # User management
│   ├── products.php       # Product management
│   ├── orders.php         # Order management
│   ├── categories.php     # Category management
│   └── reports.php        # Reports and analytics
├── api/                    # API endpoints
│   ├── add_to_cart.php    # Add item to cart
│   ├── cart_count.php     # Get cart count
│   ├── remove_from_cart.php # Remove item from cart
│   └── update_cart.php    # Update cart quantity
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css      # Custom styles
│   ├── js/
│   │   └── script.js      # Custom JavaScript
│   └── images/            # Product images
├── config/                 # Configuration files
│   ├── database.php       # Database connection
│   └── schema.sql         # Database schema
├── includes/               # Reusable components
│   ├── header.php         # Main header
│   └── footer.php         # Main footer
├── documentation/          # Design documentation
│   ├── CRC_Cards.md       # Class Responsibility Collaborator cards
│   ├── EERD.md            # Enhanced Entity Relationship Diagram
│   ├── Context_Diagram.md # Context Diagram
│   ├── DFD.md             # Data Flow Diagram
│   ├── Use_Case_Diagram.md # Use Case Diagram
│   ├── Introduction.md    # Project introduction
│   ├── Conclusion.md      # Project conclusion
│   └── Code_Samples.md    # Code samples with explanations
├── index.php              # Homepage
├── products.php           # Product listing
├── product_detail.php     # Product details
├── cart.php               # Shopping cart
├── checkout.php           # Checkout process
├── register.php           # User registration
├── login.php              # User login
├── logout.php             # User logout
├── sell.php               # Sell item form
├── my_products.php        # My products page
├── messages.php           # Messages page
├── profile.php            # User profile
└── orders.php             # My orders page
```

## Installation Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)

### Step 1: Set up the Database

1. Create a new MySQL database named `c2c_ecommerce`
2. Import the database schema:
   ```bash
   mysql -u root -p c2c_ecommerce < config/schema.sql
   ```
3. Update database credentials in `config/database.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'c2c_ecommerce');
   ```

### Step 2: Configure Web Server

#### Option A: Using PHP Built-in Server
```bash
cd C:\Users\boitu\CascadeProjects\c2c-ecommerce
php -S localhost:8000
```

#### Option B: Using Apache
1. Place the project in your web server's document root (e.g., `htdocs` or `www`)
2. Access via: `http://localhost/c2c-ecommerce`

#### Option C: Using Nginx
Configure Nginx to serve the project directory and set up PHP-FPM.

### Step 3: Create Required Directories

```bash
mkdir assets/images/products
```

### Step 4: Default Admin Account

The system creates a default admin account:
- **Username:** admin
- **Password:** password
- **Email:** admin@nbconnectjhb.com

**Important:** Change the default admin password after first login for security.

## Usage

### For Customers
1. Register a new account or login
2. Browse products by category or search
3. View product details
4. Add items to cart
5. Proceed to checkout
6. Track order status
7. Communicate with sellers
8. Write reviews

### For Sellers
1. Register as a seller (default role is customer, admin can upgrade)
2. List products for sale
3. Manage inventory
4. Process orders
5. View sales reports

### For Administrators
1. Login with admin credentials
2. Access admin panel at `/admin/`
3. Manage users, products, orders, and categories
4. Generate reports and analytics

## Screenshot Guidelines for Deliverable 2

### Required Screenshots

#### Main Website Screenshots
Capture the following pages on three devices (smartphone, tablet, desktop):

1. **Homepage (index.php)**
   - Smartphone: ~375px width
   - Tablet: ~768px width
   - Desktop: ~1920px width

2. **Products Page (products.php)**
   - Show product grid with filters
   - Smartphone, tablet, desktop views

3. **Product Detail Page (product_detail.php)**
   - Show product information and reviews
   - Smartphone, tablet, desktop views

4. **Cart Page (cart.php)**
   - Show shopping cart items
   - Smartphone, tablet, desktop views

5. **Checkout Page (checkout.php)**
   - Show checkout form and order summary
   - Smartphone, tablet, desktop views

6. **Registration Page (register.php)**
   - Show registration form
   - Smartphone, tablet, desktop views

7. **Login Page (login.php)**
   - Show login form
   - Smartphone, tablet, desktop views

#### Admin Website Screenshots
Capture the following pages on three devices:

1. **Admin Dashboard (admin/index.php)**
   - Show statistics and recent activity
   - Smartphone, tablet, desktop views

2. **User Management (admin/users.php)**
   - Show user table with actions
   - Smartphone, tablet, desktop views

3. **Product Management (admin/products.php)**
   - Show product table with moderation
   - Smartphone, tablet, desktop views

4. **Order Management (admin/orders.php)**
   - Show order table with status updates
   - Smartphone, tablet, desktop views

### How to Capture Screenshots

#### Method 1: Browser DevTools
1. Open Chrome DevTools (F12)
2. Click the device toolbar icon (Ctrl+Shift+M)
3. Select device preset (iPhone SE, iPad, Desktop)
4. Take screenshot (Ctrl+Shift+P → "Capture screenshot")

#### Method 2: Online Tools
- Use responsive design checker tools like responsivedesignchecker.com
- Enter your localhost URL and select different devices

#### Method 3: Browser Zoom
1. Resize browser window to desired width
2. Take screenshot using Snipping Tool (Windows) or equivalent

### MySQL Table Screenshots

Capture screenshots of the following tables from phpMyAdmin or MySQL Workbench:

1. **users table** - Show structure and sample data
2. **products table** - Show structure and sample data
3. **orders table** - Show structure and sample data
4. **categories table** - Show structure and sample data
5. **cart table** - Show structure and sample data
6. **messages table** - Show structure and sample data
7. **reviews table** - Show structure and sample data
8. **order_items table** - Show structure and sample data

## Security Features

- Password hashing using bcrypt (PASSWORD_DEFAULT)
- Prepared statements to prevent SQL injection
- Session-based authentication
- XSS protection with htmlspecialchars()
- CSRF protection (recommended for production)
- Input validation and sanitization

## Responsive Design

The platform is fully responsive and works on:
- Smartphones (320px - 480px)
- Tablets (481px - 768px)
- Desktops (769px+)

Bootstrap 5.3 grid system ensures proper layout across all devices.

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Future Enhancements

- Payment gateway integration
- Email notifications
- Real-time chat
- Advanced search with filters
- Product recommendations
- Rating system improvements
- Mobile app development

## Documentation

All design documentation is available in the `documentation/` folder:
- CRC Cards
- Enhanced Entity Relationship Diagram (EERD)
- Context Diagram
- Data Flow Diagram (DFD)
- Use Case Diagram
- Introduction
- Conclusion
- Code Samples

## License

This project is created for educational purposes for Deliverable 2 of the NB Connect E-Commerce Platform Development project.

## Support

For issues or questions, please refer to the project documentation or contact the development team.

## Acknowledgments

- Bootstrap Framework
- Bootstrap Icons
- PHP Documentation
- MySQL Documentation
