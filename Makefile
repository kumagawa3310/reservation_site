larastan:
	./vendor/bin/phpstan analyse --memory-limit=512M

fixer:
	./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v