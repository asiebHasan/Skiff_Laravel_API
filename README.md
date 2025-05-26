# ⏱️ Freelancer Time Tracking System

A full-featured time tracking application for freelancers and teams, built with Laravel. Users can log time entries, associate them with projects and clients, and generate PDF reports for billing or time analysis.

---

## 🚀 Features

- User authentication (Laravel Breeze/Fortify/Sanctum support)
- Time log creation (billable / non-billable)
- Project and client management
- PDF export of selected logs
- Responsive API structure (if building an SPA/frontend)

---

## 🛠️ Installation

```bash
# Clone the repository
git clone https://github.com/asiebHasan/Skiff_Laravel_API.git
cd Skiff_Laravel_API
cd freelance_time_tracker_api

# Install dependencies
composer install

# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Configure your DB settings in .env
# DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Run migrations
php artisan migrate

# Seed with test data (optional)
php artisan db:seed

# Serve the app
php artisan serve


## 🧱 Database Structure
The following tables define the core schema for the time tracking system:

### users
| Column     | Type      | Description                |
| ---------- | --------- | -------------------------- |
| id         | BIGINT    | Primary key                |
| name       | STRING    | Full name of the user      |
| email      | STRING    | Unique email address       |
| password   | STRING    | Hashed password            |
| timestamps | TIMESTAMP | `created_at`, `updated_at` |


### clients
| Column          | Type      | Description                         |
| --------------- | --------- | ----------------------------------- |
| id              | BIGINT    | Primary key                         |
| user\_id        | BIGINT    | Foreign key to `users` table        |
| name            | STRING    | Client or company name              |
| email           | STRING    | Clients contact email               |
| contact\_person | STRING    | Person to contact within the client |
| timestamps      | TIMESTAMP | `created_at`, `updated_at`          |



### projects
| Column      | Type      | Description                           |
| ----------- | --------- | ------------------------------------- |
| id          | BIGINT    | Primary key                           |
| user\_id    | BIGINT    | Owner (usually the freelancer/user)   |
| client\_id  | BIGINT    | Foreign key to `clients` table        |
| title       | STRING    | Project title                         |
| description | TEXT      | Project description                   |
| deadline    | DATETIME  | Project deadline                      |
| status      | ENUM      | Project status (e.g., active, closed) |
| timestamps  | TIMESTAMP | `created_at`, `updated_at`            |


### projects

| Column      | Type      | Description                           |
| ----------- | --------- | ------------------------------------- |
| id          | BIGINT    | Primary key                           |
| user\_id    | BIGINT    | Owner (usually the freelancer/user)   |
| client\_id  | BIGINT    | Foreign key to `clients` table        |
| title       | STRING    | Project title                         |
| description | TEXT      | Project description                   |
| deadline    | DATETIME  | Project deadline                      |
| status      | ENUM      | Project status (e.g., active, closed) |
| timestamps  | TIMESTAMP | `created_at`, `updated_at`            |


### time_logs
| Column      | Type      | Description                          |
| ----------- | --------- | ------------------------------------ |
| id          | BIGINT    | Primary key                          |
| user\_id    | BIGINT    | Foreign key to `users` table         |
| project\_id | BIGINT    | Foreign key to `projects` table      |
| start\_time | DATETIME  | When the tracked session started     |
| end\_time   | DATETIME  | When the session ended               |
| hours       | FLOAT     | Total hours worked                   |
| tag         | ENUM      | Label (e.g., billable, non-billable) |
| description | TEXT      | Description or notes for the session |
| timestamps  | TIMESTAMP | `created_at`, `updated_at`           |

## 📡 API Endpoints
All routes (except register and login) require authentication via auth:sanctum.

### 🔐 Authentication
| Method | Endpoint    | Description            |
| ------ | ----------- | ---------------------- |
| POST   | `/register` | Register new user      |
| POST   | `/login`    | User login             |
| POST   | `/logout`   | Logout current user    |
| GET    | `/user`     | Get authenticated user |


### 👥 Client Management
| Method | Endpoint       | Description              |
| ------ | -------------- | ------------------------ |
| GET    | `/client/`     | List all clients         |
| GET    | `/client/{id}` | View a specific client   |
| POST   | `/client/`     | Create a new client      |
| POST   | `/client/{id}` | Update a specific client |
| DELETE | `/client/{id}` | Delete a specific client |


### 📁 Project Management
| Method | Endpoint        | Description               |
| ------ | --------------- | ------------------------- |
| GET    | `/project/`     | List all projects         |
| GET    | `/project/{id}` | View a specific project   |
| POST   | `/project/`     | Create a new project      |
| POST   | `/project/{id}` | Update a specific project |
| DELETE | `/project/{id}` | Delete a specific project |



### ⏱️ Time Log Management
| Method | Endpoint                | Description                    |
| ------ | ----------------------- | ------------------------------ |
| GET    | `/time-logs/{id}`       | View specific time log         |
| POST   | `/time-logs`            | Create a new time log          |
| POST   | `/time-logs/{id}`       | Update a specific time log     |
| DELETE | `/time-logs/{id}`       | Delete a specific time log     |
| POST   | `/time-logs/{id}/start` | Start tracking time (clock-in) |
| POST   | `/time-logs/{id}/end`   | Stop tracking time (clock-out) |



### 📊 Filtered Logs
| Method | Endpoint                                    | Description            |
| ------ | ------------------------------------------- | ---------------------- |
| GET    | `/time-logs/logs/project/{id}`              | Logs by project        |
| GET    | `/time-logs/logs/user`                      | Logs by current user   |
| GET    | `/time-logs/logs/day`                       | Logs by current day    |
| GET    | `/time-logs/logs/week`                      | Logs by current week   |
| GET    | `/time-logs/logs/between?start=...&end=...` | Logs between two dates |



### ⏱️ Total Hours
| Method | Endpoint                              | Description                    |
| ------ | ------------------------------------- | ------------------------------ |
| GET    | `/time-logs/total/project/{id}`       | Total hours by project         |
| POST   | `/time-logs/total/day`                | Total hours for a specific day |
| GET    | `/time-logs/total/client/{client_id}` | Total hours by client          |


### 🧾 Export
| Method | Endpoint                | Description                 |
| ------ | ----------------------- | --------------------------- |
| POST   | `/time-logs/export/pdf` | Export selected logs as PDF |


