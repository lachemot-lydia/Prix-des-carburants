<?php

declare(strict_types=1);
/**
 * @file statistiques.php
 * @brief Page des statistiques de consultation de PleinPasCher.
 *
 * Lit le fichier CSV des consultations anonymes et affiche les villes
 * les plus consultées sous forme de graphique en barres (Chart.js)
 * et d'un tableau détaillé avec rang, nombre de consultations et
 * pourcentage par rapport au total.
 *
 * @author Lydia LACHEMOT, Thomas LENGLIN
 * @version 1.0
 * @date 2025-2026
 */
require_once("./include/functions.inc.php");



$titre = "Statistiques";
$lang = "fr";
require_once("./include/header.inc.php");

$statistiques = lireStatistiquesCSV();
$consultationsTotales = array_sum($statistiques);
$statistiquesAffichees = array_slice($statistiques, 0, 15, true);
$villes = json_encode(array_keys($statistiquesAffichees));
$consultations = json_encode(array_values($statistiquesAffichees));
?>

<nav id="nav-page" aria-label="Navigation de la page">
    <ul>
        <li><a href="#graphe">Graphique</a></li>
        <li><a href="#table-stat">Tableau détaillé</a></li>
        <li><a href="#graphe-ligne">Prix moyens journaliers</a></li>


    </ul>
</nav>

<h1>Statistiques des consultations</h1>

<a href="index.php" class="retour-accueil"><img src="./images/accueil.png" alt="retour à l'accueil" /></a>


<section id="stats-resume">
    <h2>Activité des utilisateurs</h2>
    <div class="stats-resume-item">
        <span class="stats-resume-label">Visiteurs uniques</span>
        <strong><?= lireNbVisiteurs() ?></strong>
    </div>
    <div class="stats-resume-item">
        <span class="stats-resume-label">Consultations de villes</span>
        <strong><?= $consultationsTotales ?></strong>
    </div>
</section>

<section id="graphe">
    <h2>Villes les plus consultées</h2>

    <?php if (count($statistiques) > count($statistiquesAffichees)): ?>
        <p class="stats-note">Affichage des 15 villes les plus consultées.</p>
    <?php endif; ?>

    <?php if (empty($statistiques)): ?>
        <p>Aucune consultation enregistrée pour le moment.</p>
    <?php else: ?>
        <div class="cont-graphique">
            <canvas id="graphique-villes"></canvas>
        </div>
        <script src="./js/charts.js"></script>
        <script>
            const contextVilles = document.getElementById('graphique-villes');
            new Chart(contextVilles, {
                type: 'bar',
                data: {
                    labels: <?= $villes ?>,
                    datasets: [{
                        label: 'Nombre de consultations',
                        data: <?= $consultations ?>,
                        backgroundColor: 'rgba(20, 147, 210, 0.7)',
                        borderColor: 'rgba(10, 65, 116, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }

            });
        </script>


        <section id="table-stat">
            <h2>Détail par ville</h2>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ville</th>
                        <th>Consultations</th>
                        <th>Part</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rang = 1;
                    foreach ($statistiquesAffichees as $ville => $nb): ?>
                        <tr <?= $rang <= 3 ? 'class="top-' . $rang . '"' : '' ?>>
                            <td>
                                <?php echo $rang; ?>
                            </td>
                            <td><?= h($ville) ?></td>
                            <td><strong><?= $nb ?></strong></td>
                            <td>
                                <?php $pourcentage = $consultationsTotales > 0 ? round($nb / $consultationsTotales * 100, 1) : 0; ?>
                                <div class="barre-pct">
                                    <div class="barre-pct-fill" style="width:<?= $pourcentage ?>%"></div>
                                    <span><?= $pourcentage ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php $rang++;
                    endforeach; ?>
                </tbody>
            </table>
        </section>

    <?php endif; ?>

</section>


<?php
$data = chargerDonneesCarburants();
?>

<section id="graphe-ligne">
    <h2>Évolution des prix moyens nationaux des carburants</h2>
    <div class="cont-graphique">
        <canvas id="graphique-prix"></canvas>
    </div>
    <script src="./js/charts.js"></script>
    <script>
        const contextPrix = document.getElementById('graphique-prix');
        new Chart(contextPrix, {
            type: 'line',
            data: {
                labels: <?= $data['labels'] ?? '[]' ?>,
                datasets: <?= $data['datasets'] ?? '[]' ?>
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: (value) => value.toLocaleString('fr-FR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' EUR'
                        }
                    }
                }
            }
        });
    </script>
</section>




<?php
$date = "04-05-2026";
require_once("./include/footer.inc.php");
?>