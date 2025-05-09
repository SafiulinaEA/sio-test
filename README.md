# Symfony REST application - calculate price and make payment

### Requirements

- Docker (Linux/macOS, на Windows — через WSL)
- Docker Compose

## Quick start

Clone the repository and initialise the project:
```bash
make init
```

This will:
•	Build and run the containers
•	Launch the Symfony application at http://127.0.0.1:8337
•	Mount the current directory inside the container

⸻

Manual Setup (if make is unavailable)

```bash
docker compose up -d --build
docker compose exec sio_test composer install
docker compose exec sio_test bin/console doctrine:database:create
docker compose exec sio_test bin/console doctrine:migrations:migrate
docker compose exec sio_test bin/console doctrine:fixtures:load
```

## Project Structure
•	src/ — main business logic
•	requests.http — example HTTP requests
•	tests/ — unit tests

## Notes
•	This container setup is intended for development and testing.
•	The application uses Symfony Validator, Doctrine ORM, and adapter classes to external payment processors.
•	New payment providers can be integrated via the PaymentProcessorInterface.