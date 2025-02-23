# event-booking-system
project description
This is a simple Event Booking System where users can browse events, register for them, and cancel bookings. Admins can manage events (create, edit, delete) and view bookings.

# Features
✅User Features
 User Registration & Login (passwords are securely hashed)
 View available events
 Book an event (reducing available seats)
 Cancel booking (increasing available seats)
 Responsive UI with AJAX-based booking (no page refresh)
✅ Admin Features
 Secure Admin Login
 Add, Edit, and Delete Events
 View all bookings
 Manage event capacity
 # Tech Stack
Frontend: HTML, CSS, JavaScript
 Backend: PHP, MySQL
 Database: MySQL
 Other: AJAX (for seamless event booking), Sessions (for authentication)

 ## Setup Instructions
 1.clone the Repository
 2.Setup the Database
     Open phpMyAdmin (http://localhost/phpmyadmin/).
     Click on Import and select the event_booking.sql file.
     Click Go to create the database and tables.
 3.Configure Database Connection
 4.Start the Local Server
     Run XAMPP (or any local server) and start Apache & MySQL.
    Open your browser and go to: http://localhost/event_booking/

## Security Features
Password Hashing using password_hash() for security
SQL Injection Prevention with prepared statements
Session Handling for authentication
  
