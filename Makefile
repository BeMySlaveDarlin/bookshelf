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

# Databse commands
db-migrate: .env
	@docker-compose exec -T service-php console doctrine:migrations:migrate --no-interaction
db-seed: .env
	@docker-compose exec -T service-php  console doctrine:fixtures:load --no-interaction

# Testing commands
tests: test-cs test-phpstan test-psalm test-phpunit
test-cs:
	@echo "Running phpcs checks"
	@docker-compose exec -T service-php phpcs
	@docker-compose exec -T service-php phpcbf -p
test-phpstan:
	@echo "Running phpstan checks"
	@docker-compose exec -T service-php phpstan analyse
test-psalm:
	@echo "Running psalm checks"
	@docker-compose exec -T service-php psalm --ignore-baseline
test-phpunit:
	@echo "Running phpunit checks"
	@docker-compose exec -T service-php phpunit
test-db-init: .env
	@docker-compose exec -T service-php console doctrine:schema:create --env=test
