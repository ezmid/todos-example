#!/usr/bin/make -f

# RUN WHOLE PROCESS IN ONE SHELL
.ONESHELL:

################################################################################
################################################################################
# Variable definitions
################################################################################

# Are we running in an interactive shell? If so then we can use codes for
# a colored output
ifeq ("$(shell [ -t 0 ] && echo yes)","yes")
FORMAT_BOLD=\e[1m
FORMAT_RED=\033[0;31m
FORMAT_YELLOW=\033[0;33m
FORMAT_GREEN=\x1b[32;01m
FORMAT_RESET=\033[0m
else
FORMAT_BOLD=
FORMAT_RED=
FORMAT_YELLOW=
FORMAT_GREEN=
FORMAT_RESET=
endif

# Path to the echo binary. This is needed because on some systems there are
# multiple versions installed and the alias "echo" may reffer to something
# different.
ECHO=$(shell which echo)
OSECHOFLAG=-e
UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S),Darwin)
	ECHO=echo
	OSECHOFLAG=
	FORMAT_BOLD=
endif

# Import all ENV vars / overwrite defaults
ifeq ($(shell test -e .env && echo -n yes),yes)
    include ./.env
    export $(shell sed 's/=.*//' .env)
endif

################################################################################
################################################################################
# Help and tool warmup
################################################################################

# Default target, must be first!
.ONESHELL: default
.PHONY: default
default:
	@$(ECHO) ""
	@$(ECHO) $(OSECHOFLAG) "$(FORMAT_BOLD)TODOS$(FORMAT_RESET)" \
	"$(FORMAT_YELLOW)\n\nCommands:$(FORMAT_RESET)\n\n" \
	"  make init                              Init environment from *.dist files\n" \
	"  make start                             Start all Docker services\n" \
	"  make dev                               Init the development env with random data\n" \
	"  make test                              Quick run tests\n" \
	"  make sh                                Log into the PHP container\n" \
	"\n" \
	"\n" \
	"$(FORMAT_YELLOW)\n\nContainer commands:$(FORMAT_RESET)\n\n" \
	"  make install                           Install all dependencies\n" \
	"  make db                                Create/Recreate database from scratch\n" \
	"  make users                             Create basic user set for development\n" \
	"  make data                              Populate database with fake data\n" \
	"  make clean                             Clean temporary data and environment variables\n" \
	"  make cc                                Alias for make clear\n" \
	"\n" \



# Clean up
.ONESHELL: clean
.PHONY: clean
clean:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Cleaning up project..$(FORMAT_RESET)\n"
	rm -f .env
	rm -f docker-compose.yaml
	rm -f phpunit.xml
	rm -f phpstan.neon
	rm -f .phplint.yaml
	rm -rf ./var/*

# Initialize the project
.ONESHELL: init
.PHONY: init
init:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Setting up project..$(FORMAT_RESET)\n"
	cp .env.dist .env
	cp docker-compose.yaml.dist docker-compose.yaml
	cp phpunit.xml.dist phpunit.xml
	cp phpstan.neon.dist phpstan.neon
	cp .phplint.yml.dist .phplint.yml
	mkdir -p public/build

# Install all dependencies
.ONESHELL: install
.PHONY: install
install:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Installing dependencies..$(FORMAT_RESET)\n"
	# @composer install --optimize-autoloader
	@composer install
	@php bin/console cache:warm

# Preload .env variables if file exists
.ONESHELL: env/load
.PHONY: env/load
env/load:
ifeq ($(shell test $(OSECHOFLAG) .env && echo -n yes),yes)
    include ./.env
    export $(shell sed 's/=.*//' .env)
endif

# Create dev environment
.ONESHELL: dev
.PHONY: dev
dev: env/load
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Creating development environment..$(FORMAT_RESET)\n"
	docker-compose exec php sh -c "make -s db"
	docker-compose exec php sh -c "make -s data"

# Start the project in Docker
.ONESHELL: start
.PHONY: start
start:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Starting Docker services..$(FORMAT_RESET)\n"
	@docker-compose up -d --force-recreate
	@docker-compose exec -u root:root php sh -c "make install"
	@yarn install
	@yarn dev

# Stop the project
.ONESHELL: stop
.PHONY: stop
stop:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Stoping all Docker services..$(FORMAT_RESET)\n"
	@docker-compose down

# Run all tests for the project
.ONESHELL: test
.PHONY: test
test:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Running tests$(FORMAT_RESET)\n"
	@docker-compose exec php sh -c "vendor/bin/codecept run --steps --xml"

# Log into the php container
.ONESHELL: sh
.PHONY: sh
sh:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Logging into container PHP$(FORMAT_RESET)\n"
	@docker-compose exec php sh


# Run all tests for the project
.ONESHELL: test/unit
.PHONY: test/unit
test/unit:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Running tests$(FORMAT_RESET)\n"
	@vendor/bin/phpunit

# Create DB from scratch
.ONESHELL: db
.PHONY: db
db:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Creating database from scratch..$(FORMAT_RESET)\n"
	curl --insecure -X GET ${TODOS_BASE_URI}api/faker/database

# Create random data
.ONESHELL: data
.PHONY: data
data:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Creating 50 random todos..$(FORMAT_RESET)\n"
	curl --insecure -X GET ${TODOS_BASE_URI}api/faker/todos/init

# PHPLint
.PHONY: phplint
.ONESHELL: phplint
phplint:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Linting project..$(FORMAT_RESET)\n"
	@docker-compose exec php sh -c "./vendor/bin/phplint ./src --no-cache"

# PHPStan
.PHONY: phpstan
.ONESHELL: phpstan
phpstan:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)PHPStan analyzing project..$(FORMAT_RESET)\n"
	@docker-compose exec php sh -c "./vendor/bin/phpstan analyze --memory-limit=1G"

# PHPLoc
.PHONY: phploc
.ONESHELL: phploc
phploc:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)PHPLoc analyzing project..$(FORMAT_RESET)\n"
	./vendor/bin/phploc ./src

# PHPUnit code coverage
.PHONY: coverage
.ONESHELL: coverage
coverage:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)PHPUnit code coverage$(FORMAT_RESET)\n"
	docker-compose exec php sh -c "./vendor/bin/phpunit --dump-xdebug-filter=var/cache/xdebug-filter.php"
	docker-compose exec php sh -c "./vendor/bin/phpunit --prepend var/cache/xdebug-filter.php"

