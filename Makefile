.PHONY: bash
bash: ## Connexion au container
	@docker-compose exec -u www-data server bash

.PHONY: chown
chown: ## Fix les droits
	@docker-compose run --rm php chown -R $(id -u):$(id -g) .

.PHONY: phpcsfixer
phpcsfixer:
	@docker-compose exec -u www-data server tools/php-cs-fixer/vendor/bin/php-cs-fixer fix

.PHONY: rector
rector:
	@docker-compose exec -u www-data server vendor/bin/rector process src
