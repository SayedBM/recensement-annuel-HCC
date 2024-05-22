<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styl.css">
    <style>
        /* Styles spécifiques au tableau */
        .pole-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pole-table th, .pole-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .pole-table th {
            background-color: #f2f2f2;
        }

        .pole-form {
            display: flex;
            align-items: center;
        }

        .pole-form input[type="text"] {
            padding: 6px;
            margin-right: 8px;
        }

        .pole-form input[type="submit"] {
            padding: 6px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .pole-form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Pôles</title>
</head>
<body>
    <?php
    session_start();

    // Inclusion du fichier de connexion PDO
    require_once 'db/connexion.php';

    // Vérification de la connexion de l'utilisateur
    if (!isset($_SESSION['login'])) {
        // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
        header('Location: login.php');
        exit;
    }

    // Récupération des informations de session
    $prenom = strtolower($_SESSION["prenom"]);
    $pole = $_SESSION['idPole'];
    $user_pseudo = $_SESSION["user_pseudo"];

    // Traitement des actions de modification et de suppression si soumises
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification de l'action à effectuer
        if (isset($_POST["action"]) && isset($_POST["idPole"])) {
            $action = $_POST["action"];
            $idPole = $_POST["idPole"];

            // Connexion à la base de données
            $conn = connexionPDO();

            if ($action === "modifier" && isset($_POST["libelle"])) {
                // Modification du libellé
                $libelle = $_POST["libelle"];
                $sql = 'UPDATE pole SET libelle = :libelle WHERE idPole = :idPole';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':libelle', $libelle);
                $stmt->bindParam(':idPole', $idPole);
                $stmt->execute();
            } elseif ($action === "supprimer") {
                // Suppression du pôle
                $sql = 'DELETE FROM pole WHERE idPole = :idPole';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idPole', $idPole);
                $stmt->execute();
            }

            // Fermeture de la connexion à la base de données
            $conn = null;

            // Redirection pour éviter la soumission multiple du formulaire
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } elseif (isset($_POST["nouveau_libelle"]) && !empty($_POST["nouveau_libelle"])) {
            // Ajout d'un nouveau pôle
            $nouveauLibelle = $_POST["nouveau_libelle"];

            // Connexion à la base de données
            $conn = connexionPDO();

            $sql = 'INSERT INTO pole (libelle) VALUES (:libelle)';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':libelle', $nouveauLibelle);
            $stmt->execute();

            // Fermeture de la connexion à la base de données
            $conn = null;

            // Redirection pour éviter la soumission multiple du formulaire
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Connexion à la base de données
    $conn = connexionPDO();

    // Préparation et exécution de la requête SQL pour récupérer les pôles
    $sql = 'SELECT idPole, libelle FROM pole';
    $result = $conn->query($sql);

    // Vérification s'il y a des résultats
    if ($result !== false && $result->rowCount() > 0) {
        // Affichage du nom d'utilisateur et du pôle
        echo '<p>' . $prenom . ' | Pôle : ' . $pole . '</p>';

        // Affichage du tableau des pôles
        echo '<table class="pole-table">';
        echo '<tr>';
        echo '<th>ID Pôle</th>';
        echo '<th>Pôle</th>';
        echo '<th>Action</th>';
        echo '</tr>';

        // Boucle à travers les résultats pour afficher les données
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['idPole']) . '</td>';
            echo '<td>';
            echo '<form class="pole-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            echo '<input type="hidden" name="idPole" value="' . $row['idPole'] . '">';
            echo '<input type="text" name="libelle" value="' . htmlspecialchars($row['libelle']) . '">';
            echo '<input type="submit" name="action" value="modifier">';
            echo '<input type="submit" name="action" value="supprimer">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        // Formulaire pour ajouter un nouveau pôle
        echo '<tr>';
        echo '<td></td>';
        echo '<td>';
        echo '<form class="pole-form" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        echo '<input type="text" name="nouveau_libelle" placeholder="Nouveau pôle">';
        echo '<input type="submit" value="Ajouter">';
        echo '</form>';
        echo '</td>';
        echo '<td></td>'; // Cellule vide pour aligner les colonnes
        echo '</tr>';

        echo '</table>';
    } else {
        // Aucun pôle trouvé dans la base de données
        echo '<p>Aucun pôle trouvé.</p>';
    }

    // Fermeture de la connexion à la base de données
    $conn = null;
    ?>
</body>
</html>