# Codeception acceptance tests
.PHONY: acceptance
.ONESHELL: acceptance
acceptance:
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Codeception API tests$(FORMAT_RESET)\n"
	docker-compose exec php sh -c "./vendor/bin/codecept run Api --steps"
	@echo $(OSECHOFLAG) "\n$(FORMAT_YELLOW)Codeception ACCEPTANCE tests$(FORMAT_RESET)\n"
	docker-compose exec php sh -c "./vendor/bin/codecept run Acceptance --steps"

################################################################################
# Run a GOSS test
.PHONY: build/test
.ONESHELL: build/test
build/test:
	@dgoss run $(REGISTRY)/$(NAMESPACE)/$(IMAGE_NAME):$(TAG)

################################################################################
# SonarQube analysis
.PHONY: sonar
.ONESHELL: sonar
sonar:
	sed -i '.bak' 's/\/app/\/root\/src/g' var/build/junit.xml
	sed -i '.bak' 's/\/app/\/root\/src/g' var/build/coverage.xml
	docker run -ti -v ${shell pwd}:/root/src --link sonarqube newtmitch/sonar-scanner:3.0.3-alpine

################################################################################
# Start SonarQube in Docker
.PHONY: sonarqube
.ONESHELL: sonarqube
sonarqube:
	docker run -d --name sonarqube -p 9000:9000 -p 9092:9092 sonarqube:lts


