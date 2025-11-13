<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Mental Prompt - Laravel Application

Aplikasi Laravel untuk manajemen prompt kualitas dengan **Laravel Octane + RoadRunner** untuk performa maksimal, disajikan melalui Nginx dalam satu container Docker.

## 🚀 Quick Start dengan Docker

### Prerequisites
- Docker & Docker Compose terinstall
- Git

### Setup & Jalankan Aplikasi

1.  **Clone repository**
    ```bash
    git clone <repository-url>
    cd mental-prompt
    ```

2.  **Setup environment**
    ```bash
    cp env.example .env
    ```

3.  **Build dan jalankan containers**
    ```bash
    docker-compose up --build -d
    ```

4.  **Jalankan database migrations**
    ```bash
    docker-compose exec app php artisan migrate
    ```

5.  **Akses aplikasi**
    -   Frontend: http://localhost
    -   Database: localhost:3306

### Docker Services

-   **App (Laravel Octane & Nginx)**: PHP 8.2 CLI + RoadRunner server dengan Nginx sebagai reverse proxy, semua dalam satu container.
-   **MySQL 8.0**: Database dengan persistent storage

### ⚡ Laravel Octane

Aplikasi ini menggunakan Laravel Octane dengan RoadRunner untuk:
- **10-100x lebih cepat** dari traditional PHP-FPM
- **In-memory application bootstrap** - aplikasi di-load sekali di memory
- **Concurrent request handling** dengan 4 workers (configurable)
- **Automatic worker recycling** setiap 500 requests untuk memory management

### Useful Commands

```bash
# Jalankan di background
docker-compose up -d

# Stop containers
docker-compose down

# Rebuild containers (clean build)
docker-compose down && docker-compose up --build --force-recreate

# Lihat logs
docker-compose logs -f app

# Lihat logs real-time dari Octane
docker-compose logs -f app | grep -i octane

# Masuk ke container app
docker-compose exec app bash

# Restart Octane (reload code changes)
docker-compose restart app

# Jalankan artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan queue:work

# Monitor Octane stats
docker-compose exec app php artisan octane:status
```

### Environment Variables

File `.env` sudah dikonfigurasi untuk Docker:
- **Server**: Laravel Octane dengan RoadRunner
- **Database**: MySQL container
- **Cache/Queue**: Menggunakan driver `database` untuk antrian yang lebih robust.
- **Session**: File-based (simple & reliable)
- **App URL**: http://localhost

### Database

- **Host**: mysql
- **Database**: mental_prompt
- **Username**: root
- **Password**: rootpassword

Pastikan untuk menjalankan migrasi secara manual setelah container berjalan.

## 📋 Development tanpa Docker

Jika ingin development tanpa Docker:

```bash
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate

# Install Octane
php artisan octane:install

# Start Octane server
php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000
```

## 📈 MCP Prompt Health Server

The `mcp-prompt-health/` directory contains a dedicated [Model Context Protocol (MCP)](https://docs.cursor.sh/extension-authoring/mcp) server for monitoring and submitting prompt quality metrics from a compatible editor (like Cursor) to the Laravel backend.

This server acts as a bridge, exposing an MCP tool that the editor can call. When called, the server forwards the metrics as an HTTP request to the `/api/prompt-quality` endpoint of this application.

There are two implementations available: TypeScript (primary) and Python (legacy/reference).

### Setup & Running (TypeScript)

The primary and recommended server is written in TypeScript.

1.  **Navigate to the directory:**
    ```bash
    cd mcp-prompt-health
    ```

2.  **Install dependencies:**
    ```bash
    npm install
    ```

3.  **Run the server:**
    ```bash
    npm run dev
    ```
    This command uses `tsx` to run the `prompt-quality-server.ts` file. The server will start and listen for requests over standard I/O (stdio).

### Configuration

The server's behavior can be configured with environment variables:

-   `PROMPT_QUALITY_API`: Sets the base URL for the Laravel backend API endpoint.
    -   **Default**: `http://localhost:8000`
    -   **Example**: `export PROMPT_QUALITY_API="https://your-production-app.com"`

### MCP Tool: `submit_prompt_quality`

The server exposes a single tool that can be called by an MCP client.

-   **Tool Name**: `submit_prompt_quality`
-   **Description**: Receives prompt quality scores and forwards them to the Laravel backend.
-   **Arguments**:
    -   `client_uuid` (string, required): A unique identifier for the client.
    -   `project` (string, required): The name of the project.
    -   `efektivitas` (number, required): Effectiveness score (0-100).
    -   `membingungkan` (number, required): Confusion score (0-100).

## 🔥 Performance Tips

1. **Octane Worker Tuning**: Adjust workers based on CPU cores
   ```bash
   php artisan octane:start --workers=8 --max-requests=1000
   ```

2. **Cache untuk Production**:
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

3. **Monitor Performance**:
   ```bash
   docker-compose exec app php artisan octane:status
   ```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
