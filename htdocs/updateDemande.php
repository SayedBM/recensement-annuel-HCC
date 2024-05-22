<?php
require_once 'db/connexion.php';
session_start();
$id = (int)$_GET['id'];


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header('Location: login/login.php');
    exit;
}
// Récupérer les données existantes
$sql = "SELECT * FROM demande WHERE idDemande = ?";
$conn = connexionPDO();
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les données pour la liste déroulante de nature
$sqlNature = "SELECT idNature, libelle FROM natureDemande";
$stmtNature = $conn->query($sqlNature);
$natures = $stmtNature->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les nouvelles valeurs des champs
    $description = $_POST['description'];
    $justificatifs = $_POST['justificatifs'];
    $localite = $_POST['localite'];
    $priorite = $_POST['priorite'];
    $remplacement = isset($_POST['remplacement']) ? 1 : 0;
    $numPoste = $remplacement ? $_POST['num_poste'] : null;
    $idNature = $_POST['id_nature'];

    // Mettre à jour les champs dans la base de données
    $sql = "UPDATE demande SET description = ?, justificatifs = ?, localite = ?, priorite = ?, remplacement = ?, numPoste = ?, idNature = ? WHERE idDemande = ?";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$description, $justificatifs, $localite, $priorite, $remplacement, $numPoste, $idNature, $id]);

    if ($result) {
        echo 'La demande a été mise à jour avec succès.';
        header('location: index.php');
    } else {
        echo 'Erreur lors de la mise à jour de la demande.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="logoSite1.png" />
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une demande</title>
    <script>
        function toggleNumPoste() {
            var numPosteField = document.getElementById("num_poste_field");
            numPosteField.style.display = document.getElementById("remplacement").checked ? "block" : "none";
        }
    </script>
</head>
<body>

    <?php
    require 'style/header.php'; require 'style/footer.php';
    ?>

    <h1>Modifier une demande</h1>
    <form method="POST">
    <label for="id_nature">Nature :</label>
        <select name="id_nature">
            <?php foreach ($natures as $nature) : ?>
                <option value="<?php echo $nature['idNature']; ?>" <?php if ($nature['idNature'] == $row['idNature']) echo 'selected'; ?>><?php echo $nature['libelle']; ?></option>
            <?php endforeach; ?>
        </select><br>
        
        <label for="description">Description :</label>
        <input type="text" name="description" value="<?php echo $row['description']; ?>"><br>

        <label for="justificatifs" >Justificatifs : </label>

        <input type="text" name="justificatifs" rows="10" size = "120" maxlength="1000" value="<?php echo $row['justificatifs']; ?>"><br>

        <label for="localite">Localité :</label>
        <input type="text" name="localite" value="<?php echo $row['localite']; ?>"><br>

        <label for="priorite">Priorité :</label>
        <input type="number" name="priorite" placeholder="de 1 à 3 "  min="1" max="3" value="<?php echo $row['priorite']; ?>" required ><br>


        <label for="remplacement">Remplacement :</label>
        <input type="checkbox" name="remplacement" id="remplacement" value="1" <?php if ($row['remplacement'] == 1) echo 'checked'; ?> onclick="toggleNumPoste()"><br>

        <div id="num_poste_field" style="display: <?php echo $row['remplacement'] == 1 ? 'block' : 'none'; ?>">
            <label for="num_poste">Numéro de matériel :</label>
            <input type="text" name="num_poste" value="<?php echo $row['numPoste']; ?>"><br>
        </div>

        

        <input type="submit" name="submit" value="Enregistrer les modifications">
    </form>
    <div class="espaceVide"></div> <!-- Espace vide -->
</body>
</html>
