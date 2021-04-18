# vibon Local Environment Setup Scripts
# Original idea from: https://gist.github.com/mpneuried/0594963ad38e68917ef189b4e6a269db

# DOCKER TASKS
start: ## Start the pre-built environment.
	docker-compose up -d --build
	docker-compose exec php-fpm composer install
	docker-compose exec -d php-fpm php artisan horizon
	sudo n 14

stop: ## Stops/pause the environment.
	docker-compose exec -d php-fpm php artisan horizon:terminate
	docker-compose stop

install: ## Builds the environment for the first and starts it
	docker-compose up -d --build
	docker-compose exec php-fpm cp .env.example .env
	docker-compose exec php-fpm composer install
	docker-compose exec php-fpm php artisan key:generate
	docker-compose exec php-fpm php artisan storage:link
	docker-compose exec php-fpm php artisan migrate:fresh
	docker-compose exec php-fpm php artisan db:seed --no-interaction
	docker-compose exec db mysql -uroot -proot -e 'CREATE DATABASE IF NOT EXISTS test;'
	docker-compose exec db mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON test.* TO 'vibon'@'%';"
	docker-compose exec php-fpm php artisan migrate:fresh --database=test
	docker-compose exec -d php-fpm php artisan horizon
	npm install
	npm run dev

destroy: ## Destroy and clean the environment.
	docker-compose down --rmi all --volumes --remove-orphans

down: ## Remove containers
	docker-compose down

syntax-check: ## Checks syntax of php files with PSR-2
	docker-compose exec php-fpm vendor/bin/phpcs --standard=phpcs.xml --report=full

crawl-local: ## Crawls local website for broken links
	vendor/bin/http-status-check scan http://vibon.localhost --dont-crawl-external-links --output=crawl_errors.txt

# HELP
# This will output the help for each task
# thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
