# POS System

A web-based Point of Sale (POS) system built with PHP and MySQL.

## Structure

- `api/`: API endpoints
- `controllers/`: Logic controllers
- `includes/`: Configuration and helper functions
- `models/`: Database models
- `views/`: Frontend views
- `database.sql`: Database schema import file

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx) - XAMPP/WAMP recommended for local development

## Installation

1.  **Clone the repository** to your web server's root directory (e.g., `htdocs` in XAMPP).
    ```bash
    git clone <repository-url> pos
    ```

2.  **Import the Database**:
    -   Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
    -   Create a new database named `pos_db` (or whatever you prefer).
    -   Import the `database.sql` file located in the root of the project.

3.  **Configuration**:
    -   The project uses a `.env` file for database configuration.
    -   Copy the example configuration file:
        ```bash
        cp .env.example .env
        ```
        (Or manually create `.env` and copy the contents of `.env.example` into it).
    -   Open `.env` and update the values to match your database setup.

    **How to use `.env` file:**
    The `.env` file contains key-value pairs for environment variables. Lines starting with `#` are comments.

    ```ini
    DB_HOST=localhost   # Database host (usually localhost)
    DB_USER=root        # Database username (default 'root' for XAMPP)
    DB_PASS=            # Database password (default empty for XAMPP)
    DB_NAME=pos_db      # The name of the database you created
    APP_URL=http://localhost/pos
    GEMINI_API_KEY=YOUR_KEY_HERE
    ```

4.  **Run the Application**:
    -   Open your browser and navigate to `http://localhost/pos`.

## Troubleshooting

-   **Database Connection Failed**:
    -   Ensure your MySQL server is running.
    -   Check if the credentials in `.env` are correct.
    -   Make sure the database name in `.env` matches the one you created in phpMyAdmin.

-   **404 Not Found**:
    -   Ensure the project is in the correct directory (`htdocs` for XAMPP).
    -   Check file permissions.
