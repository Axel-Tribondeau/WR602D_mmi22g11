
# A.T.P - Génération de PDF

**A.T.P** est un service web développé avec Symfony permettant de générer des fichiers PDF à partir de contenu HTML, d'URL, et de fichiers Office (Word, Excel, PowerPoint).

## Fonctionnalités

### **Connexion et Inscription**

- **Connexion** : L'utilisateur peut se connecter en utilisant son adresse e-mail et son mot de passe.
- **Inscription** : Un formulaire d'inscription est disponible pour créer un nouveau compte utilisateur avec une adresse e-mail et un mot de passe.
- **Mot de passe oublié** : L'utilisateur peut réinitialiser son mot de passe en fournissant son adresse e-mail et ses autres données.
- **Modification du mot de passe** : L'utilisateur peut modifier son mot de passe à partir de son profil une fois connecté.

### **Génération de PDF**

- **Génération de PDF à partir de HTML** : Convertissez le contenu HTML en fichier PDF avec une mise en page propre.
- **Génération de PDF à partir d'URL** : Entrez une URL et générez un PDF avec la mise en page d'origine du site.
- **Génération de PDF à partir de fichiers Office** : Téléchargez un fichier Word, Excel ou PowerPoint pour le convertir en PDF.
- **Historique des PDF générés** : Consultez et téléchargez à nouveau les PDF générés précédemment.

### **Gestion des abonnements**

- **Abonnement mensuel** : Gérez votre abonnement pour savoir combien de PDF vous pouvez générer chaque mois.
- **Limitation des PDF générés** : Les utilisateurs sont limités dans le nombre de PDF qu'ils peuvent générer en fonction de leur abonnement. Par exemple, un abonnement de base peut permettre la génération de 3 PDF par mois, tandis qu'un abonnement premium en permet 150.
- **Gestion de l'usage** : L'application suit la quantité de PDF générés par chaque utilisateur et bloque la génération lorsque le quota mensuel est atteint.

### **Recherche et Filtrage**

- **Recherche dans l'historique** : Recherchez les PDF générés précédemment par date, titre ou type de fichier.

### **Authentification**

- **Gestion de la session** : L'accès à l'application est sécurisé par l'authentification de l'utilisateur. Les utilisateurs doivent se connecter pour accéder aux fonctionnalités de gestion des PDF.

## Technologies utilisées

- **Symfony** : Framework PHP pour le développement backend.
- **Twig** : Moteur de template pour la gestion des vues HTML.
- **Docker** : Containerisation pour faciliter le déploiement et la gestion des environnements.
- **MariaDB** : Base de données relationnelles.
- **Cypress** : Pour les tests de bout en bout du service.
- **PHPUnit** : Pour les tests unitaires du code Symfony.

## Installation

### **Étapes d'installation**

1. **Clonez le dépôt sur votre machine locale** :
   ```bash
   git clone https://github.com/Axel-Tribondeau/WR602D_mmi22g11.git
   cd WR602D_mmi22g11
   ```

2. **Installez les dépendances** :
   ```bash
   composer install
   ```

3. **Configurez Docker pour Symfony** : Assurez-vous que votre fichier `docker-compose.yml` est configuré comme suit :
   
   ```yaml
   version: '3.8'
   services:
     web:
       image: mmi3docker/symfony-2024
       container_name: symfonyS6-web
       hostname: symfonyS6-web
       restart: always
       ports:
         - 8319:80
       depends_on:
         - db
       volumes:
         - ./www/:/var/www/
         - ./sites/:/etc/apache2/sites-enabled/

     db:
       image: mariadb:10.8
       container_name: symfonyS6-db
       hostname: symfonyS6-db
       restart: always
       volumes:
         - db-volume:/var/lib/mysql
       environment:
         MYSQL_ROOT_PASSWORD: PASSWORD
         MYSQL_DATABASE: symfony
         MYSQL_USER: symfony
         MYSQL_PASSWORD: PASSWORD

     phpmyadmin:
       image: phpmyadmin/phpmyadmin
       container_name: symfonyS6-adminsql
       hostname: symfonyS6-adminsql
       restart: always
       ports:
         - 8080:80
       environment:
         PMA_HOST: db
         MYSQL_ROOT_PASSWORD: PASSWORD
         MYSQL_USER: symfony
         MYSQL_PASSWORD: PASSWORD
         MYSQL_DATABASE: symfony

     maildev:
       image: maildev/maildev
       container_name: symfonyS6-mail
       hostname: symfonyS6-mail
       command: bin/maildev --web 1080 --smtp 1025 --hide-extensions STARTTLS
       restart: always
       ports:
         - 1080:1080

     gotenberg:
       container_name: gotenberg
       hostname: gotenberg
       image: gotenberg/gotenberg:8
       restart: always
       ports:
         - "3000:3000"

   volumes:
     db-volume:
   ```

4. **Démarrez le conteneur Docker** :
   ```bash
   docker-compose up -d
   ```

5. **Accédez au terminal du conteneur Symfony** :
   ```bash
   docker exec -ti symfonyS6-web /bin/bash
   ```

6. **Chargez les fixtures dans la base de données** :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Utilisation

### **Rapport**

1. Ouvrez la page de connexion et entrez vos identifiants pour l'application.
2. En cas de nouvel utilisateur, utilisez le formulaire d'inscription.

### **Sélectionnez un abonnement**

Une fois connecté, sélectionnez l'abonnement souhaité dans la section **Gestion des Abonnements**. Vous pourrez choisir entre plusieurs types d'abonnement (par exemple : "Medium", "Premium", etc.), chacun ayant un nombre de PDF que vous pouvez générer par mois.

### **Génération de PDF**

- **HTML vers PDF** : Utilisez le formulaire pour entrer du contenu HTML et le convertir en PDF.
- **URL vers PDF** : Entrez une URL de site web pour générer un PDF.
- **Fichier vers PDF** : Téléchargez un fichier Word, Excel ou PowerPoint pour le convertir en PDF.

### **Historique des PDF générés**

Consultez l'historique des PDF générés et téléchargez à nouveau les fichiers générés.

### **Gestion des abonnements**

Gérez votre abonnement pour savoir combien de PDFs vous pouvez générer ce mois-ci. Un suivi est effectué pour limiter le nombre de PDF générés en fonction de votre plan d'abonnement.

### **Mot de passe oublié et modification**

- Si vous avez oublié votre mot de passe, utilisez la fonctionnalité **Mot de passe oublié**.
- Modifiez votre mot de passe à partir de votre profil une fois connecté.

## Tests E2E

Le projet inclut des tests pour assurer la qualité du service, notamment avec **Cypress** pour les tests de bout en bout et **PHPUnit** pour les tests unitaires. Pour exécuter les tests de bout en bout, vous pouvez utiliser la commande suivante :

```bash
npx cypress run
```

Cela exécutera les tests de l'interface utilisateur pour vérifier le bon fonctionnement de l'application.

