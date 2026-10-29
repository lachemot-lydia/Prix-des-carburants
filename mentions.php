<?php
declare(strict_types=1);
/**
 * @file mentions.php
 * @brief Page des mentions légales de PleinPasCher.
 *
 * Présente les informations légales du site : éditeur, hébergeur,
 * sources des données (API gouvernementale, IGN, ip2location, Ghibli API),
 * propriété intellectuelle, détail des cookies utilisés et
 * limitation de responsabilité sur l'exactitude des données affichées.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");

$titre = "Mentions légales — PleinPasCher";
$lang  = "fr";
require_once("./include/header.inc.php");
?>

<h1>Mentions légales</h1>
<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>
<div class="page-legale">
    <section>
        <h2>Éditeur du site</h2>
        <p>
            Le site <strong>PleinPasCher</strong> est un projet pédagogique réalisé dans le cadre de l'UE
            <em>Développement web</em> en Licence 2 Informatique – Semestre 4 à l'université
            <strong>CY Cergy Paris Université</strong>.
        </p>
        <ul>
            <li><strong>Contact :</strong>
                <a href="mailto:lydia.lachemot@etu.cyu.fr">lydia.lachemot@etu.cyu.fr</a> —
                <a href="mailto:thomas.lenglin@etu.cyu.fr">thomas.lenglin@etu.cyu.fr</a>
            </li>
            <li><strong>Année universitaire :</strong> 2025–2026</li>
            <li><strong>Établissement :</strong> CY Cergy Paris Université</li>
        </ul>
    </section>

    <section>
        <h2>Hébergement</h2>
        <p>
            Ce site est hébergé sur <strong>Alwaysdata</strong> :
        </p>
        <ul>
            <li><strong>Site web :</strong> <a href="https://www.alwaysdata.com" target="_blank" rel="noopener noreferrer">www.alwaysdata.com</a></li>
        </ul>
    </section>


    <section>
        <h2>Sources des données</h2>
        <p>
            Les prix des carburants affichés sur ce site proviennent de l'API officielle du gouvernement français :
        </p>
        <ul>
            <li>
                <strong>API Prix des carburants en France (flux instantané v2)</strong><br />
                Fournisseur : <a href="https://data.economie.gouv.fr" target="_blank" rel="noopener noreferrer">data.economie.gouv.fr</a><br />
                Licence : <a href="https://www.etalab.gouv.fr/licence-ouverte-open-licence" target="_blank" rel="noopener noreferrer">Licence Ouverte / Open Licence v2.0 (Etalab)</a>
            </li>
            <li>
                <strong>Carte administrative des régions de France</strong><br />
                Source : IGN 2016 — Licence ouverte
            </li>
            <li>
                <strong>Géolocalisation par adresse IP</strong><br />
                Service utilisé : <a href="https://api.ip2location.io" target="_blank" rel="noopener noreferrer">ip2location.io</a> &amp;
                <a href="https://ipinfo.io" target="_blank" rel="noopener noreferrer">ipinfo.io</a><br />
                Ces services sont utilisés uniquement pour déterminer le département de l'utilisateur à des fins de recherche de stations proches. Aucune donnée personnelle n'est conservée.
            </li>
            <li>
                <strong>API Studio Ghibli</strong> (page technique)<br />
                Source : <a href="https://ghibliapi.vercel.app" target="_blank" rel="noopener noreferrer">ghibliapi.vercel.app</a> API publique non officielle
            </li>
        </ul>
    </section>

    <section>
        <h2>Propriété intellectuelle</h2>
        <p>
            L'ensemble du code source, des styles graphiques et des contenus rédigés sur ce site sont la propriété
            de leurs auteurs (Lydia LACHEMOT et Thomas LENGLIN) et sont produits dans un cadre académique.
            Toute reproduction ou réutilisation sans autorisation est interdite, sauf mention contraire.
        </p>

    </section>

    <section>
        <h2>Données personnelles &amp; cookies</h2>
        <p>
            Ce site utilise des <strong>cookies de session</strong> à des fins purement fonctionnelles :
        </p>
        <ul>
            <li><strong>Cookie de style</strong> : mémorise le thème choisi (clair ou sombre) pour la durée de 30 jours.</li>
            <li><strong>Cookie de dernière visite</strong> : mémorise la dernière ville consultée via la carte afin de proposer un accès rapide. Durée : 30 jours.</li>
            <li><strong>Cookies de navigation carte</strong> : mémorisent les sélections en cours de navigation sur la carte. Durée : 1 heure.</li>
        </ul>
        <p>
            Ces cookies ne contiennent aucune donnée personnelle identifiable et ne sont pas transmis à des tiers.
            Aucune donnée de navigation n'est exploitée à des fins commerciales.
        </p>
        <p>
            Les <strong>consultations de villes</strong> sont enregistrées de manière anonyme dans un fichier interne
            (sans adresse IP ni identifiant) à des fins statistiques .
        </p>
        <p>
            Conformément au <strong>Règlement Général sur la Protection des Données (RGPD)</strong>
            et à la loi Informatique et Libertés, vous pouvez effacer les cookies à tout moment
            depuis les paramètres de votre navigateur.
        </p>
    </section>

    <section>
        <h2>Responsabilité</h2>
        <p>
            Les informations affichées sur ce site (prix des carburants, localisation des stations) proviennent
            de sources tierces et sont susceptibles de ne pas être à jour. Ce site est un projet académique et
            ne saurait être tenu responsable d'éventuelles erreurs ou omissions dans les données affichées.
        </p>
        <p>
            L'utilisation du service de géolocalisation par IP est soumise aux conditions d'utilisation
            des fournisseurs concernés. Ce site ne garantit pas l'exactitude de la localisation détectée.
        </p>
    </section>


</div>
<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>