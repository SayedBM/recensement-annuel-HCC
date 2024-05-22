<?php 
// Load the database configuration file 
require_once 'db/connexion.php';

// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 

$conn= connexionPDO();

// Excel file name for download 
$fileName = "members-data_" . date('Y-m-d') . ".xls"; 

// Column names 
$fields = array('N°', 'Pole', 'Nature de demande', 'Description', 'Justificatifs', 'Localite', 'Priorite', 'Remplacement', 'N° de Materiel', 'Matricule', 'Demandeur', 'Telephone de demandeur' ); 

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 

// Fetch records from database 
$query = $conn->query("SELECT pole.libelle as libPol, description, justificatifs, localite, priorite, remplacement,numPoste, natureDemande.libelle,demandeur.nom, demandeur.prenom, demandeur.numTel FROM demande join demandeur on demande.idDEmandeur= demandeur.idDemandeur join natureDemande on demande.idNature = natureDemande.idNature join pole on demandeur.idPole = pole.idPole");
if($query->rowCount()> 0){ 
    // Output each row of the data 
    $count = 1;
    while($row = $query->fetch(PDO::FETCH_ASSOC)){ 
        $lineData = array($count++, $row['libPol'], $row['libelle'], $row['description'], $row['justificatifs'], $row['localite'], $row['priorite'], $row['remplacement'],$row['numPoste'],$row['nom'],$row['prenom'],$row['numTel']); 
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    } 
}else{ 
    $excelData .= 'No records found...'. "\n"; 
} 

// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 

// Render excel data 
echo $excelData; 

exit; 
?>
