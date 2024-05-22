<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['login'])) {
    // Rediriger vers la page d'accueil
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../style/styl.css">
    <title>Connexion</title>
</head>
<body>
<header>
    <div class="header-content">
        <img src="../Logo.png" alt="Icône">
        <h1>Recensements Annuel</h1>
        <div class="logout-container">
        </div>
    </div>
</header>
<h1>Connexion</h1>

<?php
// Afficher un message d'erreur s'il y en a un
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
    unset($_SESSION['error_message']); 
}


?>

<form method="post" action="loginSimple.php">
    <h3>Connectez-vous avec votre compte d'utilisateur</h3>
    <?php
    // Afficher les valeurs saisies par l'utilisateur s'il y a eu une erreur
    $user_pseudo = isset($_SESSION['user_pseudo']) ;
    $numTel = isset($_SESSION['numTel']) ? $_SESSION['numTel'] : '';
    ?>
    <label for="idPole">Pôle :*</label>
    <select name="idPole" id="user_idpole" required>
        <option value="" selected disabled>Sélectionnez un pôle</option>
        <?php
        // Inclure le fichier de connexion à la base de données
        require_once __DIR__.'/../db/connexion.php';

        // Connexion à la base de données
        $conn = connexionPDO();

        // Récupérer les données pour la liste déroulante de pole
        $poles = array(); // Initialiser un tableau vide
        try {
            $sqlpole = "SELECT idPole,libelle from pole";
            $stmtpole = $conn->query($sqlpole);
            $poles = $stmtpole->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Gérer les erreurs éventuelles
            echo "Erreur : " . $e->getMessage();
        }

        foreach ($poles as $pole) {
            $selected = ($pole['idPole'] == $user_pole) ? 'selected' : '';
            echo "<option value='{$pole['idPole']}' $selected>{$pole['libelle']}</option>";
        }
        ?>
    </select><br>

    <label for="username">Nom d'utilisateur :*</label>
    <input type="text" id="user_pseudo" name="user_pseudo" required value="<?php echo htmlspecialchars($user_pseudo); ?>">
    <label for="password">Mot de passe :*</label>
    <input type="password" id="user_password" name="user_password" required placeholder=""><br>
    <label for="numTel">N° de téléphone :*</label>
    <input type="number" id="numTel" name="numTel" max="99999" title=" 5 chiffres "maxlength="5"  placeholder="12345" required value="<?php echo htmlspecialchars($numTel); ?>">
    <input type="submit" value="Se connecter"></br>
    <font size="-1">Les champs suivis d'un * sont obligatoires.</font>
</form>
<div class="espaceVide"></div> <!-- Espace vide -->
</body>
</html>
