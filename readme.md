# Projet_PleinPasCher
 
Site de consultation des prix des carburants en France métropolitaine.
 
## Auteurs
 
- Lydia **LACHEMOT**
- Thomas **LENGLIN**
  
Licence 2 Informatique - Semestre 4  
UE : Développement web  
Année universitaire : 2025–2026  
Établissement : CY Cergy Paris Université
 
## URL du site
 
- **Site en ligne :**
https://lachemot.alwaysdata.net 
https://lenglin.alwaysdata.net 

## Documentation

- **Documentation en ligne :**
https://lachemot.alwaysdata.net/doc
https://lenglin.alwaysdata.net/doc

## Description
 
PleinPasCher permet de trouver les stations-service les moins chères près de chez soi en France métropolitaine. Les prix sont récupérés en temps réel depuis l'API officielle du gouvernement français.
 
### Fonctionnalités principales
 
- Recherche de stations par région, département et ville via une carte interactive
- Détection automatique de la position de l'utilisateur par adresse IP
- Filtrage par type de carburant (SP95, SP98, Gazole, E10, E85, GPLc)
- Statistiques des villes les plus consultées
- Mode clair / mode sombre

## Sources des données
 
- [API Prix des carburants — data.economie.gouv.fr](https://data.economie.gouv.fr) — Licence Ouverte Etalab v2.0
- [ip2location.io](https://api.ip2location.io) — Géolocalisation par IP
- [ipinfo.io](https://ipinfo.io) — Géolocalisation par IP
- [Ghibli API](https://ghibliapi.vercel.app) — Films Studio Ghibli (page technique)
- Carte administrative des régions de France — IGN 2016, Licence ouverte

## Structure du projet
 
```
projet-php/
├── index.php                   — Accueil
├── carte.php                   — Carte interactive
├── proximite-departement.php   — Stations près de moi
├── proximite-villes.php        — Stations à proximité d'une ville
├── stations-departement.php    — Stations par département
├── stations-villes.php         — Stations par ville
├── statistiques.php            — Statistiques de consultation
├── tech.php                    — Page technique (Ghibli + géolocalisation)
├── mentions.php                — Mentions légales
├── confidentialite.php         — Politique de confidentialité
├── plan.php                    — Plan du site
├── include/
│   ├── functions.inc.php       — Toutes les fonctions PHP
│   ├── header.inc.php          — En-tête commun
│   ├── footer.inc.php          — Pied de page commun
│   └── key/
│       └── config.php          — Clés API ( pour xml)
├── style/
│   ├── style.css               — Thème clair
│   └── style-alternatif.css    — Thème sombre
├── data/
│   ├── regions.csv
│   ├── departements.csv
│   └── villes.csv
├── stockage/
│   ├── consultations.csv       — Statistiques anonymes
│   ├── visiteurs.txt           — Compteur de visiteurs
│   └── cache/                  — Cache des appels API
└── images/
```
 
## Installation locale
 
1. Déposer le projet dans le dossier `www` de WAMP / XAMPP
3. Accéder au site via `http://localhost/"nom du dossier"/`

## Hébergement
- **Hébergeur :** [Alwaysdata](https://www.alwaysdata.com)
 
