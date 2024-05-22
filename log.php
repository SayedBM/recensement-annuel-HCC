<?php

$host = 'hcc-pasteur.fr';
$domain = "hcc-pasteur";
                /*
function validAdminUser($uname) {
    // Retourne vrai si l'utilisateur appartient au groupe support/it/systems
    $validAdminUser = false;
    //$arr_usr_groups = get_groups($uname);
    foreach($arr_usr_groups as $group) {
        if(strpos(strtolower($group), "cn=AppliWEB-Besoins_Annuels") !== false) {
            $validAdminUser = true;
            break;
        }
    }
    return $validAdminUser;
}
*/
    session_start();
    $username = $_POST['user_pseudo'];
    $password = $_POST['user_password'];
function ldap_login($username, $password) {
    global $host, $port, $protocol, $base_dn, $domain;
    if ($username && $password) {
        $connection_string = "$protocol://$host:$port";
        $conn = @ldap_connect($connection_string) or $msg = "Impossible de se connecter: $connection_string";
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
    
        $ldaprdn = $username.$domain;
        $ldapbind = @ldap_bind($conn, $ldaprdn, $password);
        if ($ldapbind) {
            // Vérifie l'appartenance au groupe
            $group_dn = "CN=AppliWEB-Besoins_Annuels,OU=WEB,OU=Applicatifs,OU=Groupes,OU=_HCC,DC=hcc-pasteur,DC=fr";
            $filter = "(&(objectClass=user)(sAMAccountName=$username)(memberOf=$group_dn))";
            $search = ldap_search($conn, $base_dn, $filter);
            if ($search) {
                $result = ldap_get_entries($conn, $search);
                if ($result['count'] > 0) {
                    $returnval = 10000; // "Succès"
                    echo 'OK';
                }
                else {
                    $returnval = -1; // "Utilisateur non trouvé dans le groupe"
                    echo 'Utilisateur non trouvé dans le groupe"';
                }
            }
            else {
                $returnval = -1; // "Erreur lors de la recherche LDAP"
                echo 'Erreur lors de la recherche LDAP"';
            }
        }
        else {
            $returnval = 0; // "Nom d'utilisateur/mot de passe incorrect"
            echo 'Nom d\'utilisateur/mot de passe incorrect';
        }    
    }
    else {
        $returnval = -1; // "Veuillez saisir un nom d'utilisateur/mot de passe"
        
    }
    
    return $returnval;    
}

/*
function get_groups($user) {
    global $host, $port, $protocol, $base_dn, $domain;

    // Utilise un utilisateur admin dans LDAP pour la requête
    $username = "infostage1";
    $password = "St4ge1nfo";
    
    // Serveur Active Directory
    $connection_string = "$protocol://$host:$port";
 
    // DN Active Directory, chemin de base pour notre utilisateur de requête
    $ldap_dn = $base_dn;
 
    // Utilisateur Active Directory pour la requête
    $query_user = "$username@$domain;
    $password = $password;
 
    // Connexion à AD
    $ldap = ldap_connect($connection_string) or die("Impossible de se connecter à LDAP");
    ldap_bind($ldap,$query_user,$password) or die("Impossible de se lier à LDAP");
 
    // Recherche dans AD
    $results = ldap_search($ldap,$ldap_dn,"(samaccountname=$user)",array("memberof","primarygroupid"));
    $entries = ldap_get_entries($ldap, $results);
    
    // Aucune information trouvée, mauvais utilisateur
    if($entries['count'] == 0) return false;
    
    // Obtient les groupes et le jeton de groupe principal
    $output = $entries[0]['memberof'];
    $token = $entries[0]['primarygroupid'][0];
    
    // Supprime l'entrée superflue, c'est-à-dire le nombre de groupes auxquels l'utilisateur appartient
    array_shift($output);
    
    // On doit rechercher le groupe principal, obtenir la liste de tous les groupes
    $results2 = ldap_search($ldap,$ldap_dn,"(objectcategory=group)",array("distinguishedname","primarygrouptoken"));
    $entries2 = ldap_get_entries($ldap, $results2);
    
    // Supprime l'entrée superflue
    array_shift($entries2);
    
    // Parcourir et trouver le groupe avec un jeton de groupe principal correspondant
    foreach($entries2 as $e) {
        if($e['primarygrouptoken'][0] == $token) {
            // Groupe principal trouvé, l'ajoute au tableau de sortie
            $output[] = $e['distinguishedname'][0];
            // Casse la boucle
            break;
        }
    }

    return $output;
}
 */