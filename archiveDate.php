<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login/login.php');
    exit;
}

require_once __DIR__.'/db/connexion.php';
$conn = connexionPDO();

$date_table = date("Y_m_d");
$new_table_name = "archive_".$date_table;

// Vérifier si l'utilisateur a confirmé l'archivage
if (isset($_POST['confirm_archive'])) {
    try {
        $sql_create_table = "CREATE TABLE $new_table_name(
            idDemande INT AUTO_INCREMENT PRIMARY KEY,
            libellePole TEXT,
            numUF INT,
            description TEXT,
            justificatifs TEXT,
            localite INT,
            priorite INT,
            remplacement TEXT,
            numPoste TEXT,
            libelleNatureDemande TEXT,
            matriculeDemandeur TEXT,
            nomDemandeur TEXT,
            numTelDemandeur INT
        )";
        $stmt_create_table = $conn->prepare($sql_create_table);
        $stmt_create_table->execute();
          
        // Copier les données de la table existante vers la nouvelle table
        $sql_copy_data = "INSERT INTO $new_table_name SELECT idDemande, pole.libelle as libPol, numUF, description, justificatifs, localite, priorite, IF(demande.remplacement=1, 'OUI', 'NON') as Remplacement, numPoste, natureDemande.libelle, demandeur.nom, demandeur.prenom, demandeur.numTel FROM demande join demandeur on demande.idDEmandeur= demandeur.idDemandeur join natureDemande on demande.idNature = natureDemande.idNature join pole on demandeur.idPole = pole.idPole";
        $stmt_copy_data = $conn->prepare($sql_copy_data);
        $stmt_copy_data->execute();

        // Afficher un message de succès
        $_SESSION['message'] = 'Les données ont été sauvegardées dans la table (archive_'.date("Y_m_d").') avec succès.';
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        $_SESSION['message'] = "Erreur lors de la sauvegarde des données ! Vous ne pouvez pas sauvegarder les données plus d'une fois par jour !";
        header("Location: index.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Confirmation d'archivage</title>
    <script>
        function confirmerArchive() {
            if (confirm("Êtes-vous sûr de vouloir archiver les données ?")) {
                document.getElementById("archive-form").submit();
            }
        }
    </script>
</head>
<body>

<?php include("style/header.php");
include("style/footer.php");
include("style/nav.php");

?>
    <h1>Confirmation d'archivage</h1>
    <p>Avant d'archiver les données, veuillez confirmer votre action :</p>
    <form id="archive-form" method="post">
        <input type="hidden" name="confirm_archive" value="1">
        <button type="button" onclick="confirmerArchive()">Archiver les données</button>
    </form>

    <div class="espaceVide"></div> <!-- Espace vide -->
    <div class="espaceVide"></div> <!-- Espace vide -->
    <div class="espaceVide"></div> <!-- Espace vide -->
    <div class="espaceVide"></div> <!-- Espace vide -->
</body>
</html>
