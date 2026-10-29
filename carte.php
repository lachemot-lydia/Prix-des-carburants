<?php

declare(strict_types=1);
/**
 * @file carte.php
 * @brief Page de recherche de stations par carte interactive.
 *
 * Permet à l'utilisateur de sélectionner une région via une carte
 * cliquable de France, puis de choisir un département et une ville
 * via des menus déroulants en cascade. Redirige ensuite vers les
 * pages de stations par département, par ville ou à proximité d'une ville.
 * Mémorise la sélection en cours via des cookies de session.
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
$carburantSelectionne = (isset($_GET['carburant']) && $_GET['carburant'] !== '') ? $_GET['carburant'] : null;

$ancienDepartement = $_COOKIE['ancien_departement'] ?? null;
$ancienneVille = $_COOKIE['ancienne_ville'] ?? null;

$action = $_GET['action'] ?? null;


$villeDepuisUrl = (isset($_GET['ville']) && $_GET['ville'] !== '') ? $_GET['ville'] : null;
//reset
if (($departementSelectionnee !== $ancienDepartement) && !$villeDepuisUrl) {
    $villeSelectionnee = null;
}

$tableauDepartements = extractRegionDepart();

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
//COOKIES
setcookie('ancien_departement', $departementSelectionnee ?? "", time() + 3600, COOKIE_PATH);
setcookie('ancienne_ville', $villeSelectionnee ?? "", time() + 3600, COOKIE_PATH);

$titre = "PleinPasCher";
$lang = "fr";
require_once("./include/header.inc.php");
?>

<h1>Carte interactive — Recherche manuelle</h1>
<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>


<section id="recherche-region">
    <h2>Recherchez par région</h2>

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
    </div>

    <div id="carte-formulaire">
        <figure>
            <img src="./images/carte/carte.jpg" usemap="#image-map" alt="Carte administrative des regions de France" />
            <figcaption>Carte administrative des régions de France - IGN 2016 - Licence ouverte</figcaption>
        </figure>

        <map name="image-map">
            <area alt="Hauts-de-France" title="Hauts-de-France" href="carte.php?region=Hauts-de-France" coords="389,95,362,144,302,131,294,77,304,18,348,37,384,69,384,84,388,90,388,94" shape="poly" />
            <area alt="Grand Est" title="Grand-Est" href="carte.php?region=Grand%20Est" coords="410,102,548,160,443,243,391,117,397,109,401,107,407,108,447,247,369,186,383,135,389,131,393,126,388,127,405,107,412,84,515,243,541,167,476,216" shape="poly" />
            <area alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhone-Alpes" href="carte.php?region=Auvergne-Rhône-Alpes" coords="344,336,444,463,336,325,323,423,447,475,502,354,410,363,341,327,411,366,341,328,344,335,467,426,500,358,517,407,470,428,434,385,442,381,388,352" shape="poly" />
            <area alt="Centre-Val de Loire" title="Centre-Val-de-Loire" href="carte.php?region=Centre-Val%20de%20Loire" coords="341,301,301,325,273,324,237,276,267,202,281,179,337,233,342,255,339,277,342,289" shape="poly" />
            <area alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="carte.php?region=Nouvelle-Aquitaine" coords="312,343,320,403,160,551,148,537,188,362,211,296,242,301,277,348,286,341,299,341,304,419,258,414,246,360" shape="poly" />
            <area alt="Occitanie" title="Occitanie" href="carte.php?region=Occitanie" coords="320,610,408,504,287,455,223,525,219,565,235,580,272,592" shape="poly" />
            <area alt="Normandie" title="Normandie" href="carte.php?region=Normandie" coords="251,190,292,94,192,142,166,118,181,175,224,182" shape="poly" />
            <area alt="Bretagne" title="Bretagne" href="carte.php?region=Bretagne" coords="54,166,176,201,125,247,54,191" shape="poly" />
            <area alt="Pays de la Loire" title="Pays-de-la-Loire" href="carte.php?region=Pays%20de%20la%20Loire" coords="169,323,218,199,138,249,137,258,142,296,154,328,251,215,187,192,183,203,180,238" shape="poly" />
            <area alt="Bourgogne-Franche-Comté" title=" Bourgogne-Franche-Comté" href="carte.php?region=Bourgogne-Franche-Comté" coords="500,258,404,244,368,216,356,239,356,305,403,341,441,320,463,335,489,299,496,272" shape="poly" />
            <area alt="Île-de-France" title="Île-de-France" href="carte.php?region=Île-de-France" coords="346,155,363,188,335,205,310,189,295,174,314,144,293,154,324,148" shape="poly" />
            <area alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="carte.php?region=Provence-Alpes-Côte%20d'Azur" coords="490,567,539,515,503,453,435,507,428,544,461,564,477,556,482,555" shape="poly" />
            <area alt="Corse" title="Corse" href="carte.php?region=Corse" coords="584,569,571,623,553,587,552,567,568,556" shape="poly" />
        </map>
        <form action="carte.php#form-selection" method="GET" id="form-selection">
            <label for="region">La région : </label>
            <select name="region" id="region" onchange="this.form.submit()">
                <option value="">Choisissez une région</option>
                <?php foreach (array_keys($tableauDepartements) as $region): ?>
                    <option value="<?= h($region) ?>" <?= ($regionSelectionnee === $region) ? 'selected' : '' ?>>
                        <?= h($region) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($regionSelectionnee && isset($tableauDepartements[$regionSelectionnee])): ?>
                <label for="departement">Le Département : </label>
                <select name="departement" id="departement" onchange="this.form.submit()">
                    <option value="">Choisissez un département</option>
                    <?php foreach ($tableauDepartements[$regionSelectionnee] as $departement): ?>
                        <option value="<?= h($departement) ?>" <?= ($departementSelectionnee === $departement) ? 'selected' : '' ?>>
                            <?= h($departement) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($departementSelectionnee): ?>
                    <button type="submit" formaction="stations-departement.php" name="action" value="dansDepartement">Voir les stations du département</button>
                    <?php if (!empty($villes)): ?>
                        <label for="ville">La ville : </label>
                        <select name="ville" id="ville" onchange="this.form.submit()">
                            <option value="">Choisissez une ville</option>
                            <?php foreach ($villes as $ville): ?>
                                <option value="<?= h($ville['id']) ?>"
                                    <?= ($villeSelectionnee == $ville['id']) ? 'selected' : '' ?>>
                                    <?= h($ville['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else: ?>
                        <p class="warning">Aucune ville pour ce département.</p>
                    <?php endif; ?>

                    <?php if ($villeSelectionnee): ?>
                        <button type="submit" formaction="stations-villes.php" name="action" value="dansVille">Voir les prix du carburant disponibles dans la ville </button>
                        <button type="submit" formaction="proximite-villes.php" name="action" value="proximiteVille">Voir les prix du carburant à proximité de la ville </button>


                        <?php //endif de si ville selectionne 
                        ?>
                    <?php elseif ($action == 'validerVille'): ?>
                        <p class="info">Veuillez sélectionner une ville pour continuer.</p>
                    <?php endif; ?>

                    <?php //endif de si departement selectionne 
                    ?>
                <?php elseif ($action == 'validerDepar'): ?>
                    <p class="info">Veuillez sélectionner un département pour continuer.</p>
                <?php endif; ?>
                <?php //endif de si region selectionne 
                ?>
            <?php elseif ($action == 'ValiderReg'): ?>
                <p class="info">Veuillez sélectionner une région pour continuer.</p>
            <?php endif; ?>

        </form>
    </div>
</section>

<?php
$date = "05-04-2026";
require_once("./include/footer.inc.php");
?>