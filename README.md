# Projet Symfony avec Docker

Ce projet utilise **Docker Compose** pour gérer un environnement Symfony avec une base de données.

## 📌 Prérequis

Avant de démarrer, assurez-vous d'avoir installé :

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## 🚀 Installation et démarrage du projet

### 1️⃣ Copier le fichier `.env.dist` vers `.env`
Le fichier `.env` contient les variables d'environnement nécessaires au projet.

```sh
make copy-env
```

### 2️⃣ Démarrer le projet
Pour lancer le projet avec Docker :

```sh
make start
```

Cela effectuera les actions suivantes :
- Lancer les conteneurs en arrière-plan.
- Créer la base de données si elle n'existe pas.
- Appliquer les migrations.
- Charger les données de test (fixtures).

### 3️⃣ Arrêter le projet
Pour arrêter les conteneurs :

```sh
make stop
```

## 🛠 Commandes utiles

### 📖 Afficher l'aide des commandes disponibles
```sh
make help
```

### 🛡 Vérifications et corrections
- **Analyse du code avec PHPStan** :
  ```sh
  make phpstan
  ```
- **Correction du code avec PHP-CS-Fixer** :
  ```sh
  make php-cs-fixer
  ```
- **Exécuter les tests avec PHPSpec** :
  ```sh
  make phpspec
  ```

👨‍💻 **Happy coding!**
