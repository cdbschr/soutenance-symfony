# Soutenance Symfony 6
## Développement d'une API REST avec Symfony 6

### Prérequis
Installation des dépendances du projet
```sh
composer install
```

Installation d'une base de données MariaDB avec Docker, n'oubliez pas de changer les informations de création de la base de données dans le fichier `docker-compose.yml` après l'avoir copié.
```sh
cp docker-compose.example.yml docker-compose.yml
docker-compose up -d
```

Définition des variables d'environnement :
```sh
cp .env.example .env
```
Compléter selon les informations que vous avez saisies dans le fichier `docker-compose.yml`
<br />


Selon le mode d'utilisation de l'application, vous pouvez modifier les variables d'environnement suivantes :
- `APP_ENV` : `dev` ou `prod`
- `APP_DEBUG` : `true` ou `false`

<br />

Bonne utilisation !