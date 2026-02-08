# Projet MVC PHP avec Docker

Architecture MVC en PHP avec Docker, MySQL et Adminer.

## Stack Technique

- **PHP 8.2** avec Apache
- **MySQL 8.0**
- **Adminer** (interface web pour MySQL)
- **Docker & Docker Compose**

## Prérequis

- Docker
- Docker Compose

## Structure du Projet

```
.
├── app/
│   ├── controllers/
│   │   ├── Router.php
│   │   └── ControllerHome.php
│   ├── models/
│   │   └── Model.php
│   ├── views/
│   │   ├── View.php
│   │   ├── template.php
│   │   ├── viewAccueil.php
│   │   └── viewError.php
│   ├── .htaccess
│   └── index.php
├── docker-compose.yml
├── Dockerfile
└── init.sql
```

## Installation et Lancement

### 1. Cloner le projet

```bash
git clone <votre-repo>
cd <nom-projet>
```

### 2. Lancer les conteneurs

```bash
docker-compose up -d --build
```

Cette commande :
- Build l'image PHP avec Apache
- Lance MySQL
- Lance Adminer
- Crée le réseau bridge
- Initialise la BDD avec `init.sql`

### 3. Vérifier que tout fonctionne

Attendre quelques secondes que MySQL soit complètement démarré, puis :

```bash
docker-compose ps
```

Tous les services doivent être "Up".

## Accès aux Services

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application Web** | http://localhost:8000 | - |
| **Adminer** | http://localhost:8080 | Serveur: `db`<br>User: `root`<br>Password: `root_password`<br>BDD: `internships` |
| **MySQL** (direct) | localhost:3306 | User: `root`<br>Password: `root_password` |

## Commandes Utiles

### Démarrer les conteneurs

```bash
docker-compose up -d
```

### Arrêter les conteneurs

```bash
docker-compose down
```

### Arrêter et supprimer les volumes (⚠️ efface la BDD)

```bash
docker-compose down -v
```

### Voir les logs

```bash
docker-compose logs -f
docker-compose logs -f web
docker-compose logs -f db
```

### Reconstruire après modification du Dockerfile

```bash
docker-compose up -d --build
```

### Accéder au shell du conteneur web

```bash
docker-compose exec web bash
```

### Accéder au shell MySQL

```bash
docker-compose exec db mysql -uroot -proot_password internships
```

### Importer un dump SQL

```bash
docker-compose exec -T db mysql -uroot -proot_password internships < dump.sql
```

### Exporter la BDD

```bash
docker-compose exec db mysqldump -uroot -proot_password internships > dump.sql
```

## Architecture MVC

### Routing

Le fichier `.htaccess` redirige toutes les requêtes vers `index.php` qui instancie le `Router`.

**Exemples d'URLs** :
- `/` → redirige vers `/home`
- `/home` → `ControllerHome::home()`
- `/user/profile/123` → `ControllerUser::profile($url)` avec `$url = ['user', 'profile', '123']`

### Contrôleur

```php
class ControllerExample
{
    private $_view;

    public function __construct($url)
    {
        if (isset($url[0]) && $url[0] === "example")
            $this->example();
        else
            throw new Exception('Page introuvable');
    }

    private function example()
    {
        $this->_view = new View('Example');
        $this->_view->generate('Titre', array('key' => 'value'));
    }
}
```

### Modèle

```php
class ExampleModel extends Model
{
    public function getData()
    {
        $req = $this->getBdd()->prepare('SELECT * FROM table');
        $req->execute();
        return $req->fetchAll();
    }
}
```

### Vue

Créer `views/viewExample.php` :

```php
<div>
    <h1>Exemple</h1>
    <p><?= $key ?></p>
</div>
```

## Base de Données

La BDD est initialisée automatiquement avec `init.sql` au premier lancement.

### Tables créées

- `users` : utilisateurs de l'application
- `internships` : stages

### Données de test

2 utilisateurs et 2 stages sont insérés automatiquement.

Mot de passe par défaut (hashé) : `password`

## Variables d'Environnement

Les variables sont définies dans `docker-compose.yml` :

```yaml
environment:
  - DB_HOST=db
  - DB_NAME=internships
  - DB_USER=root
  - DB_PASSWORD=root_password
```

Modifie-les selon tes besoins.

## Troubleshooting

### L'app ne se connecte pas à la BDD

Vérifie que MySQL est bien démarré :

```bash
docker-compose logs db
```

Attends le message `ready for connections`.

### Port déjà utilisé

Si les ports 8000, 8080 ou 3306 sont occupés, modifie-les dans `docker-compose.yml` :

```yaml
ports:
  - "8001:80"  # au lieu de 8000:80
```

### Réinitialiser complètement le projet

```bash
docker-compose down -v
docker-compose up -d --build
```

## Développement

Les modifications dans `./app` sont synchronisées en temps réel grâce au volume.

Pas besoin de rebuild pour les changements PHP.

## Sécurité

⚠️ **En production** :
- Change les mots de passe MySQL
- Utilise des variables d'environnement externes (fichier `.env`)
- Active HTTPS
- Configure un reverse proxy (Nginx/Traefik)
- Limite l'accès à Adminer