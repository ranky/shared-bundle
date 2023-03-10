##@ Docker
docker: ## Docker Compose with arguments
	$(DOCKER_COMPOSE) $(ARGUMENTS)

docker-exec: ## Shell Bash with arguments
	$(DOCKER_COMPOSE) exec $(ARGUMENTS)

docker-php: ## Exec PHP container with arguments
	$(DOCKER_EXEC_PHP) $(ARGUMENTS)

docker-php-root: ## Exec PHP container with arguments like root user
	$(DOCKER_EXEC_ROOT_PHP) $(ARGUMENTS)

docker-clear-tmp: # clean tmp cache bundle
	$(DOCKER_EXEC_PHP) rm -rf /tmp/*

docker-up: ## Start the docker
	$(DOCKER_COMPOSE) up -d --wait

docker-logs: ## Show Docker logs
	$(DOCKER_COMPOSE) logs -f

docker-logs-follow: ## Docker follow logs
	$(DOCKER_COMPOSE) logs -f

docker-restart: ## Restart
	$(DOCKER_COMPOSE) restart

docker-restart-php: ## Restart PHP
	$(DOCKER_COMPOSE) restart php

docker-down: ## Down the docker
	$(DOCKER_COMPOSE) down
