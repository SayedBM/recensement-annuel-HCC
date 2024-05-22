<!DOCTYPE html>
<html lang="fr">
<head>

    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recensements Annuel <?php echo date("Y");?></title>
</head>
<body>
<?php
include __DIR__.'/style/header.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Si l'utilisateur n'est pas connecté, redirigeons-le vers la page de connexion
    header('Location: login/login.php');
    exit;
}

// Inclure votre fichier de connexion PDO
require_once __DIR__.'/db/connexion.php';
$conn = connexionPDO();

// Récupérer l'identifiant de l'utilisateur connecté
$user_id = $_SESSION['user_pseudo'];

// Récupérer les informations de l'utilisateur connecté depuis la base de données
$sql_user_info = "SELECT * FROM demandeur WHERE nom = :user_pseudo";
$stmt_user_info = $conn->prepare($sql_user_info);
$stmt_user_info->bindParam(':user_pseudo', $user_id);
$stmt_user_info->execute();
$user_info = $stmt_user_info->fetch(PDO::FETCH_ASSOC);
$idDemandeur = $user_info['idDemandeur'];

// Vérifier si l'utilisateur est administrateur
$is_admin = ($user_info['admin'] == true);

if(isset($_SESSION['idDate'])){
    $idDate = $_SESSION['idDate'];
}

$_SESSION['admin'] = $is_admin;


$conn = connexionPDO();

    // Récupérer la date limite depuis la base de données
    $sqlDate = "SELECT id, dateD FROM dated order by id desc limit 1";
    $stmtDate = $conn->prepare($sqlDate);
    
    $stmtDate->execute();
    $dateLimite = $stmtDate->fetch(); // Fetch the row
    $idDate = $dateLimite['id']; // Assign the value of 'id' column to $idDate
    //echo "$idDate";

    $_SESSION['idDate'] = $dateLimite['id'];
    $idDate = $_SESSION['idDate'];

    $dateTimestamp = strtotime($dateLimite['dateD']);
    $dateObj = new DateTime();
    $dateObj->setTimestamp($dateTimestamp);
 
    echo "<p>Date limite pour faire des demandes " . $dateObj->format('d-m-Y') . "</p>";

if ($is_admin) {
  include  __DIR__.'/style/nav.php';
}

$prenom = $_SESSION['prenom'];
$pole = $_SESSION['idPole'];

echo "<p>"."Bonjour ".$prenom. '</p>' ;

if (isset($_SESSION['message'])) {
    echo '<p style="color: 	#228B22;">' . $_SESSION['message'] . '</p>';
    unset($_SESSION['message']); 
}

$poleDemande = $_SESSION['idPole'];
$nom = $_SESSION['user_pseudo'];
if ($is_admin) {
    $sql = "SELECT dateD.dated,idDemande,dateD.dated, pole.libelle as libPol,numUF, description, justificatifs, localite, priorite, IF(demande.remplacement=1, 'OUI', 'NON') as Remplacement ,numPoste, natureDemande.libelle,demandeur.nom, demandeur.prenom, demandeur.numTel FROM demande join demandeur on demande.idDEmandeur= demandeur.idDemandeur join natureDemande on demande.idNature = natureDemande.idNature join pole on demandeur.idPole = pole.idPole left join dated on demande.idDate = dated.id order by idDate DESC";
} else {
    $sql = "SELECT idDemande,pole.libelle as libPol,numUF, description, justificatifs, localite, priorite, IF(demande.remplacement=1, 'OUI', 'NON') as Remplacement ,numPoste, natureDemande.libelle,demandeur.nom, demandeur.prenom, demandeur.numTel FROM demande 
        JOIN demandeur ON demande.idDEmandeur = demandeur.idDemandeur 
        JOIN natureDemande ON demande.idNature = natureDemande.idNature 
        JOIN pole ON demandeur.idPole = pole.idPole 
        WHERE demandeur.idDemandeur = :idDemandeur and idDate = '$idDate'";

    
}
?>
<h1>Liste des demandes</h1> </br>

<?php


if (!$is_admin) {

    // Comparer la date limite avec la date actuelle
    if ($dateLimite['dateD'] < date("Y-m-d")) {
        // Si la date limite est dépassée, rediriger l'utilisateur vers la page de connexion avec un message d'erreur
        $_SESSION["login"] = false;
        $_SESSION['error_message'] = "La date limite de connexion est dépassée. Veuillez contacter l'administrateur.";
        session_destroy();
        header("Location: dateDepasse.php");
        
        exit;
    }
}
try {
    // Préparer la requête
    $stmt = $conn->prepare($sql);
    if(!$is_admin){
            $stmt->bindParam(':idDemandeur', $idDemandeur);

    }

    // Exécuter la requête
    $stmt->execute();
    
    // Vérifier si des résultats sont retournés
    if ($stmt->rowCount() > 0) {
        // Afficher le tableau des demandes
        echo '<table>';
        echo '<tr>';
        if($is_admin){echo '<th>La Date</th>';};
        echo '<th> Pôle</th>';
        echo '<th> N° UF</th>';
        echo '<th>Nature de la demande</th>';
        echo '<th>Description</th>';
        echo '<th>Justificatifs</th>';
        echo '<th>Localité</th>';
        echo '<th>Priorité</th>';
        echo '<th>Remplacement</th>';
        echo '<th>N° de Materiel</th>';
        echo '<th>Matricule</th>';
        echo '<th>Demandeur</th>';
        echo '<th>téléphone du demandeur</th>';
        echo '<th>Modifier</th>';
        echo '<th>Supprimer</th>';

        echo '</tr>';

        // Parcourir les résultats et afficher chaque demande
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            if($is_admin){echo '<td>'.$row['dated'].'</td>';};
            echo '<td>'.$row['libPol'].'</td>';
            echo '<td>'.$row['numUF'].'</td>';
            echo '<td>'.$row['libelle'].'</td>';
            echo '<td>'.$row['description'].'</td>';
            echo '<td>'.$row['justificatifs'].'</td>';
            echo '<td>'.$row['localite'].'</td>';
            echo '<td>'.$row['priorite'].'</td>';
            echo '<td>'.$row['Remplacement'].'</td>';
            echo '<td>'.$row['numPoste'].'</td>';
            echo '<td>'.$row['nom'].'</td>'; //nom c'est la matricule 
            echo '<td>'.$row['prenom'].'</td>';
            echo '<td>'.$row['numTel'].'</td>';
            echo '<td><a href="updateDemande.php?id='.$row['idDemande'].'" class="btn btn-outline-primary">Modifier</a></td>';
            echo '<td class="supp"><a href="deleteDemande.php?id='.$row['idDemande'].'" class="btn btn-outline-danger">Supprimer</a></td>';
            echo '</tr>';
        }

        echo '</table>';

        echo '</br><tr><td class="btn btn-primary btn-lg btn-block" style="text-align: center;"><a href="addDemande.php" class="btn btn-primary btn-lg btn-block">Ajouter</a></td></tr>';

    } else {
        echo '<p>Aucune demande trouvée.</p>';
        
        echo '<a href="addDemande.php" class="btn btn-primary btn-lg btn-block" >Ajouter une demande</a>';
        
    }
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$conn = null;
?>
<div class="espaceVide"></div> <!-- Espace vide -->

<?php require  __DIR__.'/style/footer.php'; ?>

</body>
</html>
