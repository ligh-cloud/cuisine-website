# Projet : Site Web pour un Chef Cuisinier 

## Contexte du Projet

Ce projet vise à développer un site web pour un chef cuisinier mondialement reconnu, offrant une expérience gastronomique unique. Les utilisateurs peuvent découvrir des menus exclusifs, réserver des expériences culinaires à domicile et interagir avec le chef.

## Objectifs du Projet

### Site Web avec Multi-Rôles :

#### Utilisateurs (Clients) :
- Découvrir les menus proposés par le chef.
- S’inscrire, se connecter, et réserver une expérience culinaire.

#### Chefs (Administrateurs) :
- Se connecter et gérer les réservations (accepter, refuser, consulter les statistiques des demandes, et gérer leur profil).

### Fonctionnalités Principales :

#### Inscription et Connexion :
- Les utilisateurs et chefs s’inscrivent et se connectent.
- Redirection vers des pages spécifiques en fonction de leur rôle après authentification réussie.

#### Page Utilisateur (Client) :
- Consultation des menus du chef et réservation d'une expérience culinaire (sélection de la date, heure et nombre de personnes).
- Gestion des réservations : consulter l’historique, modifier ou annuler des réservations.

#### Page Chef (Dashboard) :
- Gestion des réservations : accepter ou refuser les demandes en fonction de la disponibilité.
- Affichage des statistiques détaillées :
  - Nombre de demandes en attente.
  - Nombre de demandes approuvées pour la journée.
  - Nombre de demandes approuvées pour le jour suivant.
  - Détails du prochain client et de sa réservation.
  - Nombre de clients inscrits.

#### Design :
- Responsive Design : Compatible avec toutes les tailles d’écrans (mobile, tablette, desktop).
- Esthétique : Design moderne et élégant avec des couleurs raffinées pour représenter le luxe.
- UX/UI : Interface intuitive et agréable pour une navigation fluide.

## Fonctionnalités JavaScript

### Validation des Formulaires avec Regex :
- Validation des entrées des utilisateurs (email, téléphone, mot de passe, etc.).

### Formulaires Dynamiques d’Ajout de Menus :
- Ajout dynamique de plusieurs plats dans un menu sans recharger la page.

### Modals Dynamiques :
- Utilisation de modals pour afficher des informations sans recharger la page.

### SweetAlerts :
- Intégration de SweetAlert pour des alertes visuelles élégantes (confirmation de réservation, annulation, etc.).

## Sécurité des Données

### Hashage des Mots de Passe :
- Utilisation de techniques sécurisées pour le hashage des mots de passe.

### Protection contre les Failles XSS :
- Nettoyage et échappement des entrées utilisateurs.

### Prévention des Injections SQL :
- Utilisation de requêtes préparées.

### Protection contre les Attaques CSRF (Bonus) :
- Mise en place d’un token CSRF pour sécuriser les actions sensibles.

## Fonctionnalités Bonus

- **Génération de Documents Imprimables :** Génération de rapports imprimables sur les réservations et statistiques sous forme de PDF.
- **Envoi d’E-mails :** Réinitialisation de mot de passe, confirmation de réservation, alertes.
- **Archivage des Plats :** Marquage des plats comme archivés lorsqu’ils sont en rupture de stock.
- **Page 404 Personnalisée :** Page élégante proposant des liens vers l’accueil ou d’autres sections du site.

## Environnement de Développement

- **Serveur Local :** XAMPP
- **Langage Backend :** PHP
- **Base de Données :** MySQL
- **Frontend :** HTML, CSS (avec TailwindCSS), JavaScript
- **Bibliothèques et Frameworks JS :** SweetAlert2
- **Outils de Gestion de Version :** Git & GitHub

## Structure des Fichiers

- **Code Source :** Contient tous les fichiers nécessaires au site web.
- **Diagrammes :** Fichier des diagrammes (diagrammes UML, etc.).
- **Commandes SQL :** Fichier pour la création de la base de données, des tables, et insertion des données.
- **README :** Documentation complète du projet.

---

Merci de consulter le lien vers le repository GitHub pour accéder au code source, diagrammes, commandes SQL, et README. N’hésitez pas à poser vos questions ou à signaler des problèmes via les issues GitHub !
