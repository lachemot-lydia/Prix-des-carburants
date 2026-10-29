<?php

declare(strict_types=1);
/**
 * @file tech.php
 * @brief Page de démonstration technique de PleinPasCher.
 *
 * Affiche deux démonstrations : un film Studio Ghibli tiré aléatoirement
 * depuis l'API publique ghibliapi.vercel.app (avec cache 24h), et la
 * géolocalisation du visiteur par adresse IP via deux services distincts :
 * ipinfo.io (format JSON) et whatismyip.com (format XML).
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");
require_once("./include/config.php");
$titre = "Tech";
$lang = "fr";
require_once("./include/header.inc.php");
?>

<h1>Page de démonstration technique</h1>

<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>

<section id="ghibli">
    <h2>Film Ghibli aléatoire</h2>
    <?php

    $url = "https://ghibliapi.vercel.app/films";
    $films = getAvecCache($url, 86400);
    if ($films === false || empty($films)):
    ?>
        <p class="erreur">Service Ghibli indisponible, veuillez réessayer plus tard.</p>
    <?php else:
        $i = randomGHIBLI($films);
        $film = $films[$i];

    ?>
        <ul class="ghibli-infos">
            <li>Titre : <?= !empty($film['title']) ? h($film['title']) : "Non disponible" ?></li>
            <li lang="ja">Titre original :<?= !empty($film['original_title']) ? h($film['original_title']) : "Non disponible" ?></li>
            <li>Année de sortie : <?= !empty($film['release_date']) ? h($film['release_date']) : "Non disponible" ?> </li>
            <li>Réalisateur : <?= !empty($film['director']) ? h($film['director']) : "Non disponible" ?> </li>
            <li>Description : <?= !empty($film['description']) ? h($film['description']) : "Non disponible" ?></li>
        </ul>
        <?php
        $res = $film['image']; ?>
        <div style="display:flex; gap:1rem; align-items:flex-start;">

            <?php if (!empty(h($res))): ?>
                <figure>
                    <img src='<?= h($res) ?>' alt="image du film" width='200'  />
                    <figcaption>
                        « <?= !empty($film['title']) ? h($film['title']) : "-" ?> »
                        - <?= !empty($film['director']) ? h($film['director']) : "-" ?>
                        - <?= !empty($film['release_date']) ? h($film['release_date']) : "-" ?>
                    </figcaption>
                </figure>
            <?php endif; ?>

            <?php if (!empty($film['movie_banner'])): ?>
                <figure>
                    <img src="<?= h($film['movie_banner']) ?>" alt="Bannière du film <?= !empty($film['title']) ? h($film['title']) : '' ?>"  width='550' />
                    <figcaption>Bannière — Réalisé par <?= !empty($film['director']) ? h($film['director']) : '-' ?></figcaption>
                </figure>
            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>


<section id="locationGeo">

    <h2>Où êtes-vous ?</h2>

    <?php

    $ip = getIPVisiteur();

    ///JSON
    $url = "https://ipinfo.io/" . (string)$ip . "/geo";
    $geoJson = getIpGeoJSON($url);
    if ($geoJson == false) {
        $erreurJson = "Service JSON indisponible, veuillez réessayer plus tard.";
    }

    /////XML

    $url1 = "https://api.whatismyip.com/ip-address-lookup.php?key=" . WHATISMYIP_API_KEY . "&input=" . $ip . "&output=xml";
    $geoXml = getIpGeoXML($url1);
    $ville_xml = null;
    $pays_xml = null;
    $region_xml = null;
    $codePostal_xml = null;

    if ($geoXml == false) {
        $erreurXml = "Service XML indisponible, veuillez réessayer plus tard.";
    } else {

        $ville_xml = !empty((string)$geoXml->server_data->city) ? h((string)$geoXml->server_data->city) : "Non disponible";
        $pays_xml = !empty((string)$geoXml->server_data->country) ? h((string)$geoXml->server_data->country) : "Non disponible";
        $region_xml = !empty((string)$geoXml->server_data->region) ? h((string)$geoXml->server_data->region) : "Non disponible";
        $codePostal_xml = !empty((string)$geoXml->server_data->postalcode) ? h((string)$geoXml->server_data->postalcode)     : "Non disponible";
    }

    ?>


<?php if (isset($erreurXml)) : ?>
<p class="error"><?= h($erreurXml) ?></p>
<?php else : ?>


    <ul id="format-xml">
        <li>IP : <?= h($ip) ?></li>
        <li>Ville : <?= $ville_xml ?></li>
        <li>Pays : <?= $pays_xml ?></li>
        <li>Région : <?= $region_xml ?></li>
        <li>Code Postal : <?= $codePostal_xml ?></li>
    </ul>

<?php endif; ?>




</section>


<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>