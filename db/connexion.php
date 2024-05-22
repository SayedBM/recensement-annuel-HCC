<?php

function connexionPDO()
{
    $login = "root";
    $mdp = "";
    $bd = "recensement";
    $serveur = "localhost";

    try {
        $conn = new PDO(
            "mysql:host=$serveur;dbname=$bd;charset=utf8mb4;port=3306",
            $login,
            $mdp
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        print "Erreur de connexion PDO : " . $e->getMessage();
        die();
    }
}

if ($_SERVER["SCRIPT_FILENAME"] == __FILE__) {
    // Test de la fonction de connexionPDO
    echo "Test de connexionPDO() : \n";
    try {
        $connexion = connexionPDO();
        echo "Connexion réussie !\n";
        print_r($connexion);
    } catch (PDOException $e) {
        echo "Erreur de connexion PDO : " . $e->getMessage();
    }
}

?>