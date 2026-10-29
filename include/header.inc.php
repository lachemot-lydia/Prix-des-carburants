<?php

if (isset($_GET["style"]) && !empty($_GET["style"])) {
    if ($_GET["style"] === "alternatif") {
        sauvegarderStyleCookie("alternatif");
        $style = "style-alternatif.css"; 
    } else {
        sauvegarderStyleCookie("default");
        $style = "style.css";
    }
} else {
    $style = lireStyleCookie(); 
}
compterVisiteur();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
    <head>
        <title><?=$titre?></title>
        <meta charset="UTF-8"/>
        <meta name="author" content="Lydia LACHEMOT, Thomas LENGLIN"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content="Site de consultation des prix des carburants en France métropolitaine"/>
        <meta name="lieu" content="Cergy Pontoise Université"/>
        <link rel="stylesheet" href="./style/<?= $style ?>"/>
        <link rel="icon" type="image/png" href="./images/favicon.png"/>
    </head>

    <body id="haut">
        <header >
            

            
            <div class="logo-gauche">
                <a href="index.php">
                    <img src="./images/logo.png" alt="logo du site"/>
                    <div class="nom-site">
                        <strong>PleinPasCher</strong>
                     
                    </div>
                </a>
            </div>

            
            <nav id="menu-principal">
                     <ul>
                         <li><a href="carte.php">Carte interactive</a></li>
                         <li><a href="proximite-departement.php">À proximité</a></li>
                         <li><a href="statistiques.php">Statistiques</a></li>
                         
                    </ul>
            </nav>
    
             <div id="mode">
                    <?php if($style==='style-alternatif.css'): ?>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?style=default"><img src="./images/soleil.png" alt="Passer en mode clair" /></a>
                    <?php else: ?>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?style=alternatif"><img src="./images/lune.png" alt="Passer en mode sombre" /></a>
                    <?php endif; ?>

                </div>
        </header>

         
        
        
       
 <main>
        