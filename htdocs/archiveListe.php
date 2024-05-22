<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des données de la table</title>
</head>
<body>
<?php
session_start();
include("style/header.php");
include("style/footer.php");
include("style/nav.php");
?>
<h1>Affichage des données de la table</h1>

<form method="post">
    <label for="liste_tables">Choisissez une table :</label>
    <select name="liste_tables" id="liste_tables">
    <?php
    // Connexion à la base de données
    require_once 'db/connexion.php';
    $conn = connexionPDO();

    // Récupération de la liste des tables commençant par "archive"
    $sql_tables = "SHOW TABLES LIKE '%archiv%'";
    $stmt_tables = $conn->prepare($sql_tables);
    $stmt_tables->execute();
    $tables = $stmt_tables->fetchAll(PDO::FETCH_COLUMN);

    // Affichage des options de la liste déroulante
    foreach ($tables as $table) {
        echo '<option value="' . $table . '">' . $table . '</option>';
    }

    // Fermeture de la connexion
    $conn = null;
    ?>
    </select>
    <input type="submit" name="submit" value="Afficher">
</form>

<?php
// Vérifier si l'utilisateur a soumis le formulaire
if (isset($_POST['submit'])) {
    // Récupérer le nom de la table sélectionnée
    $selected_table = $_POST['liste_tables'];

    // Connexion à la base de données
    require_once 'db/connexion.php';
    $conn = connexionPDO();

    // Requête pour récupérer toutes les données de la table sélectionnée
    $sql = "SELECT * FROM " . $selected_table;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des données dans un tableau
    echo '<h2>Données de la table ' . $selected_table . '</h2>';
    echo '<table border="1">';
    // En-têtes de colonnes
    echo '<tr>';
    foreach ($data[0] as $key => $value) {
        echo '<th>' . $key . '</th>';
    }
    echo '</tr>';
    // Contenu de la table
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';

    // Bouton pour supprimer la table avec confirmation JavaScript
    echo '<form method="post" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette table ?\');">';
    echo '<input type="hidden" name="table_name" value="' . $selected_table . '">';
    echo '<input type="submit" name="delete_table" class="btn btn-danger" value="Supprimer la table">';
    echo ' ';
    echo '<a href="exportArchive.php" class="btn btn-primary">Exporter vers Excel </a>';
    echo '</form>';

    // Fermeture de la connexion
    $conn = null;
}

$_SESSION['tableName'] = $selected_table;
// Vérifier si l'utilisateur a soumis le formulaire de suppression de table
if (isset($_POST['delete_table'])) {
    // Récupérer le nom de la table à supprimer
    $table_name = $_POST['table_name'];

    // Connexion à la base de données
    require_once 'db/connexion.php';
    $conn = connexionPDO();

    // Requête pour supprimer la table
    $sql_delete = "DROP TABLE " . $table_name;
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->execute();

    // Fermeture de la connexion
    $conn = null;

    // Affichage d'un message de confirmation
    echo '<p>La table ' . $table_name . ' a été supprimée avec succès.</p>';
}
?>

<div class="espaceVide"></div> <!-- Espace vide -->
<div class="espaceVide"></div> <!-- Espace vide -->
</body>
</html>
