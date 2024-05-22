<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="logoSite1.png" />
    <meta charset="UTF-8">
    <style>
        .nature-table{
            width: 60%;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nature de demande</title>
</head>
<body>
    <?php
    include( __DIR__."/style/header.php");
    include( __DIR__."/style/footer.php");
    include( __DIR__."/style/nav.php");

    session_start();

    require_once 'db/connexion.php';
    if (!isset($_SESSION['login'])) {
        header('Location: login.php');
        exit();
    }

    $prenom = $_SESSION['prenom'];
    $pole= $_SESSION['idPole'];
    $user_pseudo = $_SESSION['user_pseudo'];
    
    if(!$_SESSION['admin']){
        header('index.php');
        exit();
    }
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["action"]) && isset($_POST["libelle"]))
        {
            $action = $_POST["action"];
            $idNature = $_POST["idNature"];
            $conn = connexionPDO();

            if($action === "modifier" && isset($_POST["libelle"])){
                $libelle = $_POST["libelle"];

                $sql = "UPDATE natureDemande SET libelle = :libelle WHERE idNature = :idNature";
                $result = $conn->prepare($sql);
                $result->bindParam(':libelle', $libelle);
                $result->bindParam(':idNature', $idNature);
                $result->execute();
            } elseif ($action === "supprimer") {
                $sql = "DELETE FROM natureDemande WHERE idNature = :idNature";
                $result = $conn->prepare($sql);
                $result->bindParam(":idNature", $idNature);
                $result->execute();
            }
            $conn = null;

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } elseif (isset($_POST['nouveau_libelle']) && !empty($_POST["nouveau_libelle"])) {
            $nouveauLibelle = $_POST["nouveau_libelle"];

            $conn = connexionPDO();
            $sqlVerif = "SELECT libelle FROM natureDemande WHERE libelle = :libelle";
            $stmtVerif = $conn->prepare($sqlVerif);
            $stmtVerif->bindParam(":libelle", $nouveauLibelle);
            $stmtVerif->execute();
        
            if ($stmtVerif->rowCount() == 0) {
                $sql = "INSERT INTO natureDemande (libelle) VALUES (:libelle)";
                $result = $conn->prepare($sql);
                $result->bindParam(":libelle", $nouveauLibelle);
                $result->execute();

                $conn = null;

                header("location: " . $_SERVER["PHP_SELF"]);
                exit();
            }else{
                echo 'la nature de demande existe déja !';
            }
        }
    }


    $conn = connexionPDO();
    $sql = "SELECT idNature, libelle FROM natureDemande";
    $result = $conn->query($sql);

    if($result->rowCount() > 0 && $result !==false){
        echo "<p>".$prenom.'</p>' ;
        echo'<h1>Gestion de Nature de Demande</h1></br>';
        echo '<table class ="nature-table">';
        echo '<tr>';
        echo '<th>ID Nature Demande</th>';
        echo '<th>Nature de demande</th>';
        echo '</tr>';

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            echo '<tr>';
            echo '<td>'.htmlspecialchars($row['idNature']).'</td>';
            echo '<td>';
            echo '<form class="nature-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            echo '<input type="hidden" name="idNature" value="' . $row['idNature'] . '">';
            echo '<input type="text" name="libelle" value="' . htmlspecialchars($row['libelle']) . '">';
                echo '<button type="submit" class="btn btn-primary" name="action" value="modifier">Sauvegarder</button> ';
                echo '<button type="submit" class="btn btn-danger" name="action" value="supprimer">Supprimer</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td></td>';
        echo '<td>';
        echo '<form class="natureForm" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="text" name="nouveau_libelle" placeholder="Nouvelle nature de demande">';
        echo '<input type="submit" value="Ajouter">';
        echo '</form>';
        echo '</td>';
        
        echo '</tr>';

        echo '</table>';

    } else {
        // Aucun pôle trouvé dans la base de données
        echo '<p>Aucun nature de demande trouvé.</p>';
        echo '<tr>';
        echo '<td></td>';
        echo '<td>';
        echo '<form class="natureForm" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="text" name="nouveau_libelle" placeholder="Nouvelle nature de demande">';
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
