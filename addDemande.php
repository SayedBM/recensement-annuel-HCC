<?php
require_once 'db/connexion.php';
$conn = connexionPDO();
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header('Location: login.php');
    exit;
}
$idDate = $_SESSION['idDate'];
// Récupérer les données pour la liste déroulante de nature
$sqlNature = "SELECT idNature, libelle FROM natureDemande";
$stmtNature = $conn->query($sqlNature);
$natures = $stmtNature->fetchAll(PDO::FETCH_ASSOC);
$nom = $_SESSION['user_pseudo'];
$idPole= $_SESSION['idPole'];
// Récupérer les données pour la liste déroulante de demandeur
$sqlDemandeurs = "SELECT idDemandeur, nom FROM demandeur where nom = '$nom' AND idPole = $idPole ";
$stmtDemandeurs = $conn->query($sqlDemandeurs);
$demandeurs = $stmtDemandeurs->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs des champs
    $numUF = $_POST['numUF'];
    $description = $_POST['description'];
    $justificatifs = $_POST['justificatifs'];
    $localite = $_POST['localite'];
    $priorite = $_POST['priorite'];
    $remplacement = isset($_POST['remplacement']) ? 1 : 0;
    $idNature = $_POST['id_nature'];
    $idDemandeur = $_POST['id_demandeur'];
    
    // Définir la valeur de $numPoste en fonction de la présence du champ "num_poste"
    $numPoste = isset($_POST['num_poste']) ? $_POST['num_poste'] : null;

    try {
        // Préparer la requête d'insertion avec des paramètres
        $sql = "INSERT INTO Demande (description, justificatifs, localite, priorite, remplacement, numPoste, idNature, idDemandeur, numUF,idDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Exécuter la requête préparée avec les valeurs des champs
        $stmt->execute([$description, $justificatifs, $localite, $priorite, $remplacement, $numPoste, $idNature, $idDemandeur, $numUF, $idDate]);

        echo 'La demande a été ajoutée avec succès.';
        header('location: index.php');
    } catch (PDOException $e) {
        // Gérer l'erreur
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="stylesheet" type="text/css" href="style/styl.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une demande</title>
</head>
<body>
    <?php
    require __DIR__.'/style/header.php'; require  __DIR__.'/style/footer.php';
    ?>

    <h1>Ajouter une demande</h1>
    <form method="POST">
        <label for="id_nature">Nature :*</label>
        <select name="id_nature" required>
        <option value="" selected disabled>Sélectionnez la nature de votre demande</option>
            <?php foreach ($natures as $nature) : ?>
                <option value="<?php echo $nature['idNature']; ?>"><?php echo $nature['libelle']; ?>  </option>
            <?php endforeach; ?>
        </select><br>
        <label for="numUF">N° UF :</label>
        <input type="text" name="numUF"  placeholder="N° UF"><br>

        <label for="description">Description :*</label>
        <input type="text" name="description"  placeholder="Description" required><br>

        <label for="justificatifs">Justificatifs :*</label>
        <input type="text" maxlength="50" name="justificatifs" placeholder="Justificatifs" required><br>


        <label for="localite" class="label-localite">Localité :*<a href="n°LC.png" target="_blank" >détail </a> </label>
        <input type="number" name="localite" max="99999999" title="Veuillez saisir 8 chiffres" placeholder="N° LC (8 chiffres)" required>

        <br>

        <label for="priorite">Priorité :*</label>
        <input type="number" name="priorite" placeholder="de 1 à 3 "  min="1" max="3"  required ><br>

        <label for="remplacement">Remplacement :</label>
        <input type="checkbox" name="remplacement" id="remplacement" value="1" onclick="toggleNumPoste()"><br>

        <div id="num_poste_field" style="display: none;">
            <label for="num_poste">Numéro de Materiel :*</label>
            <input type="text" name="num_poste"><br>
        </div>

        <label for="id_demandeur">Demandeur :*</label>
        <select name="id_demandeur">
            <?php foreach ($demandeurs as $demandeur) : ?>
                <option value="<?php echo $demandeur['idDemandeur']; ?>"><?php echo $demandeur['nom']; ?></option>
            <?php endforeach; ?>
        </select><br>
            </br>
        <input type="submit" class="add" name="submit" value="Ajouter la demande">
        <p>Les champs suivis d'une * sont obligatoires.</p>
    </form>
   <!-- <div class="accueil"><a  href="index.php">Accueil</a></div> -->
    
    <div class="espaceVide"></div> <!-- Espace vide -->
    <script>
        function toggleNumPoste() {
            var numPosteField = document.getElementById("num_poste_field");
            if (document.getElementById("remplacement").checked) {
                numPosteField.style.display = "block";
            } else {
                numPosteField.style.display = "none";
            }
        }
    </script>
    <div class="espaceVide"></div> <!-- Espace vide -->

</body>
</html>
