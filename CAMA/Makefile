.PHONY: start stop restart logs down

start:
	@docker-compose up -d --build

stop:
	@docker-compose stop

re:
	@docker-compose down
	@docker-compose up -d --build

logs:
	@docker-compose logs -f

down:
	@docker-compose down

volclean:
	@docker-compose down --volumes