# snowtricks

InsightSymfony : https://insight.symfony.com/projects/95e180b7-5753-4026-8a75-28a959bd1c33/analyses/22

## Installation
1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :
```
    git clone https://github.com/ThomasLargilliere/snowtricks.git
```
2. Editez le fichier .env pour notamment mettre à jour DATABASE_URL ainsi que MAILER_DSN vous pouvez aussi mettre APP_ENV=prod à la place de dev.

3. Installer les dépendances avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4. Pour créer la base de donnée avec la commande suivante :
```
    php bin/console doctrine:database:create
```
5. Pour créer les tables taper la commande suivante :
```
    php bin/console doctrine:migrations:migrate
```
6. Pour lancer le serveur utiliser la commande suivante :
```
    symfony server:start
```
N'oublier pas de lancer aussi votre base de donnée via XAMPP, MAMP ou WAMP
OPTIONNEL :

1. Utiliser un jeu de données crées :
```
    php bin/console doctrine:fixtures:load
```
2. Se connecter :
```
    Email : test@test.fr
    Mot de passe : test
```
