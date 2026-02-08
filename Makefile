.PHONY: help build up down restart logs shell db-shell clean rebuild

help:
	@echo "Commandes disponibles :"
	@echo "  make build     - Build les images Docker"
	@echo "  make up        - Démarre les conteneurs"
	@echo "  make down      - Arrête les conteneurs"
	@echo "  make restart   - Redémarre les conteneurs"
	@echo "  make logs      - Affiche les logs"
	@echo "  make shell     - Ouvre un shell dans le conteneur web"
	@echo "  make db-shell  - Ouvre un shell MySQL"
	@echo "  make clean     - Supprime conteneurs et volumes"
	@echo "  make rebuild   - Rebuild complet"

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

shell:
	docker-compose exec web bash

db-shell:
	docker-compose exec db mysql -uroot -proot_password internships

clean:
	docker-compose down -v
	docker system prune -f

rebuild:
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d