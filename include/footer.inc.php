</main>

<footer>
    <div id="hautPosition">
        <a href="#haut" aria-label="Revenir en haut de page">↑</a>
    </div>

    <div id="footer-contenu">

        <div class="footer-col">
            <p class="footer-titre">PleinPasCher</p>
            <p class="footer-brand-desc">Site de consultation des prix des carburants en France métropolitaine, mis à jour en temps réel depuis l'API officielle du gouvernement.</p>
        </div>

        <div class="footer-col">
            <p class="footer-titre">Navigation</p>
            <ul>
                <li><a href="carte.php">Carte interactive</a></li>
                <li><a href="proximite-departement.php">À proximité</a></li>
                <li><a href="statistiques.php">Statistiques</a></li>
                <li><a href="plan.php">Plan du site</a></li>
                <li><a href="tech.php">Page technique</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <p class="footer-titre">Légal</p>
            <ul>
                <li><a href="mentions.php">Mentions légales</a></li>
                <li><a href="confidentialite.php">Confidentialité</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <p class="footer-titre">Projet</p>
            <ul>
                <li>UE : Développement web</li>
                <li><strong style="color:rgba(255,255,255,0.65)">L2 Informatique – S4</strong></li>
                <li>Lydia LACHEMOT</li>
                <li>Thomas LENGLIN</li>
                <li>2025–2026</li>
            </ul>
        </div>

    </div>

    <div id="footer-bas">
        <span>© <?= date('Y') ?> PleinPasCher — CY Cergy Paris Université</span>
        <span><?= $date ?></span>
    </div>

</footer>
</body>
</html>
