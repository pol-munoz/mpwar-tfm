BOLD=\x1B[1m
UNBOLD=\x1B[0m

.PHONY: start
start:
	@echo "🎬 ${BOLD}Starting application${UNBOLD}..."
	docker compose up -d

stop:
	@echo "🛑 ${BOLD}Stopping application${UNBOLD}..."
	docker compose down

install:
	@echo "🏗 ${BOLD}Installing dependencies${UNBOLD}..."
	docker-compose exec -T php composer install -d /var/www/kunlabo