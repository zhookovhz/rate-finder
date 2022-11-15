install: env sail-install

env:
	test -s .env || cp .env.example .env

sail-install:
	docker run --rm --interactive --tty -v $${PWD}:/app -w /app -u $$(id -u):$$(id -g) \
	laravelsail/php81-composer:latest \
	bash -c "composer install --ignore-platform-reqs && php artisan sail:install --with=pgsql,redis"

up:
	vendor/bin/sail up -d --remove-orphans

down:
	vendor/bin/sail down

test:
	vendor/bin/sail test

migrate:
	vendor/bin/sail artisan migrate:fresh
