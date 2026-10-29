<?php

declare(strict_types=1);
/**
 * @file proximite-departement.php
 * @brief Page d'affichage des stations à proximité de la position détectée par IP.
 *
 * Géolocalise automatiquement le visiteur via son adresse IP (ip2location.io),
 * puis affiche les stations-service dans un rayon configurable (5, 10, 20 ou 50 km)
 * au sein de son département. Permet de filtrer par type de carburant,
 * de trier par prix croissant, de paginer les résultats par tranches de 5
 * et de consulter le détail d'une station individuelle.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");


//vérifier si un carburant est bien sélectionné
$carburantsSelectionnes = (isset($_GET['carburant']) && is_array($_GET['carburant']) && !empty($_GET['carburant'])) ? $_GET['carburant'] : null;

$action = $_GET['action'] ?? null;

$rayon = isset($_GET['rayon']) ? (int)$_GET['rayon'] : 10;
$rayon = in_array($rayon, [5, 10, 20, 50]) ? $rayon : 10;

$codeDeptIp = null;
$stationsProchesDepar = [];
$latitudeRecherche = null;
$longitudeRecherche = null;

$geoIp = getIpGeoJSON("https://api.ip2location.io/?ip=" . (string)getIPVisiteur() . "&format=json");

$nomDepartement = $geoIp['city_name'] ?? $codeDeptIp;
$nomRegion = $geoIp['region_name'] ?? '';

if (empty($geoIp)) {
    $erreurGeo = "Impossible de récupérer votre position (service IP indisponible).";
} elseif (empty($geoIp['zip_code'])) {

    $erreurGeo = "Votre code postal n’a pas pu être déterminé.";
} else {
    $codeDeptIp = substr((string)$geoIp['zip_code'], 0, 2);
    $latitudeRecherche = (float)$geoIp['latitude'];
    $longitudeRecherche = (float)$geoIp['longitude'];
}

if ($latitudeRecherche !== null) {

    $stationsProchesDepar = stationsDepartementProximite($latitudeRecherche, $longitudeRecherche, $codeDeptIp, $rayon);
    if ($carburantsSelectionnes !== null) {
        $stationsProchesDepar = filtrerParCarburant($stationsProchesDepar, $carburantsSelectionnes);
    }
    $stationsProchesDepar = trierParPrix($stationsProchesDepar, $carburantsSelectionnes ?? ['SP95', 'SP98', 'Gazole', 'E10', 'E85', 'GPLc']);
}
$totalStations = 0;

$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$stations = $stationsProchesDepar['results'] ?? [];

$stationsAffichees = array_slice($stations, 0, $limite);
$totalStations = count($stations);

// Récupère les moyennes (cache XML, appel API seulement si besoin)
$xmlPath = __DIR__ . '/data/tendance.xml';
[$xml, $entrees] = getEntreesTriees($xmlPath);
$derniereDate = array_key_first($entrees);
$moyennesApi = ($derniereDate !== date('Y-m-d')) ? prixMoyensNationaux() : [];
$moyennesNationales = getPrixMoyens($xmlPath, $moyennesApi);


$titre = "Voir les stations près de moi";
$lang = "fr";
require_once("./include/header.inc.php");

?>

<h1>Stations à proximité de votre position</h1>

<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>


<?php if (!empty($erreurGeo)): ?>
    <p class="error"><?= h($erreurGeo) ?></p>
<?php endif; ?>

<?php if ($latitudeRecherche === null || $longitudeRecherche === null): ?>
    <p class="warning">La localisation n’a pas pu être déterminée. Impossible d’afficher les stations proches.</p>
    <p class="info">Vous pouvez rechercher manuellement via la carte interactive :
        <a href="carte.php" class="derniere-visite-retour">Rechercher sur la carte</a>
    </p>
<?php endif; ?>

<form action="proximite-departement.php" method="GET">

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
    </div>
</form>



<?php if (!empty($stationsProchesDepar['results'] ?? [])): ?>
    <section id="stations-dept-ip">
        <h2>Département <?= h($codeDeptIp) ?> _ <?= h($nomDepartement) ?>_<?= h($nomRegion) ?></h2>
        <div id="liste-stations-dept-ip">
            <div class="legende-prix">
                <span class="legende-item"><span class="point prix-bas">●</span> Bon prix</span>
                <span class="legende-item"><span class="point prix-moyen">●</span> Prix moyen</span>
                <span class="legende-item"><span class="point prix-haut">●</span> Prix élevé</span>
                <small>Par rapport à la moyenne nationale du jour</small>
            </div>
            <?php foreach ($stationsAffichees as $index => $station): ?>
                <div class="station-proche" id="station-<?= h((string)$station['id']) ?>">

                
                    <div class="station-proche-adresse-ligne">
                        <span class="station-proche-adresse"><?= h($station['adresse'] ?? '') ?></span>
                        <span class="station-proche-ville"><?= h($station['ville'] ?? '') ?></span>

                        <form action="proximite-departement.php" method="GET">


                            <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                                    <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>" />
                            <?php endforeach;
                            endif; ?>

                            <input type="hidden" name="station" value="<?= h((string)$station['id']) ?? '' ?>" />
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



                    <?php if (!empty($station['carburants_disponibles'])): ?>
                        <div class="station-proche-prix">

                            <?php foreach ($station['carburants_disponibles'] as $carburant):
                                $prix = $station[strtolower($carburant) . "_prix"] ?? null;
                            ?>
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
                    <?php else: ?>
                        <p>Aucun carburant disponible</p>
                    <?php endif; ?>
                    <?php if ($action === 'voirPlus' && (string)$_GET['station'] === (string)$station['id']): ?>
                        <?= afficherDetailsStation($station, $carburantsSelectionnes) ?>
                    <?php endif; ?>

                    <a href="https://www.google.fr/maps/search/<?= urlencode($station['adresse'] . ' ' . $station['ville']) ?>"
                        target="_blank" rel="noopener noreferrer" title="Voir l'itinéraire vers <?= h($station['adresse'] . ' ' . $station['ville']) ?>">
                        <img src="./images/map.png" alt="logo itinéraire" />
                    </a>

                </div>


            <?php endforeach; ?>
            <?php if ($limite < $totalStations): ?>
                <form method="GET">
                    <?php if ($carburantsSelectionnes): foreach ($carburantsSelectionnes as $carbu): ?>
                            <input type="hidden" name="carburant[]" value="<?= h($carbu) ?>" />
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
    </section>

<?php elseif ($latitudeRecherche !== null):  ?>
    <p class="warning">Aucune station proposant ce carburant trouvée à proximité. Essayez d'élargir le rayon de recherche.</p>
<?php endif; ?>


<?php if (in_array($action, ['voirPlus', 'voirMoins']) && isset($_GET['station'])): ?><script>
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