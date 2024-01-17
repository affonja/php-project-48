install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

inspect:
	composer exec --verbose phpstan analyse -- -c phpstan.neon

test:
	composer exec --verbose phpunit -- --testdox --colors=always tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
