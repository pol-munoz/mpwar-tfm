BOLD=\x1B[1m
UNBOLD=\x1B[0m

.PHONY: start
start:
	@echo "🎬 ${BOLD}Starting application${UNBOLD}..."
	@docker compose up -d

.PHONY: stop
stop:
	@echo "🛑 ${BOLD}Stopping application${UNBOLD}..."
	@docker compose down

.PHONY: install@php
install@php:
	@echo "🏗 ${BOLD}Installing PHP dependencies${UNBOLD}..."
	@docker-compose exec -T php composer install -d /var/www/kunlabo

.PHONY: install@node
install@node:
	@echo "🏗 ${BOLD}Installing Node dependencies${UNBOLD}..."
	@docker-compose -f docker-compose.node.yaml run --rm -T node yarn install

.PHONY: install
install: install@php install@node

.PHONY: schema
schema:
	@echo "🗂️ ${BOLD}Generating database schema${UNBOLD}..."
	@docker-compose exec -T php bin/console doctrine:schema:update --force

.PHONY: assets
assets:
	@echo "🛠️ ${BOLD}Generating encore dev assets${UNBOLD}..."
	@docker-compose -f docker-compose.node.yaml run --rm -T node yarn encore dev

.PHONY: assets@prod
assets@prod:
	@echo "🛠️ ${BOLD}Generating encore production assets${UNBOLD}..."
	@docker-compose -f docker-compose.node.yaml run --rm -T node yarn encore production

.PHONY: run
run: start install assets schema