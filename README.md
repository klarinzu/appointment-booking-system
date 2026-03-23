A versatile and customizable appointment booking system designed for astrologers, doctors, consultants, Salons, Spas, Lawyers, Tutors, Career Coaches, Personal Trainers, Nutritionists, Home services, Plumbers, Electricians, Automotive and other professionals. Features include automated email notifications, multi-user roles, calendar-based scheduling, availability management, and holiday settings.

#### Features:

✅ Multi-role support (Admin,  Employee/Professional , Moderator, Subscriber)

✅ Automated Email Notifications for bookings & reminders

✅ Interactive Calendar View for easy scheduling

✅ Multi-Slot Availability (Multiple time slots per day e.g., 9 AM–12 PM + 3 PM–6 PM).

✅ Mark Holidays & Unavailable Dates

✅ Easy Rescheduling & Cancellation for professionals & clients

✅ Responsive Design (Works on desktop & mobile)

## Installation

1. Clone the repository:

```php
git clone https://github.com/vfixtechnology/appointment-booking-system.git
```
```php
cd appointment-booking-system
```
Install Dependencies:
```php
composer install
```
Setting Up Environment File
##### Rename .env.example to .env in the main directory. This file holds your app’s environment settings like database and API keys.

Generate Key for project
```php
php artisan key:generate
```

2. Set up the database:
 - Create a MySQL database.
 - Update .env file with your database credentials:
 ```php
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
 ```

3. Configure SMTP for email notifications:
Add your email service (e.g., Mailtrap, Gmail) details in .env:
 ```php
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=your_smtp_port
MAIL_USERNAME=your_email_username
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@email.com
 ```

4. Start the Docker containers:
 ```php
docker compose up -d student-dashboard db pma
 ```

5. Run migrations & seed dummy data inside the student dashboard container:
```php
docker compose exec student-dashboard php artisan migrate
docker compose exec student-dashboard php artisan db:seed
```

6. Start the queue listener and frontend assets:
```php
php artisan queue:listen
npm run dev
```

Now, open http://127.0.0.1:8000 in your browser to access the student dashboard from Docker.

## Faster WSL setup

If the browser feels slow while the project lives on `C:\`, run it from the Ubuntu WSL filesystem instead. This is usually much faster than serving a Docker bind mount from Windows.

From PowerShell in the project root:

```powershell
.\scripts\run-in-wsl.ps1
```

That command will:

```text
1. Sync this project into Ubuntu WSL at ~/projects/appointment-booking-system
2. Keep the working copy inside the Linux filesystem
3. Start docker compose from Ubuntu WSL
4. Run migrations automatically
```

Optional flags:

```powershell
.\scripts\run-in-wsl.ps1 -InstallNode
.\scripts\run-in-wsl.ps1 -SkipInstall
.\scripts\run-in-wsl.ps1 -SkipMigrate
```

After the first sync, future starts are faster directly from Ubuntu:

```bash
cd ~/projects/appointment-booking-system
docker compose up -d student-dashboard db pma
```

## Windows auto-start

To start the Ubuntu WSL stack automatically every time you sign in to Windows:

```powershell
.\scripts\install-autostart.ps1
```

This installs a hidden launcher in your Windows Startup folder. The launcher:

```text
1. Starts Docker Desktop if it is not already running
2. Waits for Docker to become ready
3. Syncs the project into Ubuntu WSL
4. Starts the student-dashboard, db, and pma containers
```

Logs are written to:

```text
storage/logs/windows-wsl-autostart.log
```

To remove the auto-start task later:

```powershell
.\scripts\remove-autostart.ps1
```

## Troubleshooting

If Laravel shows `Permission denied` for `storage` or `bootstrap/cache`, run this from PowerShell in the project root:

```powershell
.\scripts\fix-permissions.ps1
```

That shortcut repairs the writable Laravel directories inside the `student-dashboard` container and clears compiled Blade views afterward.

## Admin login credentials:
link: http://localhost:8000/login

user: admin@example.com

pass: admin123


## 📅 How to Use?
Create Account For Professionals (Doctors, Astrologers, or etc.)

✅ Set Availability: Define working hours & multiple slots per day.

✅ Block Holidays: Mark days as unavailable - only available while editing profile of professional.

✅ Manage Appointments: Approve, Confirmed, or cancel bookings.



## ✨ Key Features
### 🔐 Role-Based Access
##### Admin: Full system control (users, appointments, settings).

##### Moderator: Manage all appointments + employee-level access.

##### Employee/Professional:
✅ Set availability (multiple slots/day).

✅ Mark holidays/unavailable dates.

✅ View/manage their own appointments.


##### Subscriber (Client):
✅ Guest checkout is available. However, bookings can only be viewed after logging in with an account created at the time of booking.
