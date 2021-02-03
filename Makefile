build:
	docker build -t smarthome .
	docker run -v $(shell pwd):/app smarthome composer install
up:
	docker run -it -v $(shell pwd):/app smarthome sh
test:
	docker run -v $(shell pwd):/app smarthome vendor/bin/phpunit  --testdox --colors=always
restart:
	supervisorctl restart sh-eq3-core sh-eq3-subscriber sh-mijia
stop:
	supervisorctl stop sh-eq3-core sh-eq3-subscriber sh-mijia
start:
	supervisorctl start sh-eq3-core sh-eq3-subscriber sh-mijia

help:
	@echo ""
	@echo "Please use make <command> where <command> is one of:"
	@echo ""
	@echo "  build    create docker image and install smarthome dependencies."
	@echo ""
	@echo "  test    execute smarthome tests."
	@echo ""
