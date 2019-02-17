DOCKER_RUN = docker-compose run --rm composer

all: install test
.PHONY: all

install:
		$(DOCKER_RUN) install
.PHONY: install

update:
		$(DOCKER_RUN) update
.PHONY: update

cs:
		$(DOCKER_RUN) run-script cs
.PHONY: cs

test: wait-for-mongo
		$(DOCKER_RUN) run-script test
.PHONY: test

test-cc: wait-for-mongo
		$(DOCKER_RUN) run-script test-cc
.PHONY: test-cc

wait-for-mongo:
		docker-compose run --rm dockerize -wait tcp://mongo:27017 -timeout 1m
.PHONY: wait-for-mongo

require:
		$(DOCKER_RUN) require $(package)
.PHONY: require

require-dev:
		$(DOCKER_RUN) require --dev $(package)
.PHONY: require-dev
