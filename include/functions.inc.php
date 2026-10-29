<?php

if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    define('COOKIE_PATH', '/');
} else {
    define('COOKIE_PATH', '/');
}
/**Partie 1**************************************************************/

/**Localiser un visiteur**************/

/**
 * Fonction qui retourne l'adresse ip d'un visiteur.
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @return string adresse IP du visiteur
 */
function getIPVisiteur(): string
{

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]); // La première IP est celle du visiteur
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    //ip par défaut pour le test local
    if ($ip == "127.0.0.1") {
        $ip = "193.54.115.107";
    }
    return $ip;
}

/**
 * Fonction sert à sécuriser l'affichage dans le HTML
 * @return string sécurisé
 */
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction qui récupère les données disponibles d'une url 
 * contenant l'IP du visiteur avec format JSON
 * @return array<string, mixed>|false
 */
function getIpGeoJSON(string $url): array|false
{

    $contenu = @file_get_contents($url);

    if ($contenu == false || in_array($contenu, ["1", "2", "3"])) {
        return false;
    }

    $donnee = json_decode($contenu, true);

    if (is_array($donnee)) {
        return $donnee;
    } else {
        return false;
    }
}

/**
 * Fonction qui récupère les données disponibles d'une url 
 * contenant l'IP du visiteur avec format XML
 * @return  \SimpleXMLElement|false objet contenant les data
 */
function getIpGeoXML(string $url): \SimpleXMLElement|false
{

    $contenu = @file_get_contents($url);

    if ($contenu == false || in_array($contenu, ["1", "2", "3"])) {
        return false;
    }

    return simplexml_load_string($contenu);
}

/**
 * Retourne un index aléatoire pour les films Ghibli (0 à 21).
 * @return int indice aléatoire 
 */
function randomGHIBLI($films): int
{
    if (empty($films)) {
        return 0;
    }
    return rand(0, count($films) - 1);
}


/**
 * Fonction qui extrait les régions et leurs départements associés
 * depuis les fichiers CSV regions.csv et departements.csv
 * @return array tableau associatif regions/departements
 */
function extractRegionDepart(): array
{
    //Lecture des fichiers CSV ligne par ligne
    $regions = file(__DIR__ . "/../data/regions.csv");
    $departements = file(__DIR__ . "/../data/departements.csv");

    $tab = [];

    //Suppression de la première ligne (en-tête) des deux fichiers
    array_shift($regions);
    array_shift($departements);

    //Parcours de chaque ligne du fichier regions.csv
    foreach ($regions as $regionLine) {
        $region = str_getcsv(trim($regionLine), ",", '"', "\\");

        //Parcours de chaque ligne du fichier departements.csv
        foreach ($departements as $departementLine) {
            $departement = str_getcsv(trim($departementLine), ",", '"', "\\");

            //Vérification de la liaison entre région et département
            if ($region[1] == $departement[1]) {
                $tab[$region[2]][] = "({$departement[2]}) {$departement[3]}";
            }
        }
    }

    return $tab;
}

/**
 * Fonction qui extrait les villes d'un département
 * depuis le fichier villes.csv
 * @param string $codeDepartement code du département
 * @return array tableau des villes avec nom et coordonnées GPS
 */

function extractVilles(string $codeDepart): array
{
    $villes = file(__DIR__ . "/../data/villes.csv");
    $tab = [];
    //Suppression de la première ligne du fichier
    array_shift($villes);
    foreach ($villes as $villeLine) {
        $ville = str_getcsv(trim($villeLine), ",", '"', "\\");
        if ($ville[1] == $codeDepart) {
            //nom, latitude GPS, longitude GPS
            $tab[] = [
                'id' => $ville[0],
                'nom' => $ville[4] . " (" . $ville[3] . ")",
                'gps_lat' => $ville[6],
                'gps_lng' => $ville[7]
            ];
        }
    }

    return $tab;
}

/**
 * Supprime un cookie en le faisant expirer dans le passé.
 * @param string $nom Nom du cookie à supprimer
 * @return void
 */
function supprimerCookie(string $nom): void
{
    setcookie($nom, '', time() - 3600, COOKIE_PATH);
}

