<?php

declare(strict_types=1);
/**
 * @file index.php
 * @brief Page d'accueil du site PleinPasCher.
 *
 * Affiche le hero de présentation, la dernière ville recherchée
 * via la carte (si le cookie est présent), la section "Pourquoi PleinPasCher"
 * et les raccourcis de navigation vers les fonctionnalités principales
 * (carte interactive, proximité, statistiques).
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");


compterVisiteur(); 

$titre = "PleinPasCher";
$lang = "fr";
require_once("./include/header.inc.php");
?>



<nav id="nav-page" aria-label="Navigation de la page">
    <ul>
        <li><a href="#prix-moyen">Prix moyen du jour</a></li>
        <li><a href="#pourquoi">Pourquoi PPC ?</a></li>
        <li><a href="#nav-accueil">Actions rapides</a></li>

    </ul>
</nav>


<div id="hero">
    <div id="hero-texte">
        <h1>Faites le plein au meilleur prix</h1>
        <p>Trouvez la station la moins chère près de chez vous en France métropolitaine</p>
        <form action="proximite-departement.php" method="GET" id="hero-form">
            <button type="submit" name="action" value="stationpres">Voir les stations près de moi</button>
        </form>
    </div>
</div>

<?php
//Afficher la derniere visite de l'utilisateur sur le navigateur 
$derniereVisite = lireVilleCookie();
?>


<?php
if ($derniereVisite):
?>

    <div id="derniere-visite">
        <span class="derniere-visite-titre">Votre dernière recherche par carte</span>
        <span class="derniere-visite-ville"><?= h($derniereVisite['ville']) ?></span>
        <span class="derniere-visite-info"><?= h($derniereVisite['departement']) ?></span>
        <span class="derniere-visite-info"><?= h($derniereVisite['region']) ?></span>
        <span class="derniere-visite-info">Le <?= h($derniereVisite['date']) ?></span>
        <a href="carte.php?region=<?= urlencode($derniereVisite['region']) ?>&amp;departement=<?= urlencode($derniereVisite['departement']) ?>&amp;ville=<?= urlencode($derniereVisite['ville_id'] ?? '') ?>"
            class="derniere-visite-retour">Retourner sur cette recherche</a>
    </div>

<?php endif; ?>

<?php
$aujourd_hui = getPrixMoyens(__DIR__ . '/data/tendance.xml', prixMoyensNationaux()) ?? [];
?>

<section id="prix-moyen">
    <h2>Prix moyen du jour €/L — <?= date('d/m/Y') ?></h2>
    <?php if (!empty($aujourd_hui)): ?>
        <div class="prix-moyens-grid">
            <?php foreach ($aujourd_hui as $carburant => $prix): ?>
                <div class="prix-card">
                    <div class="prix-card-label"><?= h($carburant) ?></div>
                    <div class="prix-card-valeur"><?= number_format($prix, 3, ',', '') ?> <span>€/L</span></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune donnée disponible</p>
    <?php endif; ?>
</section>

<section id="pourquoi">
    <h2>Pourquoi PleinPasCher ?</h2>
    <div id="pourquoi-cards">
        <div class="pourquoi-card">
            <span class="pourquoi-titre">Prix en temps réel</span>
            <span class="pourquoi-desc">Les données sont mises à jour en continu directement depuis l'API officielle du gouvernement.</span>
        </div>
        <div class="pourquoi-card">
            <span class="pourquoi-titre">Toute la France</span>
            <span class="pourquoi-desc">Plus de 10 000 stations en France métropolitaine, organisées par région, département et ville.</span>
        </div>
        <div class="pourquoi-card">
            <span class="pourquoi-titre">Trouvez le moins cher</span>
            <span class="pourquoi-desc">Les stations sont triées par prix pour que vous trouviez immédiatement la moins chère.</span>
        </div>
    </div>
</section>




<section id="nav-accueil">
    <h2>Que souhaitez-vous faire ?</h2>

    <div id="nav-accueil-cards">

        <a href="carte.php" class="nav-card">
            <img src="./images/index/miniCarte.png" alt="carte-de-recherche" class="nav-card-icone-img" />
            <span class="nav-card-titre">Carte interactive</span>
            <span class="nav-card-desc">Sélectionnez votre région, département et ville pour trouver les stations.</span>
        </a>

        <a href="proximite-departement.php?action=stationpres" class="nav-card nav-card-orange">
            <img src="./images/index/proximite.png" alt="voir-aux-alentours" class="nav-card-icone-img" />
            <span class="nav-card-titre">À proximité de moi</span>
            <span class="nav-card-desc">Détecte automatiquement votre position et affiche les stations les moins chères.</span>
        </a>

        <a href="statistiques.php" class="nav-card">
            <img src="./images/index/statistique.png" alt="Stats" class="nav-card-icone-img" />
            <span class="nav-card-titre">Statistiques</span>
            <span class="nav-card-desc">Visualisez les villes les plus consultées sous forme de graphique et
                l'évolution des prix moyens nationaux des carburants.</span>
        </a>

    </div>
</section>



<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>