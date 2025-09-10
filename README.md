# Users Service
This microservice manages user registration, authentication, and login.  
It is part of the Ecommerce Microservices project.

---

## Requirements
- PHP >= 8.1
- Composer
- MySQL
- Laravel


## Setup
1. Clone the repository:
git clone https://github.com/VanceeBassem/users-service.git
git clone 
cd users-service
2. Install dependencies:
composer install
3. Copy .env.example to .env and configure your database:
cp .env.example .env
4. Generate app key:
php artisan key:generate
5. Run migrations (and optional seeders):
php artisan migrate --seed
6. Start the service:
php artisan serve --port=8001

## End Points

Authentication
POST /api/register → Register a new user
POST /api/login → Authenticate and receive a JWT
GET /api/me → Get the logged-in user (requires JWT)

## Notes
This service uses JWT authentication.
It is designed to work with other microservices (products-service, orders-service, warehouse-service).