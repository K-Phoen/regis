tests: db
	php ./vendor/bin/phpunit

unittests:
	php ./vendor/bin/phpunit ./tests/unit/

functionaltests: db
	php ./vendor/bin/phpunit ./tests/functional/

db:
	rm -f ./var/test_db.sqlite || true
	SYMFONY_ENV=test vendor/bin/phinx migrate
	SYMFONY_ENV=test vendor/bin/phinx seed:run

phpmd:
	./vendor/bin/phpmd src,tests text phpmd-ruleset.xml

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon src tests

.PHONY: tests unittests functionaltests phpstan phpmd