/**
 * Enregistre le choix du style dans un cookie.
 * @param string $choix "alternatif" ou "default"
 * @return void
 */
function sauvegarderStyleCookie(string $choix): void
{
    $valeursValides = ['alternatif', 'default'];

    if (!in_array($choix, $valeursValides, true)) {
        return;
        // valeur non autorisée, on n'enregistre rien
    }

    setcookie('style', $choix, time() + 30 * 24 * 3600, COOKIE_PATH);
}

/**
 * Lit le cookie et retourne le nom du fichier CSS.
 * Si le cookie est absent ou corrompu, retourne le style par défaut.
 * @return string Nom du fichier CSS ("style.css" ou "style-alternatif.css")
 */
function lireStyleCookie(): string
{
    if (!isset($_COOKIE['style'])) {
        return 'style.css'; //style par défaut
    }

    $valeur = $_COOKIE['style'];

    if ($valeur === 'alternatif') {
        return 'style-alternatif.css';
    }

    if ($valeur === 'default') {
        return 'style.css';
    }
    //valeur corrompue on supprime le cookie et on retourne le style par défaut
    supprimerCookie('style');
    return 'style.css';
}

/**
 * Enregistre la dernière ville consultée dans un cookie.
 * @param string $ville nom de la ville
 * @param string $departement nom du département
 * @param string $region nom de la région 
 * @return void 
 */

function sauvegarderVillesCookie(string $ville, string $departement, string $region, string $villeId): void
{
    $donnees = json_encode(['ville' => $ville, 'ville_id' => $villeId, 'departement' => $departement, 'region' => $region, 'date' => date('d/m/y')]);
    setcookie('derniere_ville', $donnees, time() + 30 * 24 * 3600, COOKIE_PATH);
}

/**
 * Lit le cookie de la dernière ville consultée.
 * @return array|null Tableau avec les clés 'ville','departement','region','date'
 */
function lireVilleCookie(): ?array
{

    if (!isset($_COOKIE['derniere_ville'])) {
        return null;
    }
    $donnees = json_decode($_COOKIE['derniere_ville'], true);
    if (!is_array($donnees) || empty($donnees) || empty($donnees['ville']) || empty($donnees['departement']) || empty($donnees['region'])) {
        supprimerCookie('derniere_ville');
        return null;
    }
    return $donnees;
}


/**
 * Elle enregistre chaque consultation (ville,département,région) dans un fichier CSV, avec la date et l’heure.
 * @return void 
 */
function stockerConsultationCSV(string $ville, string $departement, string $region): void
{

    $fichier = __DIR__ . '/../stockage/consultations.csv';

    if (!file_exists($fichier)) {
        file_put_contents($fichier, "date;heure;ville;departement;region\n");
    }
    $ligne = date('d/m/Y') . ';' . date('H:i') . ';' . $ville . ';' . $departement . ';' . $region . "\n";
    file_put_contents($fichier, $ligne, FILE_APPEND);
}

/**
 * Lit le fichier CSV des consultations et retourne
 * un tableau trié des villes les plus consultées.
 * @return array 'ville'=> nombre de consultations
 */
function lireStatistiquesCSV(): array
{
    $fichier = __DIR__ . '/../stockage/consultations.csv';
    if (!file_exists($fichier)) {
        return [];
    }
    $lignes = file($fichier);
    array_shift($lignes);
    $statistiques = [];
    foreach ($lignes as $ligne) {
        $colonnes = explode(';', trim($ligne));
        if (!empty($colonnes[2])) {
            $ville = $colonnes[2];
            //Compter le nombre de fois une ville apparait dans le csv
            $statistiques[$ville] = isset($statistiques[$ville]) ? $statistiques[$ville] + 1 : 1;
        }
    }
    arsort($statistiques);
    return $statistiques;
}

/**
 *Recherche le nom d'une ville à partir de son identifiant (ID)
 *en parcourant le fichier villes.csv.
 * @return string nom de la ville 
 */
function nomVilleId(string $id): string
{
    $villes = file(__DIR__ . "/../data/villes.csv");
    array_shift($villes);
    foreach ($villes as $villeLine) {
        $ville = str_getcsv(trim($villeLine), ",", '"', "\\");
        if ($ville[0] == $id) {
            return $ville[4]; // retourne le nom
        }
    }
    return "";
}

