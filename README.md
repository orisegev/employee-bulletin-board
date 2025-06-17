# 🧾 Employee Bulletin Board

A simple internal message board system for municipal employees, built with PHP and MySQL using clean architecture principles, Docker-based development, and PHPUnit testing.

---

## 🚀 Features

- Submit and display messages with:
  - Publisher name
  - Email address
  - Message content
  - Publish date
- Email confirmation sent using **PHPMailer**
- Input validation on server side
- Clean file structure with fully separated concerns (services, database, templates, public)
- Dockerized environment for easy setup and deployment
- Basic responsive UI with HTML/CSS

---

## 🛠️ Tech Stack

- PHP 8.2+
- MySQL
- PHPMailer
- Composer (autoloading via PSR-4)
- PHPUnit
- HTML / CSS / JavaScript
- Docker & Docker Compose

---

## 🧱 Project Structure

```
├── src/                # Core business logic and services
│   └── Services/       # EmailService, MessageService
│   └── Core/           # Database abstraction
│   └── Factories/      # MailerFactory
│
├── tests/              # Unit tests using PHPUnit
│
├── public/             # Public-facing HTML, CSS, JS
│   ├── api/            # API endpoints (messages.php)
│   └── assets/         # Styles, JS, icons
│
├── EmailTemplates/     # HTML email templates
├── sql/                # Database schema (hod.sql)
├── .env.example        # Environment variables template
├── docker-compose.yml  # Docker environment
└── phpunit.xml         # PHPUnit configuration
```

---

## ⚙️ Installation (via Docker)

```bash
git clone https://github.com/orisegev/employee-bulletin-board.git
cd employee-bulletin-board

# Copy .env file and adjust config
cp .env.example .env

# Start the Docker containers
docker-compose up -d --build

# Access the app via http://localhost:8080
```

---

## 🧪 Run Tests

```bash
docker exec -it app php vendor/bin/phpunit
```

> Make sure Composer dependencies are installed inside the container.

---

## 📧 Email Configuration

The project uses `PHPMailer` with SMTP settings defined in your `.env` file:
```
EMAIL_HOST=smtp.example.com
EMAIL_USER=your@email.com
EMAIL_PASSWORD=secret
EMAIL_PORT=465
```

---

## 📄 License

This project was built by **Ori Segev** as part of a technical junior developer task.  
Feel free to explore, use and improve.

---
