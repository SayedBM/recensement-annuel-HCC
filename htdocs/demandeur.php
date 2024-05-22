<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="logoSite1.png" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <title>Liste des demandeurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>

 <?php
include( __DIR__."/style/header.php");
include( __DIR__."/style/footer.php");
include( __DIR__."/style/nav.php");

 ?>
    <div >
        
        <?php 
    
        session_start();

        // Vérifier la connexion de l'utilisateur
        if (!isset($_SESSION['login'])) {
            header('Location: login.php');
            exit();
        }
        
            $prenom = $_SESSION['prenom'];
            $pole = $_SESSION['idPole'];

            echo "<p>".$prenom. '</p>' ;

            echo'<h1>Liste des Demandeurs </h1></br>';
        require_once 'db/connexion.php';
        $prenom = $_SESSION['prenom'];
        $pole= $_SESSION['idPole'];
        $user_pseudo = $_SESSION['user_pseudo'];

        // Gestion des soumissions de formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn = connexionPDO();
            
            if (isset($_POST["action"])) {
                $action = $_POST["action"];
                
                // Traitement pour modifier un demandeur existant
                if ($action === "modifier" && isset($_POST["idDemandeur"], $_POST["admin"])) {
                    $idDemandeur = $_POST["idDemandeur"];
                    $admin = $_POST["admin"];

                    $sql = "UPDATE demandeur SET admin = :admin WHERE idDemandeur = :idDemandeur";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':admin', $admin);
                    $stmt->bindParam(':idDemandeur', $idDemandeur);
                    $stmt->execute();
                } 
                // Traitement pour supprimer un demandeur existant
                elseif ($action === "supprimer" && isset($_POST["idDemandeur"])) {
                    $idDemandeur = $_POST["idDemandeur"];
                    $sql = "DELETE FROM demandeur WHERE idDemandeur = :idDemandeur";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idDemandeur', $idDemandeur);
                    $stmt->execute();

                    header("location: " . $_SERVER["PHP_SELF"]);
                    exit();
                }
            }
            // Traitement pour ajouter un nouveau demandeur
            elseif (isset($_POST['nouveau_nom'], $_POST['nouveau_prenom'], $_POST['nouveau_idPole'], $_POST['nouveau_numTel'], $_POST['nouveau_admin'])) {
                $nouveauNom = $_POST["nouveau_nom"];
                $nouveauPrenom = $_POST["nouveau_prenom"];
                $nouveauIdPole = $_POST["nouveau_idPole"];
                $nouveauNumTel = $_POST["nouveau_numTel"];
                $nouveauAdmin = $_POST["nouveau_admin"];

                $sql = "INSERT INTO demandeur (nom, prenom, idPole, numTel, admin) VALUES (:nom, :prenom, :idPole, :numTel, :admin)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nom', $nouveauNom);
                $stmt->bindParam(':prenom', $nouveauPrenom);
                $stmt->bindParam(':idPole', $nouveauIdPole);
                $stmt->bindParam(':numTel', $nouveauNumTel);
                $stmt->bindParam(':admin', $nouveauAdmin);
                $stmt->execute();
                header("location: " . $_SERVER["PHP_SELF"]);
                exit();
            }
        }
        ?>

        <?php
        // Récupérer les demandeurs depuis la base de données
        $conn = connexionPDO();
        $sql = "SELECT demandeur.idDemandeur, demandeur.nom, demandeur.prenom, pole.libelle as pole, demandeur.numTel, IF(demandeur.admin = 1, 'Oui', 'Non') as admin FROM demandeur INNER JOIN pole ON demandeur.idPole = pole.idPole";
        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
            // Afficher les demandeurs dans un tableau
            echo '<table >';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th >ID Demandeur</th>';
            echo '<th >Matricule</th>';
            echo '<th >Prénom Nom</th>';
            echo '<th >Pôle</th>';
            echo '<th >Numéro de Téléphone</th>';
            echo '<th >Admin</th>';
            echo '<th ></th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<th >' .htmlspecialchars($row['idDemandeur'] ) . '</th>';
                echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
                echo '<td>' .htmlspecialchars($row['prenom'])  . '</td>';
                echo '<td>' .htmlspecialchars($row['pole'] ) . '</td>';
                echo '<td>' . htmlspecialchars($row['numTel']) . '</td>';
                echo '<td>' . htmlspecialchars($row['admin']) . '</td>';
                echo '<td>';
                echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
                echo '<input type="hidden" name="idDemandeur" value="' . $row['idDemandeur'] . '">';
                echo '<input type="number" name="admin" placeholder="1 pour admin, 0 sinon" value="' . htmlspecialchars($row['admin']) . '" min="0" max="1">';
                echo '<button type="submit" class="btn btn-primary" name="action" value="modifier">Sauvegarder</button> ';
                echo '<button type="submit" class="btn btn-danger" name="action" value="supprimer">Supprimer</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            // Aucun demandeur trouvé dans la base de données
            echo '<p>Aucun demandeur trouvé.</p>';
        }

        // Fermer la connexion à la base de données
        $conn = null;
        

// Formulaire pour ajouter un nouveau demandeur
echo '</br><h2>Ajouter un nouveau demandeur</h2></br>';
echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
echo '<label for="nouveau_nom">Nom:</label>';
echo '<input type="text" name="nouveau_nom" id="nouveau_nom" required>';
echo '<label for="nouveau_prenom">Prénom:</label>';
echo '<input type="text" name="nouveau_prenom" id="nouveau_prenom" required>';

// Récupérer les données pour la liste déroulante de pôle
$poles = array(); // Initialiser un tableau vide
try {
    $conn = connexionPDO();
    $sqlpole = "SELECT idPole, libelle from pole";
    $stmtpole = $conn->query($sqlpole);
    $poles = $stmtpole->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer les erreurs éventuelles
    echo "Erreur : " . $e->getMessage();
}

echo '<label for="nouveau_idPole">Pôle :</label>';
echo '<select name="nouveau_idPole"  required>';
echo '<option value="" selected disabled>Sélectionnez un pôle</option>';
foreach ($poles as $pole) {
    echo '<option value="' . $pole['idPole'] . '">' . $pole['libelle'] . '</option>';
}
echo '</select><br>';

echo '<label for="nouveau_numTel">Numero de téléphone :</label>';
echo '<input type="text" name="nouveau_numTel" id="nouveau_numTel" required>';
echo '<label for="nouveau_admin">Admin:</label>';
echo '<input type="number" name="nouveau_admin" id="nouveau_admin" min="0" max="1" placeholder="1 pour Admin, 0 sinon" required>';
echo '<input type="submit" name="action" value="ajouter">';
echo '</form>';

?>
<div class="espaceVide"></div> <!-- Espace vide -->


<?php 
// Inclure le footer

?>
</body>
</html>