/**
 * Supprime les stations  qui existent dans l'API
 * mais n'ont aucun carburant disponible.
 * @param array $stations Tableau au format API
 * @return array Tableau sans les stations vides
 */
function filtrerStationsSansCarburant(array $stations): array
{
    if (empty($stations['results'])) return $stations;

    $stations['results'] = array_values(array_filter(
        $stations['results'],
        fn(array $s) => !empty($s['carburants_disponibles'])
    ));

    return $stations;
}


/**
 * Trie les stations par prix croissant pour un carburant donné.
 * Les stations sans prix sont placées à la fin.
 * @param array $stations tableau au format API (clé 'results')
 * @param string $carburant un carburant ou plusieurs
 * @return array tableau trié
 */
function trierParPrix(array $stations, string|array $carburant): array
{
    if (empty($stations['results'])) return $stations;

    $carburants=is_array($carburant) ? $carburant : [$carburant];
    $getPrixMin=function (array $station) use ($carburants): ?float {
        $prix=[];
        foreach ($carburants as $carbu) {
            $champ = strtolower($carbu) . '_prix';
            $p = isset($station[$champ]) && $station[$champ] !== '' ? (float)$station[$champ] : null;
            if ($p !== null) $prix[] = $p;
        }
        return empty($prix) ? null : array_sum($prix) / count($prix);
    };

    usort($stations['results'], function (array $a, array $b) use ($getPrixMin): int {
        $prixA = $getPrixMin($a);
        $prixB = $getPrixMin($b);

        if ($prixA === null && $prixB === null) return 0;
        if ($prixA === null) return 1;
        if ($prixB === null) return -1;

        return $prixA <=> $prixB;
    });

    return $stations;
}



/**
 * Retourne la classe CSS correspondant au niveau de prix d'un carburant.
 *
 * Compare le prix donné aux seuils bas et haut définis pour chaque
 * type de carburant afin de classifier le prix en trois niveaux :
 * bas (vert), moyen (orange) ou élevé (rouge).
 *
 * @param float  $prix Prix du carburant en €/L
 * @param string $carburant Nom du carburant 
 * @return string Classe CSS : 'prix-bas','prix-moyen' ou 'prix-haut'
 */
function classePrix(float $prix, string $carburant, array $moyennesNationales): string
{
    $moyenne = $moyennesNationales[$carburant] ?? null;
    if ($moyenne === null) return '';

    $ecart = ($prix-$moyenne)/$moyenne;

    if ($ecart <= -0.03) return 'prix-bas';   // +3% sous la moyenne
    if ($ecart <= +0.03) return 'prix-moyen'; // dans ±3%
    return 'prix-haut'; // +3% au-dessus
}



/**
 * Récupère des données depuis une URL en utilisant un système de cache local.
 * @param string $url URL de l'API à appeler
 * @param int $ttlSeconds Durée de validité du cache (en secondes)
 * @return array|false Données décodées ou false en cas d'erreur
 */
function getAvecCache(string $url, int $ttlSeconds = 3600): array|false
{
    $cacheDir = __DIR__ . '/../stockage/cache/';
    $cacheFile = $cacheDir . md5($url) . '.json';

    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttlSeconds) {
        $contenu = file_get_contents($cacheFile);
        return json_decode($contenu, true) ?? false;
    }

    $contenu = @file_get_contents($url);
    if ($contenu === false || in_array($contenu, ["1", "2", "3"])) {
        // retourner le cache expiré si dispo
        if (file_exists($cacheFile)) {
            return json_decode(file_get_contents($cacheFile), true) ?? false;
        }
        return false;
    }

    file_put_contents($cacheFile, $contenu);
    return json_decode($contenu, true) ?? false;
}

/**
 *@brief Récupère les stations d'une ville donnée via l'API gouvernementale.
 * Récupère les stations d'une ville donnée via l'API gouvernementale.
 * Utilise un cache de 30 minutes pour éviter les appels répétés.
 * @param string $ville  Nom de la ville
 * @return array Résultats de l'API ou tableau vide
 */
