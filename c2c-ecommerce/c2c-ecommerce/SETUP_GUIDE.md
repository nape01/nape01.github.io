# Quick Setup Guide - NB Connect

## Fastest Setup with XAMPP

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Run the installer
3. Install to default location: `C:\xampp`

### Step 2: Copy Project to XAMPP
1. Copy the entire `c2c-ecommerce` folder to: `C:\xampp\htdocs\`
2. Final path should be: `C:\xampp\htdocs\c2c-ecommerce`

### Step 3: Start XAMPP Services
1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to show green status

### Step 4: Set Up Database
1. Open browser and go to: http://localhost/phpmyadmin
2. Click "New" in left sidebar
3. Create database named: `c2c_ecommerce`
4. Click "Import" tab
5. Choose file: `C:\xampp\htdocs\c2c-ecommerce\config\schema.sql`
6. Click "Go" to import

### Step 5: Access the Website
Open browser and go to: **http://localhost/c2c-ecommerce**

## Default Admin Credentials
- **Username:** admin
- **Password:** password
- **Email:** admin@nbconnectjhb.com

## Troubleshooting

### Port 80 Already in Use
If Apache won't start:
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "Apache (httpd.conf)"
4. Find "Listen 80" and change to "Listen 8080"
5. Access website at: http://localhost:8080/c2c-ecommerce

### MySQL Won't Start
If MySQL won't start:
1. Open XAMPP Control Panel
2. Click "Shell" button
3. Type: `mysql_fix_privilege_tables`
4. Press Enter
5. Try starting MySQL again

### Database Connection Error
If you see "Connection failed":
1. Check `config/database.php` credentials match XAMPP defaults:
   - Host: localhost
   - User: root
   - Password: (leave empty)
   - Database: c2c_ecommerce

## For Online Hosting (Deliverable 2 Requirement)

### Using InfinityFree (Free)
1. Sign up at: https://infinityfree.net/
2. Create a new account
3. Create a new website
4. Use their online file manager to upload all files
5. Use their MySQL panel to import the database
6. Update `config/database.php` with their database credentials
7. Access via your provided subdomain

### Using Paid Hosting
1. Purchase hosting plan (Hostinger, Bluehost, etc.)
2. Upload files via FTP or file manager
3. Create MySQL database in hosting panel
4. Import schema.sql
5. Update database credentials in config/database.php
6. Access via your domain
