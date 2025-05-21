.PHONY: start restart logs down volclean

start:
	@docker-compose up -d --build

restart:
	@docker-compose restart

re:
	@docker-compose down
	@docker-compose up -d --build

logs:
	@docker-compose logs

down:
	@docker-compose down

volclean:
	@docker-compose down --volumes
