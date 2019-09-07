port := 1234

test:
	./vendor/bin/phpunit

make test-coverage:
	./vendor/bin/phpunit --coverage-html=coverage

run:
	php -S 0.0.0.0:$(port) public/index.php
