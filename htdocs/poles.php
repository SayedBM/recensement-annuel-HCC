<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="shortcut icon" href="logoSite1.png" />
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Pôles</title>
</head>
<body>
    
    <?php
    include( __DIR__."/style/header.php");
    include( __DIR__."/style/footer.php");
    session_start();
    include( __DIR__."/style/nav.php");
    // Inclusion du fichier de connexion PDO
    require_once __DIR__.'/db/connexion.php';
    $admin= 'infostage';

    // Vérification de la connexion de l'utilisateur
    if (!isset($_SESSION['login'])&& $_SESSION['user_pseudo']!=$admin ) {
        // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
        header('Location: login.php');
        exit;
    }

    // Récupération des informations de session
    $prenom =$_SESSION["prenom"];
    $pole = $_SESSION['idPole'];
    $user_pseudo = $_SESSION["user_pseudo"];
    
    // Traitement des actions de modification et de suppression si soumises
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification de l'action à effectuer
        if (isset($_POST["action"]) && isset($_POST["idPole"])) {
            $action = $_POST["action"];
            $idPole = $_POST["idPole"];

            // Connexion à la base de données
            $conn = connexionPDO();

            if ($action === "modifier" && isset($_POST["libelle"])) {
                // Modification du libellé
                $libelle = $_POST["libelle"];
                $sql = 'UPDATE pole SET libelle = :libelle WHERE idPole = :idPole';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':libelle', $libelle);
                $stmt->bindParam(':idPole', $idPole);
                $stmt->execute();
            } elseif ($action === "supprimer") {
                // Suppression du pôle
                $sql = 'DELETE FROM pole WHERE idPole = :idPole';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idPole', $idPole);
                $stmt->execute();
            }

            // Fermeture de la connexion à la base de données
            $conn = null;

            // Redirection pour éviter la soumission multiple du formulaire
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } elseif (isset($_POST["nouveau_libelle"]) && !empty($_POST["nouveau_libelle"])) {
            // Ajout d'un nouveau pôle
            $nouveauLibelle = $_POST["nouveau_libelle"];
        
            // Connexion à la base de données
            $conn = connexionPDO();
        
            // Vérification si le libellé du nouveau pôle existe déjà
            $sqlVerif = "SELECT libelle FROM pole WHERE libelle = :libelle";
            $stmtVerif = $conn->prepare($sqlVerif);
            $stmtVerif->bindParam(":libelle", $nouveauLibelle);
            $stmtVerif->execute();
        
            if ($stmtVerif->rowCount() == 0) {
                // Le libellé du nouveau pôle n'existe pas encore, on peut l'ajouter
                $sql = 'INSERT INTO pole (libelle) VALUES (:libelle)';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':libelle', $nouveauLibelle);
                $stmt->execute();
        
                // Fermeture de la connexion à la base de données
                $conn = null;
        
                // Redirection pour éviter la soumission multiple du formulaire
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                // Le libellé du nouveau pôle existe déjà
                echo 'Le pôle existe déjà.';
            }
        }
        
    }

    // Connexion à la base de données
    $conn = connexionPDO();

    // Préparation et exécution de la requête SQL pour récupérer les pôles
    $sql = 'SELECT idPole, libelle FROM pole';
    $result = $conn->query($sql);

    // Vérification s'il y a des résultats
    if ($result !== false && $result->rowCount() > 0) {
        // Affichage du nom d'utilisateur et du pôle
		echo "<p>".$prenom. '</p>' ;
        ?>
        <h1>Gestion de Pôle</h1></br>
    <?php
        // Affichage du tableau des pôles
        echo '<table class="pole-table">';
        echo '<tr>';
        echo '<th>ID Pôle</th>';
        echo '<th>Pôle</th>';
        echo '</tr>';

        // Boucle à travers les résultats pour afficher les données
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['idPole']) . '</td>';
            echo '<td>';
            echo '<form class="pole-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            echo '<input type="hidden" name="idPole" value="' . $row['idPole'] . '">';
            echo '<input type="text" name="libelle" value="' . htmlspecialchars($row['libelle']) . '">';
            echo '<button type="submit" class="btn btn-primary" name="action" value="modifier">Sauvegarder</button> ';
            echo '<button type="submit" class="btn btn-danger" name="action" value="supprimer">Supprimer</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        // Formulaire pour ajouter un nouveau pôle
        echo '<tr>';
        echo '<td></td>';
        echo '<td>';
        echo '<form class="pole-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="text" name="nouveau_libelle" placeholder="Nouveau pôle">';
        echo '<input type="submit" value="Ajouter">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
    } else {
        // Aucun pôle trouvé dans la base de données
        echo '<p>Aucun pôle trouvé.</p>';
        echo '<tr>';
        echo '<td></td>';
        echo '<td>';
        echo '<form class="pole-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="text" name="nouveau_libelle" placeholder="Nouveau pôle">';
        echo '<input type="submit" value="Ajouter">';
        echo '</form>';
        echo '</td>';
        echo '<td></td>'; // Cellule vide pour aligner les colonnes
        echo '</tr>';
    }


    // Fermeture de la connexion à la base de données
    $conn = null;
    ?>
    <div class="espaceVide"></div> <!-- Espace vide -->

</body>
</html>
