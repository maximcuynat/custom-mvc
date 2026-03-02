# Custom MVC PHP

Template MVC PHP fait maison — zéro dépendance front, ORM Active Record, système de composants natif.

**Stack :** PHP 8.2 · Apache · MySQL 8.0 · Docker

---

## Installation

### 1. Cloner le projet

```bash
git clone <votre-repo>
cd <nom-projet>
```

### 2. Lancer les conteneurs

```bash
docker compose up -d --build
```

Ou avec Make :

```bash
make build && make up
```

### 3. Accéder à l'application

| Service | URL | Accès |
|---|---|---|
| Application | http://localhost:8001 | — |
| Adminer | http://localhost:8080 | Serveur: `db` · User: `root` · Password: `root_password` |
| MySQL | localhost:3306 | User: `root` · Password: `root_password` |

---

## Commandes Make

```bash
make build      # Build les images
make up         # Démarre les conteneurs
make down       # Arrête les conteneurs
make restart    # Redémarre
make logs       # Affiche les logs
make shell      # Shell dans le conteneur web
make db-shell   # Shell MySQL
make clean      # Supprime tout (⚠️ efface la BDD)
make rebuild    # Rebuild complet
```

---

## Variables d'environnement

Définies dans `docker-compose.yml` :

```yaml
DB_HOST=db
DB_NAME=internships
DB_USER=root
DB_PASSWORD=root_password
```

> ⚠️ En production : changez les mots de passe, utilisez un `.env` externe, activez HTTPS.

---

## Documentation

La documentation complète est dans le dossier [`docs/`](docs/index.md).

| Guide | Description |
|---|---|
| [Routing](docs/routing.md) | Comment les URLs sont résolues |
| [Contrôleurs](docs/controllers.md) | Créer et utiliser les contrôleurs |
| [Modèles & ORM](docs/models.md) | Active Record, QueryBuilder |
| [Vues & Composants](docs/views.md) | Layouts, composants, partials |
| [Frontend CSS](docs/frontend.md) | Design system, classes utilitaires |
| [Sécurité](docs/security.md) | CSRF, bonnes pratiques |
