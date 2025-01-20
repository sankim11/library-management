# Library Management System

This project is a Library Management System built using the Symfony framework. It allows users to manage books, members, loans, and reservations through RESTful APIs.

## Features

- Manage **Books** (add, view, update, delete)
- Manage **Members** (register, view, update, delete)
- Create and manage **Loans**
- Create and manage **Reservations**
- Secure endpoints with authentication
- Validate inputs and handle errors gracefully

## Prerequisites

To run this project, you need to have the following installed:

- PHP 8.1 or higher
- Composer
- PostgreSQL
- Symfony CLI
- Postman for testing the API

## Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd library-management
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

3. Configure the `.env` file for database and environment variables:

   ```env
   DATABASE_URL="postgresql://postgres:<your-password>@127.0.0.1:5432/library_management"
   ```

4. Create the database and update the schema:

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   ```

5. Start the Symfony development server:

   ```bash
   symfony server:start
   ```

6. Access the application at:

   [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Testing the API

### Postman
1. Import the `Postman` collection to test the endpoints.
2. Configure the API base URL (e.g., `http://127.0.0.1:8000`).

### PHPUnit
Run unit and functional tests:

```bash
php bin/phpunit
```

## Debugging

Use Symfony Profiler to debug requests:

1. Enable the profiler by setting `APP_ENV=dev` in `.env`.
2. Access the profiler at [http://127.0.0.1:8000/_profiler](http://127.0.0.1:8000/_profiler).

## Project Structure

- **src/**: Contains the application code (entities, services, controllers).
- **tests/**: Contains unit and functional tests.
- **public/**: Contains the entry point (`index.php`).
- **config/**: Contains configuration files.

## Security

- Authentication is implemented using JWT via `LexikJWTAuthenticationBundle`.
- Protected endpoints require a valid JWT token.

## Tools and Libraries

- **Symfony Framework**: Backend framework.
- **Doctrine ORM**: Database interactions.
- **ApiPlatform**: Auto-generates RESTful APIs.
- **JWT Authentication**: Secure endpoints.
- **PostgreSQL**: Database.
- **PHPUnit**: Testing framework.

## Troubleshooting

### Common Errors

1. **Database Not in Sync**
   ```bash
   php bin/console doctrine:schema:update --force
   ```

2. **Entity Not Found**
   Ensure the `id` exists in the database.

3. **Validation Errors**
   Review the payload structure and constraints.

## License

This project is open-source and available under the MIT License.