function stations(string $ville): array
{
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records?limit=100&refine=ville:"
        . urlencode('"' . $ville . '"');
    $resultat = getAvecCache($url, 1800);
    $resultat = $resultat !== false ? $resultat : [];
    return filtrerStationsSansCarburant($resultat);
}

/**
 * Récupère les stations dans un rayon autour d'une position GPS.
 * @param float $latitude Latitude
 * @param float $longitude Longitude
 * @param int $rayon Rayon en km (par défaut 10 km)
 * @return array Résultats ou tableau vide
 */
function stationsProximite(float $latitude, float $longitude, int $rayon = 10): array
{
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
        . "?where=" . urlencode("distance(geom, GEOM'POINT($longitude $latitude)',{$rayon}km)") . "&limit=100";
    $resultat = getAvecCache($url, 1800);
    $resultat = $resultat !== false ? $resultat : [];
    return filtrerStationsSansCarburant($resultat);
}

/**
 * Récupère les stations d'un département donné.
 * @param string $codeDepartement Code du département 
 * @return array Résultats ou tableau vide
 */

function stationsDepartement(string $codeDepartement): array
{
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
        . "?where=" . urlencode("code_departement=\"$codeDepartement\"") . "&limit=100";
    $resultat = getAvecCache($url, 3600);
    $resultat = $resultat !== false ? $resultat : [];
    return filtrerStationsSansCarburant($resultat);
}

/**
 * Récupère les stations proches d'une position GPS
 * mais uniquement dans un département donné.
 * @param float  $latitude  Latitude
 * @param float  $longitude Longitude
 * @param string $codeDept  Code du département
 * @param int    $rayon Rayon en km
 * @return array Résultats ou tableau vide
 */
function stationsDepartementProximite(float $lat, float $lon, string $codeDept, int $rayon = 10): array
{
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
        . "?where=" . urlencode("distance(geom, GEOM'POINT($lon $lat)', {$rayon}km) AND code_departement=\"$codeDept\"") . "&limit=100";
    $resultat = getAvecCache($url, 1800);
    $resultat = $resultat !== false ? $resultat : [];
    return filtrerStationsSansCarburant($resultat);
}


/**
 * Filtre un tableau de stations selon un ou plusieurs types de carburants.
 *
 * @param array $stations  Tableau au format retourné par l'API 
 * @param string|array $carburant  Un carburant  ou plusieurs 
 * @return array Tableau au même format avec uniquement les stations retenues
 */
function filtrerParCarburant(array $stations, string|array $carburant): array
{
    if (empty($stations['results'])) return ['results' => []];
    $carburants = is_array($carburant) ? $carburant : [$carburant];
    $resultats = array_filter(
        $stations['results'],
        function (array $station) use ($carburants): bool {
            $disponibles = $station['carburants_disponibles'] ?? [];
            return !empty(array_intersect($carburants, $disponibles));
        }
    );

    return ['results' => array_values($resultats)];
}

/**
 * Génère le HTML affichant les détails d'une station-service.

 * @param array $station  Données complètes de la station (API)
 * @param string|array|null $carburantSelectionne Carburant choisi par l'utilisateur 
 * @return string HTML prêt à afficher
 */
function afficherDetailsStation(array $station, string|array|null $carburantSelectionne): string
{
    $html = '<div class="station-details"><h3>Détails de la station</h3>';

    if (!empty($station['services_service'])) {
        $html .= '<div class="station-services"><strong>Services</strong><ul>';
        foreach ($station['services_service'] as $service) {
            $html .= '<li>' . h($service) . '</li>';
        }
        $html .= '</ul></div>';
    }
    $automate = $station['horaires_automate_24_24'] ?? 'Non renseigné';
    $html .= '<div class="station-horaires"><strong>Automate 24h/24</strong><span>' . h($automate) . '</span></div>';

    if ($carburantSelectionne !== null) {
        $carburants = is_array($carburantSelectionne) ? $carburantSelectionne : [$carburantSelectionne];

        foreach ($carburants as $carbu) {
            $champMaj = strtolower($carbu) . '_maj';
            $maj = $station[$champMaj] ?? null;
            if ($maj) {
                $html .= '<div class="station-maj">' . '<strong>Mise à jour ' . h($carbu) . '</strong>' . '<span>' . h(date('d/m/Y H:i', strtotime($maj))) . '</span>' . '</div>';
            }
        }
    }
    if (!empty($station['carburants_rupture_definitive'])) {
        $html .= '<div class="station-rupture"><strong>Ruptures définitives</strong><span>' . h($station['carburants_rupture_definitive']) . '</span></div>';
    }

    return $html . '</div>';
}

