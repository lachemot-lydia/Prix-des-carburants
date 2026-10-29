<?php

declare(strict_types=1);
/**
 * @file proximite-villes.php
 * @brief Page d'affichage des stations à proximité d'une ville sélectionnée.
 *
 * Reçoit la région, le département et la ville depuis la carte interactive,
 * récupère les coordonnées GPS de la ville depuis le fichier villes.csv,
 * puis affiche les stations dans un rayon configurable (5, 10, 20 ou 50 km).
 * Permet de filtrer par type de carburant, de trier par prix croissant,
 * de paginer les résultats par tranches de 5 et de consulter
 * le détail d'une station individuelle.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");
//vérifier qu'une région est bien séléctionnée
$regionSelectionnee = (isset($_GET['region']) && $_GET['region'] !== '') ? $_GET['region'] : null;

//vérifier qu'un département est bien séléctionnée 
$departementSelectionnee = (isset($_GET['departement']) && $_GET['departement'] !== '') ? $_GET['departement'] : null;

//vérifier qu'une ville est bien séléctionnée
//$_GET retroune un ID 
$villeSelectionnee = (isset($_GET['ville']) && $_GET['ville'] !== '') ? $_GET['ville'] : null;

//vérifier si un carburant est bien sélectionné
$carburantsSelectionnes = (isset($_GET['carburant']) && is_array($_GET['carburant']) && !empty($_GET['carburant'])) ? $_GET['carburant'] : null;

$action = $_GET['action'] ?? null;

$rayon = isset($_GET['rayon']) ? (int)$_GET['rayon'] : 10;
$rayon = in_array($rayon, [5, 10, 20, 50]) ? $rayon : 10;

$villes = [];
$codeDepartement = null;
//verifier qu'un département est selectionné
if ($departementSelectionnee) {
    preg_match('/\(([^)]+)\)/', $departementSelectionnee, $code);
    $codeDepartement = $code[1] ?? null;
    if ($codeDepartement !== null) {
        $villes = extractVilles($codeDepartement);
    }
}

//ENVIRONS
$latitudeRecherche = null;
$longitudeRecherche = null;
$stationsProchesVilles = [];

if ($villeSelectionnee && !empty($villes ?? [])) {
    foreach ($villes as $ville) {
        if ($ville['id'] == $villeSelectionnee) {
            $latitudeRecherche = (float)$ville['gps_lat'];
            $longitudeRecherche = (float)$ville['gps_lng'];
        }
    }
}

if ($latitudeRecherche !== null) {
    $stationsProchesVilles = stationsProximite($latitudeRecherche, $longitudeRecherche, $rayon);
    if ($carburantsSelectionnes !== null) {
        $stationsProchesVilles = filtrerParCarburant($stationsProchesVilles, $carburantsSelectionnes);
    }
    $stationsProchesVilles = trierParPrix($stationsProchesVilles, $carburantsSelectionnes ?? ['SP95', 'SP98', 'Gazole', 'E10', 'E85', 'GPLc']);
}
$totalStations = 0;
$limite = 5;
$stations = $stationsProchesVilles['results'] ?? [];
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$stationsAffichees = array_slice($stations, 0, $limite);
$totalStations = count($stations);


// Récupère les moyennes (cache XML, appel API seulement si besoin)
$xmlPath = __DIR__ . '/data/tendance.xml';
[$xml, $entrees] = getEntreesTriees($xmlPath);
$derniereDate = array_key_first($entrees);
$moyennesApi = ($derniereDate !== date('Y-m-d')) ? prixMoyensNationaux() : [];
$moyennesNationales = getPrixMoyens($xmlPath, $moyennesApi);

$titre = "Voir les prix du carburant";
$lang = "fr";
require_once("./include/header.inc.php");
?>


<h1>Stations à proximité d'une ville</h1>

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
    <span class="etape <?= $villeSelectionnee ? 'etape-done' : ($departementSelectionnee ? 'etape-active' : '') ?>">
        <?= $villeSelectionnee ? '3. ' . h(nomVilleId($villeSelectionnee)) : '3. Ville' ?>
    </span>
    <span class="etape-sep"><a href="carte.php?region=<?= urlencode($regionSelectionnee ?? '') ?>&amp;departement=<?= urlencode($departementSelectionnee ?? '') ?>&amp;ville=<?= urlencode($villeSelectionnee ?? '') ?>"
            class="derniere-visite-retour">Retourner sur la recherche</a></span>
</div>


<form action="proximite-villes.php" method="GET">
    <input type="hidden" name="region" value="<?= h((string)$regionSelectionnee) ?>" />
    <input type="hidden" name="departement" value="<?= h((string)$departementSelectionnee) ?>" />
    <input type="hidden" name="ville" value="<?= h((string)$villeSelectionnee) ?>" />
    <input type="hidden" name="action" value="proximiteVille" />

    <div id="filtres-form">

        <div id="carbu-Rech">
            <label for="rayon">Rayon de recherche :</label>
            <select name="rayon" id="rayon">
                <?php foreach ([5, 10, 20, 50] as $r): ?>
                    <option value="<?= $r ?>" <?= ($rayon === $r) ? 'selected="selected"' : '' ?>>
                        <?= $r ?> km
                    </option>
                <?php endforeach; ?>
            </select>

            <?php $carburants = ['SP95', 'SP98', 'Gazole', 'E10', 'E85', 'GPLc']; ?>
            <fieldset>
                <legend>Type de carburant :</legend>
                <div class="carbu-options">
                    <div class="carbu-options">
                        <?php foreach ($carburants as $carburant): ?>
                            <label>
                                <input type="checkbox" name="carburant[]" value="<?= h($carburant) ?>"
                                    <?= (is_array($carburantsSelectionnes) && in_array($carburant, $carburantsSelectionnes)) ? 'checked="checked"' : '' ?> />
                                <?= h($carburant) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </fieldset>
            <button type="submit" name="action" value="choixCarbuPro">Valider</button>


        </div>
    </div>

</form>

<section id="proximite">
    <?php if (!empty($stationsProchesVilles['results'])): ?>
        <h2>Ville : <?= h(nomVilleId($villeSelectionnee)) ?> </h2>
        <div id="liste-stations-proches">
            <div class="legende-prix">
                <span class="legende-item"><span class="point prix-bas">●</span> Bon prix</span>
                <span class="legende-item"><span class="point prix-moyen">●</span> Prix moyen</span>
                <span class="legende-item"><span class="point prix-haut">●</span> Prix élevé</span>
                <small>Par rapport à la moyenne nationale du jour</small>
            </div>
            <?php
            foreach ($stationsAffichees as $index => $station) { ?>
                <div class="station-proche" id="station-<?= h((string)$station['id']) ?>">

                    


                    <div class="station-proche-adresse-ligne">
                        <span class="station-proche-adresse"><?= h($station['adresse'] ?? '') ?></span>
                        <span class="station-proche-ville"><?= h($station['ville'] ?? '') ?></span>
                        <form action="proximite-villes.php" method="GET">
                            <input type="hidden" name="region" value="<?= h((string)$regionSelectionnee) ?>" />
                            <input type="hidden" name="departement" value="<?= h((string)$departementSelectionnee) ?>" />
                            <input type="hidden" name="ville" value="<?= h((string)$villeSelectionnee) ?>" />

                            <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                                    <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>" />
                            <?php endforeach;
                            endif; ?>

                            <input type="hidden" name="station" value="<?= h((string)$station['id'] ?? '') ?>" />
                            <input type="hidden" name="rayon" value="<?= $rayon ?>" />
                            <input type="hidden" name="limite" value="<?= $limite ?>" />
                            <?php $estOuverte = $action === 'voirPlus' && isset($_GET['station']) && (string)$_GET['station'] === (string)$station['id']; ?>

                            <?php if ($estOuverte): ?>
                                <button type="submit" name="action" value="voirMoins">Voir moins</button>
                            <?php else: ?>
                                <input type="hidden" name="station" value="<?= h((string)$station['id'] ?? '') ?>" />
                                <button type="submit" name="action" value="voirPlus">Voir les détails</button>
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
                    <?php if ($action === 'voirPlus' && (string)$_GET['station'] === (string)$station['id']): ?>
                        <?= afficherDetailsStation($station, $carburantsSelectionnes) ?>
                    <?php endif; ?>

                    <a href="https://www.google.fr/maps/search/<?= urlencode($station['adresse'] . ' ' . $station['ville']) ?>"
                        target="_blank" rel="noopener noreferrer" title="Voir l'itinéraire vers <?= h($station['adresse'] . ' ' . $station['ville']) ?>">
                        <img src="./images/map.png" alt="logo itinéraire" />
                    </a>


                </div>
            <?php } ?>
            <?php if ($limite < $totalStations): ?>
                <form method="GET">
                    <input type="hidden" name="region" value="<?= h((string)$regionSelectionnee) ?>" />
                    <input type="hidden" name="departement" value="<?= h((string)$departementSelectionnee) ?>" />
                    <input type="hidden" name="ville" value="<?= h((string)$villeSelectionnee) ?>" />
                    <input type="hidden" name="action" value="proximiteVille" />
                    <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                            <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>">
                    <?php endforeach;
                    endif; ?>
                    <input type="hidden" name="rayon" value="<?= $rayon ?>" />
                    <?php $restantes = min(5, $totalStations - $limite); ?>
                    <input type="hidden" name="limite" value="<?= min($limite + 5, $totalStations) ?>" />
                    <button type="submit" name="action" value="afficherPlus" class="btn-load-more" id="btn-load-plus">
                        Afficher <?= $restantes ?> station<?= $restantes > 1 ? 's' : '' ?> de plus
                    </button>
                </form>
            <?php endif; ?>

        </div>
        <?php if (($action == 'proximiteVille' || $action == 'voirPlus') && $villeSelectionnee && $latitudeRecherche === null): ?>
            <p class="warning">Impossible de déterminer la position GPS de cette ville.</p>
        <?php endif; ?>
    <?php elseif ($latitudeRecherche !== null): ?>
        <p class="warning">Aucune station proposant ce carburant trouvée à proximité. Essayez d'élargir le rayon de recherche.</p>
    <?php endif; ?>
</section>



<?php if (($action === 'voirPlus' || $action == 'voirMoins') && isset($_GET['station'])): ?>
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