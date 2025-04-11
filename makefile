# GDA Makefile
# Holding outside-container commands
# For project-related commands/scripts, use file .bash_project.sh

# Parameters
DB_NAME = link_screech_app
DOCKER_APP_CONTAINER = web_link-screech-app

# Executables
DOCKER          = docker
DOCKER_COMPOSE  = $(DOCKER) compose

help: # Show help
	@grep -E '^[a-zA-Z0-9 -]+:.*#'  makefile | sort | while read -r l; do printf "\033[1;32m$$(echo $$l | cut -f 1 -d':')\033[00m:$$(echo $$l | cut -f 2- -d'#')\n"; done

# -- Docker ------------------------------------------------------------------------------------------------------------
rebuild: # Build project docker
	@$(DOCKER_COMPOSE) build --force-rm --no-cache --parallel --pull

up: # Run project docker
	@$(DOCKER_COMPOSE) up -d

down: # Stop project docker
	@$(DOCKER_COMPOSE) down --remove-orphans

restart: down up # Restart project docker

sh: # Open docker container bash
	@docker exec -it $(DOCKER_APP_CONTAINER) bash

postclone:
	@docker exec -it $(DOCKER_APP_CONTAINER) bash -c 'composer install'
	@docker exec -it $(DOCKER_APP_CONTAINER) bash -c 'yarn'

