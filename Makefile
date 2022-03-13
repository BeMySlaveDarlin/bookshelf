include .env

all: compose

# Build commands
build: .env
	@echo "Building containers"
	@docker-compose build
up: .env
	@echo "Starting containers"
	@docker-compose up -d
down: .env
	@echo "Stopping containers"
	@docker-compose down
remove: down
	@echo "Removing containers"
	@docker system prune -af
	@docker volume prune -f
restart: down up

# Application commands
compose: down build up
	@echo "Installing composer dependencies"
	@docker-compose exec -T service-php composer clear-cache
	@docker-compose exec -T service-php composer install --no-interaction --optimize-autoloader

# Testing commands
checks: check-cs check-phpstan check-psalm
check-cs:
	@echo "Running phpcs checks"
	@docker-compose exec -T service-php phpcs
	@docker-compose exec -T service-php phpcbf -p
check-phpstan:
	@echo "Running phpstan checks"
	@docker-compose exec -T service-php phpstan analyse
check-psalm:
	@echo "Running psalm checks"
	@docker-compose exec -T service-php psalm --ignore-baseline

