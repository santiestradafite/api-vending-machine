JOB_NAME=api
PROJECT_NAME=${JOB_NAME}
USER_ID:=$(shell id -u)
GROUP_ID:=$(shell id -g)
COMPOSE=docker-compose -p "$(PROJECT_NAME)" -f docker/docker-compose.yml

.EXPORT_ALL_VARIABLES:
DOCKER_UID=$(USER_ID)
DOCKER_GID=$(GROUP_ID)

up:
	$(COMPOSE) build
	$(COMPOSE) up -d
down:
	$(COMPOSE) down
stop:
	$(COMPOSE) stop
refresh:
	$(COMPOSE) down
	$(COMPOSE) build
	$(COMPOSE) up -d
reload:
	$(COMPOSE) stop
	$(COMPOSE) build
	$(COMPOSE) up -d
bash:
	$(COMPOSE) run --rm api-vending-machine bash
autoload:
	$(COMPOSE) run --rm api-vending-machine composer dump-autoload
install:
	$(COMPOSE) build
	$(COMPOSE) up -d
	$(COMPOSE) exec api-vending-machine composer install
test-unit:
	$(COMPOSE) exec api-vending-machine bin/phpunit -d memory_limit=256M ${parameters}