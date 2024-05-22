<!DOCTYPE html>
<html lang="fr">
<head>
    
	<link rel="shortcut icon" href="logoSite1.png" />
    <meta charset="UTF-8">
    <style>
        .nature-table {
            width: 60%;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date de demande</title>
</head>
<body>
<?php
include(__DIR__ . "/style/header.php");
include(__DIR__ . "/style/footer.php");
include(__DIR__ . "/style/nav.php");



session_start();
$prenom = $_SESSION['prenom'];
echo "<p>".$prenom. '</p>' ; 
echo'<h1>Gestion de la Date de Clôture des Demandes</h1>';
require_once 'db/connexion.php';
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connexionPDO();
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
        if ($action === "modifier" && isset($_POST["id_dated"]) && isset($_POST["nouvelle_dated"])) {
            $idDated = $_POST["id_dated"];
            $nouvelleDated = $_POST["nouvelle_dated"];
            $sql = "UPDATE dated SET dateD = :nouvelle_dated WHERE id = :id_dated";
            $result = $conn->prepare($sql);
            $result->bindParam(':nouvelle_dated', $nouvelleDated);
            $result->bindParam(':id_dated', $idDated);
            $result->execute();
        } elseif ($action === "supprimer" && isset($_POST["id_dated"])) {
            echo '<script>';
            echo 'if (confirm("Voulez-vous vraiment supprimer cette date ?")) {';
            echo 'document.getElementById("delete-form-' . $idDated . '").submit();';
            echo '}';
            echo '</script>';

            $idDated = $_POST["id_dated"];
            $sql = "DELETE FROM dated WHERE id = :id_dated";
            $result = $conn->prepare($sql);
            $result->bindParam(":id_dated", $idDated);
            $result->execute();
        }
        $conn = null;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['nouvelle_dated']) && !empty($_POST["nouvelle_dated"])) {
        $nouvelleDated = $_POST["nouvelle_dated"];
        $conn = connexionPDO();
        $sql = "INSERT INTO dated (dateD) VALUES (:dateD)";
        $result = $conn->prepare($sql);
        $result->bindParam(":dateD", $nouvelleDated);
        $result->execute();
        $conn = null;
        header("location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
}

$conn = connexionPDO();
$sql = "SELECT id, dateD FROM dated";
$result = $conn->query($sql);

if ($result->rowCount() > 0 && $result !== false) {
    echo '<table class ="nature-table">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Date de fin demande</th>';
    echo '</tr>';

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>';
        echo '<form class="nature-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="hidden" name="id_dated" value="' . $row['id'] . '">';
        echo '<input type="date" name="nouvelle_dated" value="' . htmlspecialchars($row['dateD']) . '"></br>';
        echo '<button type="submit" class="btn btn-primary" name="action" value="modifier">Sauvegarder</button> ';
        echo '<button type="submit" class="btn btn-danger" name="action" value="supprimer">Supprimer</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
        echo '<form id="delete-form-' . $row['id'] . '" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="hidden" name="id_dated" value="' . $row['id'] . '">';
        echo '</form>';
    }
    echo '<tr>';
    echo '<td></td>';
    echo '<td>';
    echo '<form class="natureForm" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
    echo '<input type="date" name="nouvelle_dated" placeholder="Nouvelle dated de demande" required>';
    echo '<input type="submit" value="Ajouter">';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
} else {
    echo '<p>Aucune dated de demande trouvé.</p>';
    echo '<form class="natureForm" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
    echo '<input type="date" name="nouvelle_dated" placeholder="Nouvelle dated de demande" required>';
    echo '<input type="submit" value="Ajouter">';
    echo '</form>';
}
echo"<p>*C'est la date ayant l'ID le plus grand qui sera la date de fin pour faire des demandes.</p>";
// Fermeture de la connexion à la base de données
$conn = null;
?>
<div class="espaceVide"></div> <!-- Espace vide -->
</body>
</html>
