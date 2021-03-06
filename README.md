# Bookshelf and Wallet API examples

Example REST API for books, authors and casual wallet CRUD based on Symfony Framework.

## Requirements

- OS: Ubuntu, Debian, Windows WSL
- Docker & docker-compose
- NGINX 1.17+
- PHP 7.4+:
- Composer 2.1+
- MySQL 5.7+

# Dependencies

- Symfony 5
- Doctrine 2

# Installation

1. Copy environments to local `.env` file: `cp .env.example .env`
2. Edit database credentials and service ports in `.env` file
3. Run `make` or `make compose` to deploy application
4. Run `make db-migrate` and `make db-seed` to initiate database population

# Usage

For bookshelf api you can request helper routes `/en/author` or `/en/book`.

For wallets api you can request helper route `/wallet`.

# Tests
 - `make test-db-init` to create test schema
 - `make tests` for full tests run
 - `make test-cs` for code-style tests
 - `make test-phpstan` and `test check-psalm` for static code analysis
 - `make test-phpunit` for unit tests

[:license:]:   https://github.com/BeMySlaveDarlin/bookshelf/blob/master/LICENSE
