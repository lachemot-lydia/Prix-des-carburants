<?php

declare(strict_types=1);
/**
 * @file plan.php
 * @brief Plan du site PleinPasCher.
 *
 * Liste l'ensemble des pages du site organisées par catégories :
 * pages principales (accueil, carte, statistiques),
 * recherche à proximité, informations techniques et pages légales
 * (mentions légales, confidentialité).
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");

$titre = "Plan du site — PleinPasCher";
$lang = "fr";

require_once("./include/header.inc.php");

?>
<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>

<div class="plan-page">
    <h1>Plan du site</h1>

    <div class="plan-categorie">
        <h2>Pages principales</h2>
        <div class="plan-liens">
            <a class="plan-lien" href="index.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Accueil</span>
                    <span class="plan-lien-desc">Page d'accueil du site</span>
                </span>
            </a>
            <a class="plan-lien" href="carte.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Carte interactive</span>
                    <span class="plan-lien-desc">Point d'entrée de toute la recherche : sélectionnez votre région, département et ville pour trouver les stations et leurs prix</span>
                </span>
            </a>
            <a class="plan-lien" href="statistiques.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Statistiques</span>
                    <span class="plan-lien-desc">Villes les plus consultées et l'évolution des prix moyens nationaux des carburants.</span>
                </span>
            </a>
        </div>
    </div>

    <div class="plan-categorie">
        <h2>Recherche à proximité</h2>
        <div class="plan-liens">
            <a class="plan-lien" href="proximite-departement.php?action=stationpres">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Stations près de moi</span>
                    <span class="plan-lien-desc">Détecte automatiquement votre position via votre adresse IP et affiche les stations à proximité</span>
                </span>
            </a>
        </div>
    </div>

    <div class="plan-categorie">
        <h2>Informations</h2>
        <div class="plan-liens">
            <a class="plan-lien" href="tech.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Page technique</span>
                    <span class="plan-lien-desc">Démonstration technique : film Ghibli aléatoire et géolocalisation par adresse IP</span>
                </span>
            </a>

        </div>
    </div>

    <div class="plan-categorie">
        <h2>Légal</h2>
        <div class="plan-liens">
            <a class="plan-lien" href="mentions.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Mentions légales</span>
                    <span class="plan-lien-desc">Éditeur du site, hébergement, sources des données et propriété intellectuelle</span>
                </span>
            </a>
            <a class="plan-lien" href="confidentialite.php">
                <span class="plan-lien-texte">
                    <span class="plan-lien-titre">Confidentialité</span>
                    <span class="plan-lien-desc">Politique de confidentialité</span>
                </span>
            </a>
        </div>
    </div>

</div>

<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>