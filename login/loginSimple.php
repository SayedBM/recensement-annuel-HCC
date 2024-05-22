<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['login'])) {
    // Rediriger vers la page d'accueil
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Traiter les données du formulaire uniquement si la méthode de requête est POST
    if (isset($_POST['user_pseudo'], $_POST['user_password'], $_POST['idPole'], $_POST['numTel'])) {
        $user_pseudo = $_POST['user_pseudo'];
        $user_password = $_POST['user_password'];
        $idPole = $_POST['idPole'];
        $numTel = $_POST['numTel'];

        // Inclure le fichier de connexion à la base de données
        require_once __DIR__.'/../db/connexion.php';
        $conn = connexionPDO();

        // Vérifier si le nom d'utilisateur et le mot de passe correspondent dans la base de données
        $stmt = $conn->prepare("SELECT * FROM demandeur WHERE nom = :user_pseudo AND prenom = :user_password AND idPole = :idPole");
        $stmt->bindParam(':user_pseudo', $user_pseudo);
        $stmt->bindParam(':user_password', $user_password);
        $stmt->bindParam(':idPole', $idPole);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Les informations d'identification sont valides

            // Récupérer le prénom et le nom de l'utilisateur depuis la base de données
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nomPrenom = $row['prenom'] . ' ' . $row['nom'];

            // Enregistrer les informations de l'utilisateur dans la session
            $_SESSION["login"] = true;
            $_SESSION["user_pseudo"] = $user_pseudo;
            $_SESSION['idPole'] = $idPole;
            $_SESSION['numTel'] = $numTel;
            $_SESSION['prenom'] = $nomPrenom;

            // Rediriger vers la page d'accueil
            header("Location: ../index.php");
            exit();
        } else {
            // Les informations d'identification sont incorrectes, afficher un message d'erreur
            $_SESSION['error_message'] = "Nom d'utilisateur ou mot de passe incorrect.";
            header("Location: login.php");
            exit();
        }

    } else {
        // Champs non définis, afficher un message d'erreur
        $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
        header("Location: login.php");
        exit();
    }
} else {
    // Méthode de requête incorrecte, afficher un message d'erreur
    $_SESSION['error_message'] = "Méthode de requête incorrecte.";
    header("Location: login.php");
    exit();
}