/**
 * Incrémente le compteur de visiteurs (une fois par mois via cookie).
 * @return int Nombre total de visiteurs
 */
function compterVisiteur(): int
{
    $fichier = __DIR__ . '/../stockage/visiteurs.txt';

    if (!file_exists($fichier)) {
        file_put_contents($fichier, '0');
    }

    $total = (int)file_get_contents($fichier);

    if (!isset($_COOKIE['visiteur_compte'])) {
        file_put_contents($fichier, (string) ++$total);
        setcookie('visiteur_compte', '1', time() + 30 * 24 * 3600, COOKIE_PATH);
    }

    return $total;
}
/**
 * Lit le nombre total de visiteurs sans l'incrémenter.
 * @return int
 */
function lireNbVisiteurs(): int
{
    $fichier = __DIR__ . '/../stockage/visiteurs.txt';
    return file_exists($fichier) ? (int) file_get_contents($fichier) : 0;
}




/**
 * Calcule le prix moyen national actuel pour chaque carburant
 * en interrogeant l'API sur un échantillon de stations.
 * @return array ['SP95' => 1.85, 'Gazole' => 1.72, ...]
 */
function prixMoyensNationaux(): array
{
    $carburants = [
        'SP95' => 'sp95_prix',
        'SP98' => 'sp98_prix',
        'Gazole' => 'gazole_prix',
        'E10' => 'e10_prix',
        'E85' => 'e85_prix',
        'GPLc' => 'gplc_prix',
    ];
    $totaux = [];
    $compteurs = [];
    $offset = 0;
    $limit = 100;
    $maxPages = 20;

    do {
        $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
            . "?limit=$limit&offset=$offset&select=sp95_prix,sp98_prix,gazole_prix,e10_prix,e85_prix,gplc_prix";

        $resultat = getAvecCache($url, 3600);
        if (!$resultat || empty($resultat['results'])) break;

        foreach ($resultat['results'] as $station) {
            foreach ($carburants as $nom => $champ) {
                if (!empty($station[$champ]) && $station[$champ] > 0) {
                    $totaux[$nom] = ($totaux[$nom] ?? 0) + (float)$station[$champ];
                    $compteurs[$nom] = ($compteurs[$nom] ?? 0) + 1;
                }
            }
        }
        $offset += $limit;
        $maxPages--;
    } while (count($resultat['results']) === $limit && $maxPages > 0);

    $moyennes = [];
    foreach ($totaux as $nom => $total) {
        if ($compteurs[$nom] > 0) {
            $moyennes[$nom] = round($total / $compteurs[$nom], 3);
        }
    }
    return $moyennes;
}




/**
 * Ajoute ou met à jour une entrée dans le XML pour la date donnée.
 * Protège contre les doublons de date.
 */
function ajouterEntreeXML(\SimpleXMLElement $xml, string $xmlPath, array $moyennes, string $date): void
{
    // Vérification doublon
    foreach ($xml->entree as $entree) {
        if ((string)$entree['date'] === $date) {
            return;
        }
    }
    $nouvelleEntree = $xml->addChild('entree');
    $nouvelleEntree->addAttribute('date', $date);

    foreach ($moyennes as $nom => $prix) {
        $carb = $nouvelleEntree->addChild('carburant');
        $carb->addAttribute('nom', $nom);
        $carb->addAttribute('moyenne', (string)$prix);
    }
    $xml->asXML($xmlPath);
}


/**
 * Retourne le SimpleXML et les entrées triées par date décroissante.
 * Crée le fichier XML s'il n'existe pas.
 */
