.PHONY: help
help:
	@echo "Available commands:"
	@echo "  make install    - Install dependencies"
	@echo "  make check      - Check code"
	@echo "  make fix        - Fix code"
	@echo "  make test       - Run tests"
	@echo "  make clean      - Ckear cache"

install:
	composer install
	npm install

check:
	@echo "=== PHP CodeSniffer ==="
	vendor/bin/phpcs --standard=phpcs.xml || true
	@echo ""
	@echo "=== PHP-CS-Fixer ==="
	vendor/bin/php-cs-fixer fix --dry-run --diff || true
	@echo ""
	@echo "=== Psalm ==="
	vendor/bin/psalm

fix:
	vendor/bin/php-cs-fixer fix
	vendor/bin/phpcbf --standard=phpcs.xml || true
	vendor/bin/psalm --alter --issues=all

test:
	vendor/bin/phpunit

clean:
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf .phpcs-cache
	rm -rf var/cache/.psalm-cache
