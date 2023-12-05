install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

yaml:
	./bin/gendiff file1.yaml file2.yml

json:
	./bin/gendiff file1.json file2.json

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
