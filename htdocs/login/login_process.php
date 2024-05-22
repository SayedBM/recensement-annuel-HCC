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

        // Connexion à LDAP
        $ldap_server = 'ldap://wsrvdc01.hcc-pasteur.fr';
        $ldap_domain = 'hcc-pasteur.fr';
        $ldap_basedn = 'dc=hcc-pasteur,dc=fr';
        $ldap_group = 'AppliWEB-Besoins_Annuels';

        // Connexion LDAP
        $ad = ldap_connect($ldap_server) or die('Impossible de se connecter au serveur LDAP.');
        ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

        // Vérification des identifiants LDAP
        $ldap_bind_result = @ldap_bind($ad, "$user_pseudo@$ldap_domain", $user_password);

        if ($ldap_bind_result) {
            // Récupérer le DN de l'utilisateur
            $user_dn = getDN($ad, $user_pseudo, $ldap_basedn);

            // Vérifier l'appartenance à un groupe
            if (checkGroupEx($ad, $user_dn, getDN($ad, $ldap_group, $ldap_basedn))) {
                // L'utilisateur est autorisé

                // Récupérer le prénom et le nom de l'utilisateur depuis LDAP
                $search_result = ldap_search($ad, $user_dn, "(objectclass=*)", array("givenName", "sn"));
                $ldap_info = ldap_get_entries($ad, $search_result);
                $prenom = $ldap_info[0]["givenname"][0];
                $nom = $ldap_info[0]["sn"][0];
                $nomPrenom = $prenom . ' ' . $nom;

                // Enregistrer les informations de l'utilisateur dans la session
                $_SESSION["login"] = true;
                $_SESSION["user_pseudo"] = $user_pseudo;
                $_SESSION['idPole'] = $idPole;
                $_SESSION['numTel'] = $numTel;
                $_SESSION['prenom'] = $nomPrenom;

                // Vérifier si le demandeur existe dans la base de données
                $stmt_check = $conn->prepare("SELECT nom, admin FROM demandeur WHERE nom = :user_pseudo AND idPole = :idPole");
                $stmt_check->bindParam(':user_pseudo', $user_pseudo);
                $stmt_check->bindParam(':idPole', $idPole);
                $stmt_check->execute();

                if ($stmt_check->rowCount() == 0) {
                    // Le demandeur n'existe pas, l'ajouter à la base de données
                    $stmt_insert = $conn->prepare("INSERT INTO demandeur (nom, prenom, idPole, numTel) VALUES (:user_pseudo, :prenom, :idPole, :numTel)");
                    $stmt_insert->bindParam(':user_pseudo', $user_pseudo);
                    $stmt_insert->bindParam(':prenom', $nomPrenom);
                    $stmt_insert->bindParam(':idPole', $idPole);
                    $stmt_insert->bindParam(':numTel', $numTel);
                    $stmt_insert->execute();
                }

                // Rediriger vers la page d'accueil
                header("Location: ../index.php");
                exit();
            } else {
                // L'utilisateur n'est pas autorisé, afficher un message d'erreur
                $_SESSION['error_message'] = "Vous n'avez pas l'autorisation d'accéder à cette application.";
                header("Location: login.php");
                exit();
            }
        } else {
            // Échec de la connexion LDAP, afficher un message d'erreur
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



/*
 * Fonction pour récupérer le DN d'un utilisateur LDAP
 */
function getDN($ad, $samaccountname, $basedn)
{
    $attributes = array('dn');
    $result = ldap_search($ad, $basedn, "(samaccountname={$samaccountname})", $attributes);
    if ($result === FALSE) {
        return '';
    }
    $entries = ldap_get_entries($ad, $result);
    if ($entries['count'] > 0) {
        return $entries[0]['dn'];
    } else {
        return '';
    }
}

/*
 * Fonction pour vérifier l'appartenance à un groupe LDAP
 */
function checkGroupEx($ad, $userdn, $groupdn)
{
    $attributes = array('memberof');
    $result = ldap_read($ad, $userdn, '(objectclass=*)', $attributes);
    if ($result === FALSE) {
        return FALSE;
    }
    $entries = ldap_get_entries($ad, $result);
    if ($entries['count'] <= 0) {
        return FALSE;
    }
    if (empty($entries[0]['memberof'])) {
        return FALSE;
    } else {
        for ($i = 0; $i < $entries[0]['memberof']['count']; $i++) {
            if ($entries[0]['memberof'][$i] == $groupdn) {
                return TRUE;
            } elseif (checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn)) {
                return TRUE;
            }
        }
    }
    return FALSE;
}
?>
