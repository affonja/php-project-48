install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
	composer exec --verbose phpunit tests
