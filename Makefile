larastan:
	./vendor/bin/phpstan analyse

fixer:
	./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v