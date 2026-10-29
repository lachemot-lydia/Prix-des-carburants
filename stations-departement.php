<?php

declare(strict_types=1);
/**
 * @file stations-departement.php
 * @brief Page d'affichage de toutes les stations d'un département sélectionné.
 *
 * Reçoit la région et le département depuis la carte interactive,
 * extrait le code département et récupère toutes les stations via l'API
 * gouvernementale. Permet de filtrer par type de carburant, de trier
 * par prix croissant, de paginer les résultats par tranches de 5
 * et de consulter le détail d'une station individuelle.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");


$regionSelectionnee = (isset($_GET['region']) && $_GET['region'] !== '') ? $_GET['region'] : null;
//vérifier si un carburant est bien sélectionné
$carburantsSelectionnes = (isset($_GET['carburant']) && is_array($_GET['carburant']) && !empty($_GET['carburant'])) ? $_GET['carburant'] : null;

$action = $_GET['action'] ?? null;

$rayon = isset($_GET['rayon']) ? (int)$_GET['rayon'] : 10;
$rayon = in_array($rayon, [5, 10, 20, 50]) ? $rayon : 10;


//vérifier qu'un département est bien séléctionnée 
$departementSelectionnee = (isset($_GET['departement']) && $_GET['departement'] !== '') ? $_GET['departement'] : null;

$stationsDepartement = [];
$codeDepartement = null;
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$totalStations = 0;
$stationsAffichees = [];

//verifier qu'un département est selectionné
if (!$departementSelectionnee) {
    $erreur = "Aucun département sélectionné.";
} else {

    // Extraction du code département
    preg_match('/\(([^)]+)\)/', $departementSelectionnee, $code);
    $codeDepartement = $code[1] ?? null;


    if ($codeDepartement === null) {
        $erreur = "Impossible d'extraire le code du département.";
    } else {
        $stationsDepartement = stationsDepartement($codeDepartement);

        if (empty($stationsDepartement['results'])) {
            $erreur = "Aucune station trouvée dans ce département.";
        }
        if ($carburantsSelectionnes !== null) {
            $stationsDepartement = filtrerParCarburant($stationsDepartement, $carburantsSelectionnes);


            if (empty($stationsDepartement['results'])) {
                $erreur = "Aucune station ne propose le carburant sélectionné dans ce département.";
            }
        }
        $stationsDepartement = trierParPrix($stationsDepartement, $carburantsSelectionnes ?? ['SP95', 'SP98', 'Gazole', 'E10', 'E85', 'GPLc']);

        $stations = $stationsDepartement['results'] ?? [];
        $stationsAffichees = array_slice($stations, 0, $limite);
        $totalStations = count($stations);
    }
}


// Récupère les moyennes (cache XML, appel API seulement si besoin)
$xmlPath = __DIR__ . '/data/tendance.xml';
[$xml, $entrees] = getEntreesTriees($xmlPath);
$derniereDate = array_key_first($entrees);
$moyennesApi = ($derniereDate !== date('Y-m-d')) ? prixMoyensNationaux() : [];
$moyennesNationales = getPrixMoyens($xmlPath, $moyennesApi);


$titre = "Voir toutes les stations du département";
$lang = "fr";
require_once("./include/header.inc.php");

?>

<h1>Stations d'un département sélectionné</h1>

<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>

<div id="etapes">
    <span class="etape <?= $regionSelectionnee ? 'etape-done' : 'etape-active' ?>">
        <?= $regionSelectionnee ? '1. ' . h($regionSelectionnee) : '1. Région' ?>
    </span>
    <span class="etape-sep">›</span>
    <span class="etape <?= $departementSelectionnee ? 'etape-done' : ($regionSelectionnee ? 'etape-active' : '') ?>">
        <?= $departementSelectionnee ? '2. ' . h($departementSelectionnee) : '2. Département' ?>
    </span>
    <span class="etape-sep">›</span>
    <span class="etape-sep"><a href="carte.php?region=<?= urlencode($regionSelectionnee ?? '') ?>&amp;departement=<?= urlencode($departementSelectionnee ?? '') ?>"
            class="derniere-visite-retour">Retourner sur la recherche</a></span>
</div>

<form action="stations-departement.php" method="GET">
    <input type="hidden" name="region" value="<?= h($regionSelectionnee ?? '') ?>" />
    <input type="hidden" name="departement" value="<?= h($departementSelectionnee ?? '') ?>" />
    <div id="carbu-Rech-autre">
        <?php $carburants = ['SP95', 'SP98', 'Gazole', 'E10', 'E85', 'GPLc']; ?>
        <fieldset>
            <legend>Type de carburant :</legend>
            <div class="carbu-options">
                <?php foreach ($carburants as $carburant): ?>
                    <label>
                        <input type="checkbox" name="carburant[]" value="<?= h($carburant) ?>"
                            <?= (is_array($carburantsSelectionnes) && in_array($carburant, $carburantsSelectionnes)) ? 'checked="checked"' : '' ?> />
                        <?= h($carburant) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <button type="submit" name="action" value="choixCarbuPro">Valider</button>


    </div>
</form>

<?php if (!empty($stationsDepartement['results'] ?? [])): ?>
    <section id="stations-departement">
        <h2>Département : <?= h($departementSelectionnee ?? '') ?></h2>

        <div id="stations-depar">
            <div class="legende-prix">
                <span class="legende-item"><span class="point prix-bas">●</span> Bon prix</span>
                <span class="legende-item"><span class="point prix-moyen">●</span> Prix moyen</span>
                <span class="legende-item"><span class="point prix-haut">●</span> Prix élevé</span>
                <small>Par rapport à la moyenne nationale du jour</small>
            </div>
            <?php foreach ($stationsAffichees as $index => $station) { ?>
                <div class="station-proche" id="station-<?= h((string)$station['id']) ?>">


                    <div class="station-proche-adresse-ligne">
                        <span class="station-proche-adresse"><?= h($station['adresse'] ?? '') ?></span>
                        <span class="station-proche-ville"><?= h($station['ville'] ?? '') ?></span>
                        <form action="stations-departement.php" method="GET">
                            <input type="hidden" name="departement" value="<?= h((string)$departementSelectionnee) ?>" />
                            <input type="hidden" name="region" value="<?= h($regionSelectionnee ?? '') ?>" />

                            <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                                    <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>" />
                            <?php endforeach;
                            endif; ?>

                            <input type="hidden" name="station" value="<?= h((string)$station['id']) ?? '' ?>" />
                            <input type="hidden" name="limite" value="<?= $limite ?>" />
                            <?php $estOuverte = $action === 'voirPlus' && isset($_GET['station']) && (string)$_GET['station'] === (string)$station['id']; ?>

                            <?php if ($estOuverte): ?>
                                <button type="submit" name="action" value="voirMoins" style="margin-left: auto; display: block;">Voir moins</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="voirPlus" style="margin-left: auto; display: block;">Voir les détails</button>
                            <?php endif; ?>
                        </form>
                    </div>


                    <div class="station-proche-prix">
                        <?php foreach ($station['carburants_disponibles'] ?? [] as $carburant):
                            $prix = $station[strtolower($carburant) . "_prix"] ?? null; ?>
                            <?php if ($carburantsSelectionnes === null || in_array($carburant, $carburantsSelectionnes)): ?>
                                <span class="prix-tag">
                                    <?= h($carburant) ?> :
                                    <?php if ($prix !== null && $prix !== ''):
                                        $classe = classePrix((float)$prix, $carburant, $moyennesNationales); ?>
                                        <strong class="<?= $classe ?>"><?= h((string)$prix) ?> €/L</strong>
                                    <?php else: ?>
                                        n/d
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($action === 'voirPlus' && isset($_GET['station']) && (string)$_GET['station'] === (string)$station['id']): ?>
                        <?= afficherDetailsStation($station, $carburantsSelectionnes) ?>
                    <?php endif; ?>

                    <a href="https://www.google.fr/maps/search/<?= urlencode($station['adresse'] . ' ' . $station['ville']) ?>"
                        target="_blank" rel="noopener noreferrer" title="Voir l'itinéraire vers <?= h($station['adresse'] . ' ' . $station['ville']) ?>">
                        <img src="./images/map.png" alt="logo itinéraire" />
                    </a>

                </div>
            <?php } ?>
            <?php if ($limite < $totalStations): ?>
                <form method="GET" action="stations-departement.php">
                    <input type="hidden" name="departement" value="<?= h($departementSelectionnee ?? '') ?>" />

                    <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                            <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>" />
                    <?php endforeach;
                    endif; ?>

                    <?php $restantes = min(5, $totalStations - $limite); ?>
                    <input type="hidden" name="limite" value="<?= min($limite + 5, $totalStations) ?>" />
                    <button type="submit" name="action" value="afficherPlus" class="btn-load-more" id="btn-load-plus">
                        Afficher <?= $restantes ?> station<?= $restantes > 1 ? 's' : '' ?> de plus
                    </button>
                </form>
            <?php endif; ?>


        </div>
    </section>
<?php else: ?>
    <?php if (!empty($erreur)): ?>
        <p class="warning"><?= h($erreur) ?></p>
    <?php else: ?>
        <p class="warning">Aucune station dans ce département.</p>
    <?php endif; ?>
<?php endif; ?>


<?php if (($action === 'voirPlus' || $action === 'voirMoins') && isset($_GET['station'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('station-<?= h((string)$_GET['station']) ?>');
            if (el) {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>
<?php endif; ?>
<?php if ($action === 'afficherPlus'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-load-plus');
            if (btn) {
                btn.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>

<?php endif; ?>


<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>