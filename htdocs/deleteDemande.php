<?php
require_once 'db/connexion.php';
$conn = connexionPDO();
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header('Location: login/login.php');
    exit;
}
$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si l'utilisateur a confirmé la suppression
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $sql = "DELETE FROM demande WHERE idDemande = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        // Rediriger vers la page d'index après la suppression
        header('location: index.php');
        exit(); // Terminer le script
    } else {
        // Si l'utilisateur a annulé la suppression, rediriger vers la page d'index
        header('location: index.php');
        exit(); // Terminer le script
    }
}
require __DIR__.'/style/footer.php';
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
    <link rel="shortcut icon" href="logoSite1.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="style/styl.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmation de suppression</title>
        <script>
            function confirmDelete() {
                // Afficher une boîte de dialogue de confirmation
                var result = confirm("Êtes-vous sûr de vouloir supprimer cette demande ?");
                // Si l'utilisateur clique sur OK, soumettre le formulaire
                if (result) {
                    return true;
                }
                // Sinon, ne pas soumettre le formulaire
                return false;
            }
        </script>
</head>
<body>
<?php
    require  __DIR__.'/style/header.php';
    ?>

    <h1>Confirmation de suppression</h1>
    <form method="POST" onsubmit="return confirmDelete()">
        <p>Voulez-vous vraiment supprimer cette demande ?</p>
        <input type="hidden" name="confirm" value="yes">
        <button type="submit">Oui</button>
        <a href="index.php">Non</a>
    </form>
</body>
</html>
