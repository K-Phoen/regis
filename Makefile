tests: db
	php ./vendor/bin/phpunit

unittests:
	php ./vendor/bin/phpunit ./tests/Regis/

functionaltests: db
	php ./vendor/bin/phpunit ./tests/Functional/

db:
	bin/console doctrine:schema:update --force -n --env=test
	bin/console doctrine:fixtures:load --env=test -n

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon src tests

.PHONY: tests unittests functionaltests phpstan
