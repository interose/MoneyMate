PHP ?= php
COMPOSER ?= composer
SYMFONY ?= SYMFONY

.PHONY: install
install:
	$(COMPOSER) install
	$(PHP) bin/console importmap:install

.PHONY: prepare-db
prepare-db:
	$(PHP) bin/console doctrine:database:create --if-not-exists
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction

.PHONY: load-test-data
load-test-data:
	$(PHP) bin/console doctrine:fixtures:load

.PHONY: reset-db
reset-db:
	$(PHP) bin/console doctrine:database:drop --force
	$(PHP) bin/console doctrine:database:create
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) bin/console doctrine:fixtures:load --no-interaction

.PHONY: run
run:
	$(SYMFONY) server:start -d

.PHONY: clean
clean:
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf var/tailwind/*


