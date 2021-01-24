build:
	docker build -t smarthome .
	docker run -v $(shell pwd):/app smarthome composer install
up:
	docker run -it -v $(shell pwd):/app smarthome sh
test:
	docker run -v $(shell pwd):/app smarthome vendor/bin/phpunit  --testdox --colors=always

help:
	@echo ""
	@echo "Please use make <command> where <command> is one of:"
	@echo ""
	@echo "  build    create docker image and install smarthome dependencies."
	@echo ""
	@echo "  test    execute smarthome tests."
	@echo ""
