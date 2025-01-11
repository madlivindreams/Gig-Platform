# Gig Platform

Gig Platform is a simple bidding platform created specifically for the music industry. It allows users to register, create offers, browse offers from other users, and communicate through a messaging system. The project is scalable and ready for deployment.

## Installation Requirements

- **XAMPP**: Used as a local server (Apache, PHP, and MySQL).
- **PHP**: For the backend logic of the application.
- **MySQL**: For storing user data, offers, and messages.
- **Web browser**: For testing and using the application.

## Installation Instructions

1. **Download XAMPP:**
   - Download and install XAMPP from [the official site](https://www.apachefriends.org/index.html).
   - During installation, select Apache and MySQL.

2. **Copy the project to the `htdocs` folder:**
   - Copy the entire project folder (e.g., `gig_platform`) to the `xampp/htdocs` folder.

3. **Set up the database:**
   - Start XAMPP and activate Apache and MySQL.
   - Open [phpMyAdmin](http://localhost/phpmyadmin).
   - Create a new database named `gig_platform`.
   - Import the `gig_platform.sql` file (which is part of the project) into this database.

4. **Configure the config file:**
   - Open the `config.php` file in the root folder of the project.
   - Modify the database login credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'gig_platform');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

## Running the Project

1. Start XAMPP and turn on Apache and MySQL.
2. In your browser, go to http://localhost/gig_platform/public.
3. You can now start using the application!

## Technologies Used

- **PHP:** Main backend programming language.
- **MySQL:** Database system for storing data.
- **HTML/CSS:** Frontend design.
- **JavaScript:** Interactivity and dynamic elements.
- **XAMPP:** Local server for testing the application.

## Authors and Contributors

- **Main Author:** Matus Durica (madlivindreams)
- **Contributions:** Contributions are welcome! If you have suggestions or modifications, please submit a `pull request` or contact the author.

---

If you have any questions or need assistance, feel free to contact the author through GitHub or email.
