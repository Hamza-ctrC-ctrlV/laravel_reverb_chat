# Laravel Reverb Chat

A real-time chat application built with **Laravel 11** and **Laravel Reverb**, using Laravel’s native WebSocket server for high-performance real-time communication without third-party services.

---

## 🚀 Features

- **Real-time Messaging** – Instant message delivery using WebSockets  
- **Laravel Reverb** – First-party WebSocket server (no Pusher required)  
- **Laravel Echo** – Client-side event listening  
- **Blade & Tailwind CSS** – Clean and responsive chat UI  

---

## 📋 Prerequisites

Make sure you have the following installed:

- PHP **8.2+**
- Composer
- Node.js & NPM
- Database (SQLite / MySQL / PostgreSQL)

---

## 🛠 Installation

### Clone the repository
```bash
git clone https://github.com/Hamza-ctrC-ctrlV/laravel_reverb_chat.git
cd laravel_reverb_chat
```
### Install backend dependencies
```bash
composer install
```
### Install frontend dependencies
```bash
npm install
```
### Environment setup
```bash
cp .env.example .env
```
### Configure your database and Reverb settings in .env
```bash
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```
### Generate application key
```bash
php artisan key:generate
```
### Run migrations
```bash
php artisan migrate
```
### Build frontend assets
```bash
npm run build
```
## 🏁 Running the Application
- *Open three terminals:*
### 1. Laravel server
```bash
php artisan serve
```
### 2. Reverb WebSocket server
```bash
php artisan reverb:start
```
### 3. Queue worker (optional but recommended)
```bash
php artisan queue:listen
```
### Open your browser at:
```bash
http://localhost:8000
```
