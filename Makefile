install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

p_yaml:
	./bin/gendiff plain1.yaml plain2.yml

p_json:
	./bin/gendiff plain1.json plain2.json

p_json_pl:
	./bin/gendiff --format plain plain1.json plain2.json

n_yaml:
	./bin/gendiff nest1.yaml nest2.yml

n_json:
	./bin/gendiff nest1.json nest2.json

n_json_pl:
	./bin/gendiff --format plain nest1.json nest2.json

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
