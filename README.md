# Karigar - Freelance Home Services Platform

Karigar is a web-based platform built using **HTML**, **CSS**, **JavaScript**, and **PHP**. It connects users with real freelance service providers (Karigars) for home services and allows users to browse building or repair materials easily.

## Technologies Used
- HTML5
- CSS3
- JavaScript
- PHP (Backend)
- XAMPP Server (Apache & MySQL)

## Features
- User Signup & Login
- Dashboard for managing services and materials
- Material listings
- Freelancer profiles with ratings
- Profile management
- Booking system

##  User Flow
1. **Landing Page (`index.html`)**
   - Navigate through Services, Materials, About.
   - Login via modal popup.

2. **Login/Signup**
   - Backend validation using PHP.
   - Users are redirected to their dashboard upon login.

3. **User Dashboard**
   - View and update profile.
   - View services and material bookings.

4. **Booking System**
   - Book freelance service providers directly.
   - Browse through construction materials.

##   Project Structure

```
Karigar/
│
├── index.html               # Homepage
├── about.html               # About section
├── services.html            # List of services
├── material.html            # Material listings
├── booking.php              # Booking form/logic
│
├── dashboards/              # User-related dashboards
│   ├── user-dashboard.php
│   ├── profile.php
│   ├── update-profile.php
│   ├── services-dashboard.php
│   └── material-dashboard.php
│
├── images/                  # Static images
└── style/                   # CSS files
```



## Note
- Make sure database connection credentials (if any) are updated properly in your PHP files.
- Database SQL file is not included here. Ensure to create one or import if available.

##  Authors
Developed by :
Anam Khan,Zara Naeem ,Hafsa Kokab


