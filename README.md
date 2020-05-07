# TODOS

This is a simple todo application based on the latest version of the Symfony framework (fullstack).


## Requirements

* A working shell, not Powershell
* [GIT](https://git-scm.com/) >= 2.15
* [Docker CE](https://www.docker.com/) >= 17.12.0
* [Yarn](https://yarnpkg.com/en/) >= 1.10
* [Make](https://www.gnu.org/software/make/)
    

## Installation
```sh
# Clone the repository
$ git clone ssh://git@company-git-scm-server.intra/todos.git

# Go into the folder
$ cd todos

# Init environment (will copy all the *.dist files)
$ make init

# Start the project with a web server listening on http://localhost:8080/
# This will start Docker services, install Composer dependencies,
# install Yarn dependencies and build a working version of the frontend assets
$ make start

# Create a database called todos, create the current schema and populate it
# with initial random data -> goto http://localhost:8080/
$ make dev

```

## Development standards

There is an .editorconfig file in the project root. Please adhere to these
standards. Otherwise pretty common stuff:
* PSR 12 for PHP, JS and CSS standards follow Facebook/React settings
* Strict typing is preffered, so don't @param dockblocks etc.
* Test your code with Unit tests and refactor every day.
* Always check the critical acceptance path with Codeception
* Get things done in a proper way. No quick fixes.


## Design

### Docker 

The application uses docker and docker-compose for almost everything. All
services are described in docker-compose.yaml file and all environment specific
variables can be found in the .env file

* php - PHP application (both frontend and API), served by [Caddy](https://hub.docker.com/r/ezmid/s4-caddy)
* mysql - Database - MySQL / MariaDB
* adminer - Adminer

#### How to work inside of a service
```sh
# Example: Log into the PHP container. You will be in the /app directory if
# everything is setup up correctly
$ make sh
# Or alternatively
$ docker-compose exec php sh

# Execute a Symfony 4 command
$ php bin/console doctrine:schema:update --force
```

### Backend / API

The backend is composed of 2 parts. The API itself and an administrative UI.
For now they are one application glued together. This can change in the near
future as we want to separate it into 2 services.

All source files are located in {PROJECT_ROOT}/src.

### Frontend

All frontend assets are managed with Yarn. For a complete list look at {PROJECT_ROOT}/package.json.

[Webpack](https://webpack.js.org/) is used as the asset bundler:
* CSS extension language: [SASS](https://sass-lang.com/)
* Component library: [Bootstrap 4](https://getbootstrap.com/)
* Icon pack: [FontAwesome](https://fontawesome.com/)
* Template engine: [Twig 2](https://twig.symfony.com/)

All asset files are located in {PROJECT_ROOT}/assets.
All templates files are located in {PROJECT_ROOT}/templates.
All build files are located in {PROJECT_ROOT}/public/build.

```sh
# To install all assets
$ yarn install

# To update all assets
$ yarn update

# During development Yarn watches all chages and rebuilds automatically
$ yarn watch

# Build a production version of the assets
$ yarn build
```

### Testing data

We use the [Faker library](https://github.com/fzaninotto/Faker) to create random test data and verify use cases.
All the API endpoints are only available in the DEV environment.

```sh
# Create database from scratch, or recreate the old one
$ make db
# The same as calling the API endpoint
$ curl http://localhost:8080/app/api/faker/database
# Populate the DB with default users for development
$ make users
# Populate the DB with fake Applications
$ make data
```

### Code quality

```sh
# LOCs - currently the package doesn not support Symfony 5
$ make phploc

# Linter
$ make phplinter

# Static analysis
$ make phpstan

# This part is missing due to NDAs
$ make test
$ make test/unit
$ make test/func
$ make test/acceptance
```
