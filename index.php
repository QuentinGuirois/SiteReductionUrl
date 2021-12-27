<!-- Envoi de formulaire -->
<?php
//On vérifie que le shortcut existe au moins une fois
if (isset($_GET['q'])) {
    $shortcut = htmlspecialchars($_GET['q']); // capture et Stock dans une variable protégée
    //IS A SHORTCUT ?
    try {
        $bdd = new PDO('mysql:host=sqlprive-pc2372-001.privatesql.ha.ovh.net:3306;dbname=cefiidev1175;charset=utf8', 'cefiidev1175', 'y34Dmn3E');
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    $req = $bdd->prepare('SELECT COUNT (*) AS x FROM linksBitly WHERE shortcut = ?');
    $req->execute(array($shortcut));
}
if (!empty($_POST["url"])) {
    $url = $_POST["url"];

    //VERIFICATION URL... s'execute si l'adresse n'est pas bonne
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // PAS UN LIEN
        header('location:/?error=true&message=Adresse url non valide');
        exit(); //Arrêt du script
    }
    //CREATION RACCOURCIS
    $shortcut = crypt($url, rand());
    //...SI PAS DEJA CREE
    try {
        $bdd = new PDO('mysql:host=sqlprive-pc2372-001.privatesql.ha.ovh.net:3306;dbname=cefiidev1175;charset=utf8', 'cefiidev1175', 'y34Dmn3E');
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    $req = $bdd->prepare('SELECT COUNT (*) AS x FROM linksBitly WHERE url = ?');
    $req->execute(array($url));

    while ($result = $req->fetch()) {
        if ($result['x'] != 0) {
            header('location:/?error=true&message=Adresse déjà raccourcie');
            exit();
        }
    }

    // ENVOI BDD
    $req = $bdd->prepare('INSERT INTO linksBitly (url, shortcut) VALUES(?, ?)');
    $req->execute(array($url, $shortcut));

    while ($result = $req->fetch()) {

        if ($result['x'] != 1) {
            header('location:/?error=true&message=Adresse url non connue');
            exit();
        }
    }

    //REDIRECTION 
    $req = $bdd->prepare('SELECT * FROM linksBitly WHERE shortcut = ?');
    $req->execute(array($shortcut));
    while($result = $req->fetch()){
        header('location: '.$result['url']);
        exit();
    }

    header('location:/?short=' . $shortcut);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raccourcisseur URL</title>
    <link rel="stylesheet" type="text/css" href="design/style.css" media="all" />
    <link rel="icon" type="image/png" href="design/img/favico.png">
    <script src="jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>

</head>

<body>
    <section id="hello">
        <div class="container">
            <header>
                <img id="logo" src="design/img/logo.png" alt="logo" />
                <h1>Une url longue ? Raccourcissez-là !</h1>
                <h2>Largement plus court que les autres</h2>
                <form method="POST" action="">
                    <input type="url" name="url" placeholder="Renseignez votre url" />
                    <input type="submit" value="Raccourcir" />
                </form>
                <?php
                if (isset($_GET['error']) && isset($_GET['message'])) {
                ?>
                    <div class="center">
                        <div id="result">
                            <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                        </div>
                    </div>
                <?php } else if (isset($_GET['short'])) { ?>
                    <div class="center">
                        <div id="result">
                            <b>URL RACCOURCIE : https://www.cefii-developpements.fr/quentin1175/autres/siteReducUrl/?q=</b>
                        <?php echo htmlspecialchars($_GET['short']);
                    } ?>
                        </div>
                    </div>
            </header>
        </div>

    </section>
    <section id="brands">
        <div class="container">
            <h3>Ces marques nous font confiance</h3>
            <img src="design/img/1.png" alt="1" class="picture" />
            <img src="design/img/2.png" alt="2" class="picture" />
            <img src="design/img/3.png" alt="3" class="picture" />
            <img src="design/img/4.png" alt="4" class="picture" />
        </div>
    </section>
    <footer>
        <div class="container">
            <img id="logoFooter" src="design/img/logo2.png" alt="logo2" /><br />
            2018 © Bitly<br />
            <a href="#">Contact</a> - <a href="#">A propos</a>


        </div>


    </footer>


</body>

</html>