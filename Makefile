tests: db
	php ./vendor/bin/phpunit

unittests:
	php ./vendor/bin/phpunit ./tests/Regis/

functionaltests: db
	php ./vendor/bin/phpunit ./tests/Functional/

db:
	bin/console doctrine:schema:update --force -n --env=test
	bin/console doctrine:fixtures:load --env=test -n

.PHONY: tests unittests functionaltests
