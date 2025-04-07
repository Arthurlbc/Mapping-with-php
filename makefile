-include .env

help: ## Display this current help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

copy-env: ## Copy .env.dist to .env
	cp -n .env.dist .env

## Start project
start:
	docker compose up -d && \
	docker compose run --rm php bin/console doctrine:database:create --if-not-exists && \
	docker compose run --rm php bin/console doctrine:migrations:migrate --no-interaction && \
	docker compose run --rm php bin/console doctrine:fixtures:load --no-interaction

stop: ## Stop project
	docker compose stop

phpstan:
	docker compose exec php vendor/bin/phpstan analyse -c phpstan.neon

php-cs-fixer:
	docker compose exec php vendor/bin/php-cs-fixer fix

phpspec:
	docker compose exec php vendor/bin/phpspec run