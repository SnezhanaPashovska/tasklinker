TaskLinker is a Symfony-based web platform designed for managing and tracking projects within a company. It provides features for employees to view, create, and manage tasks, with role-based access control for different user types.

Features
🔐 Authentication & Authorization: Role-based access control (ROLE_USER, ROLE_ADMIN).
🛡️ Two-Factor Authentication: Google Authenticator for enhanced security.
📋 Project Management: Employees can view and manage projects assigned to them.
🔧 Admin Interface: Admin users can manage all projects and employees.
📱 Responsive UI: Mobile-friendly design for ease of use.
Tech Stack
Backend: PHP 8.1+, Symfony 6.x, Doctrine ORM
Database: PostgreSQL
Frontend: Twig (templating), Bootstrap 5 (styling)
Authentication: Symfony Security, Google Authenticator
Version Control: Git, GitHub
Requirements
PHP 8.1 or higher
Composer
PostgreSQL
Symfony CLI (optional)
Installation
1. Clone the repository
bash
Copy code
git clone https://github.com/SnezhanaPashovska/tasklinker
cd tasklinker
2. Install dependencies
bash
Copy code
composer install
3. Set up environment variables
Create a .env.local file in the root directory with the following configuration:

dotenv
Copy code
DATABASE_URL="pgsql://user:password@localhost:5432/tasklinker2"
Replace user, password, and localhost with your PostgreSQL credentials.

4. Set up the database
Run the migrations to set up the database schema:

bash
Copy code
php bin/console doctrine:migrations:migrate
5. (Optional) Load fixtures
To load sample data, run:

bash
Copy code
php bin/console doctrine:fixtures:load
6. Start the development server
Using Symfony CLI:
bash
Copy code
symfony server:start
Using PHP’s built-in server:
bash
Copy code
php -S 127.0.0.1:8000 -t public
Usage
Login: Use existing credentials to log in.
Two-Factor Authentication: After login, users with 2FA enabled will be prompted to enter a code from Google Authenticator.
Admin Features: Admins can create, edit, and archive projects, as well as manage employees.
Employee Features: Employees can view and manage their assigned projects.
Roles
ROLE_USER: Standard employee role, can view and manage assigned projects.
ROLE_ADMIN: Admin role, can manage all employees and projects.
Example Routes
Homepage (Projects Overview): /project
Employee Management (Admin): /employees
2FA Setup: /2fa

Acknowledgments
Symfony for the robust framework.
PHP for its versatility and power.
Google Authenticator for secure two-factor authentication.
