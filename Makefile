install: env composer-init

env:
	test -s .env || cp .env.example .env

composer-init:
	docker run --rm --interactive --tty -v $${PWD}:/app -w /app -u $$(id -u):$$(id -g) \
	laravelsail/php81-composer:latest \
	bash -c "composer install --ignore-platform-reqs"

up:
	vendor/bin/sail up -d --remove-orphans

down:
	vendor/bin/sail down

test:
	vendor/bin/sail test

migrate:
	vendor/bin/sail artisan migrate:fresh