function getEntreesTriees(string $xmlPath): array
{
    // Création du fichier XML si absent
    if (!file_exists($xmlPath)) {
        $xmlVide = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tendance/>');
        $xmlVide->asXML($xmlPath);
    }

    $xml = simplexml_load_file($xmlPath);
    if ($xml === false) {
        throw new \RuntimeException("Impossible de lire le fichier XML : $xmlPath");
    }
    $entrees = [];
    foreach ($xml->entree as $entree) {
        $entrees[(string)$entree['date']] = $entree;
    }
    krsort($entrees);
    return [$xml, $entrees];
}

/**
 * Extrait les prix moyens des carburants depuis une entrée XML.
 *
 * Parcourt les éléments <carburant> d'une entrée du fichier XML
 * de tendance et retourne un tableau associatif nom => prix moyen.
 *
 * @param \SimpleXMLElement $entree Élément XML <entree> contenant
 *  les sous-éléments <carburant>
 * @return array<string, float> Tableau associatif
 */
function extrairePrix(\SimpleXMLElement $entree): array
{
    $prix = [];
    foreach ($entree->carburant as $c) {
        $prix[(string)$c['nom']] = (float)$c['moyenne'];
    }
    return $prix;
}

/**
 * Récupère les prix moyens actuels depuis le cache XML ou l'API.
 * @param string $xmlPath Chemin vers le fichier XML
 * @param array  $moyennes Prix calculés depuis l'API (peut être vide si échec)
 * @return array 
 */
function getPrixMoyens(string $xmlPath, array $moyennes): array
{
    $dateAujourdhui = date('Y-m-d');

    [$xml, $entrees] = getEntreesTriees($xmlPath);

    $derniereDate  = array_key_first($entrees);
    $derniereEntree = $entrees[$derniereDate] ?? null;

    // Cache XML valide pour aujourd'hui
    if ($derniereDate === $dateAujourdhui && $derniereEntree !== null) {
        return extrairePrix($derniereEntree);
    }

    // Échec API
    if (empty($moyennes)) {
        // Retourne la dernière entrée connue plutôt que rien
        return $derniereEntree !== null ? extrairePrix($derniereEntree) : [];
    }

    ajouterEntreeXML($xml, $xmlPath, $moyennes, $dateAujourdhui);

    return $moyennes;
}

/**
 * Charge toutes les entrées du XML pour le graphique,
 * triées par date croissante.
 */
function chargerDonneesCarburants(): array
{
    $fichierXml = __DIR__ . '/../data/tendance.xml';

    if (!file_exists($fichierXml)) {
        return ['labels' => '[]', 'datasets' => '[]'];
    }

    $xml = simplexml_load_file($fichierXml);
    if ($xml === false) {
        return ['labels' => '[]', 'datasets' => '[]'];
    }

    $carburants = ['Gazole', 'SP95', 'E85', 'E10', 'SP98', 'GPLc'];
    $couleurs = ['#f97316', '#3b82f6', '#22c55e', '#a855f7', '#ef4444', '#eab308'];

    // Collecte et tri par date croissante pour le graphique
    $entrees = [];
    foreach ($xml->entree as $entree) {
        $entrees[(string)$entree['date']] = $entree;
    }
    ksort($entrees); // croissant pour l'axe du temps

    $dates = [];
    $series = array_fill_keys($carburants, []);

    foreach ($entrees as $date => $entree) {
        $dates[] = $date;
        $map = [];
        foreach ($entree->carburant as $c) {
            $map[(string)$c['nom']] = (float)$c['moyenne'];
        }
        foreach ($carburants as $nom) {
            $series[$nom][] = $map[$nom] ?? null;
        }
    }

    $datasets = [];
    foreach ($carburants as $k => $nom) {
        $datasets[] = [
            'label' => $nom,
            'data'  => $series[$nom],
            'borderColor'  => $couleurs[$k],
            'backgroundColor' => $couleurs[$k],
            'borderWidth' => 2,
            'pointRadius' => 0,
            'pointHoverRadius' => 4,
            'tension'  => 0.3,
        ];
    }

    return [
        'labels' => json_encode(array_values($dates)),
        'datasets' => json_encode($datasets),
    ];
}
