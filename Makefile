.PHONY: help
help:
	@echo "Available commands:"
	@echo "  make install    - Install dependencies"
	@echo "  make check      - Check code"
	@echo "  make fix        - Fix code"
	@echo "  make test       - Run tests"

install:
	composer install
	npm install

check:
	@echo "=== PHP CodeSniffer ==="
	vendor/bin/phpcs --standard=phpcs.xml
	@echo ""
	@echo "=== PHP-CS-Fixer ==="
	vendor/bin/php-cs-fixer fix --dry-run --diff
	@echo ""
	@echo "=== Psalm ==="
	vendor/bin/psalm

fix:
	vendor/bin/php-cs-fixer fix
	vendor/bin/phpcbf --standard=phpcs.xml
	vendor/bin/psalm --alter --issues=all

test:
	vendor/bin/phpunit
