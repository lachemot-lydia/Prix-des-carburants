<?php
declare(strict_types=1);
/**
 * @file confidentialite.php
 * @brief Page de politique de confidentialité de PleinPasCher.
 *
 * Informe l'utilisateur sur la transmission de son adresse IP
 * aux services tiers de géolocalisation (ip2location.io, ipinfo.io),
 * la conservation anonyme des statistiques de consultation,
 * ses droits RGPD et la gestion des cookies.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");

$titre = "Confidentialité — PleinPasCher";
$lang  = "fr";
require_once("./include/header.inc.php");
?>

<h1>Politique de confidentialité</h1>
<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>

<div class="page-legale">

    <section>
        <h2>Transmission de votre IP à des tiers</h2>
        <p>
            Votre adresse IP est transmise à deux services externes pour déterminer votre localisation approximative :
        </p>
        <ul>
            <li><a href="https://www.ip2location.io/privacy-policy" target="_blank" rel="noopener noreferrer">ip2location.io</a></li>
            <li><a href="https://ipinfo.io/privacy-policy" target="_blank" rel="noopener noreferrer">ipinfo.io</a></li>
        </ul>
        <p>Ces services ont leurs propres politiques de confidentialité consultables via les liens ci-dessus. Nous ne conservons pas votre adresse IP.</p>
    </section>

    <section>
        <h2>Durée de conservation des statistiques</h2>
        <p>
            Les consultations de villes sont enregistrées anonymement (sans IP ni identifiant) dans un fichier interne.
            Ce fichier n'est pas purgé automatiquement et peut être supprimé sur simple demande.
        </p>
    </section>

    <section>
        <h2>Vos droits (RGPD)</h2>
        <p>Vous disposez d'un droit d'accès, de rectification et de suppression de vos données. Pour l'exercer, contactez-nous via les services de CY Cergy Paris Université.</p>
    </section>

    <section>
        <h2>Supprimer les cookies</h2>
        <p>Vous pouvez supprimer les cookies de ce site à tout moment depuis votre navigateur :</p>
        <ul>
            <li>Chrome</li>
            <li>Firefox</li>
            <li>Safari</li>
            <li>Edge</li>
        </ul>
    </section>

    <section>
        <h2>Mise à jour</h2>
        <p>Cette politique peut être modifiée à tout moment. La date de dernière mise à jour est indiquée en bas de page.</p>
    </section>


</div>
<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>