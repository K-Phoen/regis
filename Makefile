tests: db
	php ./vendor/bin/phpunit

unittests:
	php ./vendor/bin/phpunit ./tests/unit/

functionaltests: db
	php ./vendor/bin/phpunit ./tests/Functional/

db:
	vendor/bin/phinx migrate --environment=test
	bin/console doctrine:fixtures:load --env=test -n

phpmd:
	./vendor/bin/phpmd src,tests text phpmd-ruleset.xml

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon src tests

.PHONY: tests unittests functionaltests phpstan phpmd
