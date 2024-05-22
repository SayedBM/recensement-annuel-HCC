
<?php
if(isset($_POST['Export_table'])){

    require_once 'db/connexion.php';
    $conn = connexionPDO();

}
session_start();
$tableArchive = $_SESSION['tableName'];
// Headers for download
$nomFichier = "$tableArchive";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; Filename = $nomFichier.xls");
?>
<?php
require "db/connexion.php";
?>
<table>
<tr>
    <td>numemro</td>
    <td>ID Demande</td>
    <td>Pole</td>
    <td>N UF</td>
    <td>Description</td>
    <td>Justificatifs</td>
    <td>Localite</td>
    <td>Priorite</td>
    <td>Remplacement</td>
    <td>N du materiel</td>
    <td>Nature de la demande</td>
    <td>Nom du demandeur</td>
    <td>Prenom du demandeur</td>
    <td>Telephone du demandeur</td>
</tr>
<?php
$conn = connexionPDO();
$stmt = $conn->query("SELECT idDemande, LibellePole, numUF, description, justificatifs, localite, priorite, IF(remplacement=1, 'OUI', 'NON') AS Remplacement, numPoste , 	libelleNatureDemande, 	matriculeDemandeur, nomDemandeur, numTelDemandeur FROM $tableArchive");
$i=1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
?>
    <tr>
        <td> <?php echo "$i" ?></td>
        <td><?php echo isset($row["idDemande"]) ? $row["idDemande"] : ''; ?></td>
        <td><?php echo isset($row["LibellePole"]) ? $row["LibellePole"] : ''; ?></td>
        <td><?php echo isset($row["numUF"]) ? $row["numUF"] : ''; ?></td>
        <td><?php echo isset($row["description"]) ? $row["description"] : ''; ?></td>
        <td><?php echo isset($row["justificatifs"]) ? $row["justificatifs"] : ''; ?></td>
        <td><?php echo isset($row["localite"]) ? $row["localite"] : ''; ?></td>
        <td><?php echo isset($row["priorite"]) ? $row["priorite"] : ''; ?></td>
        <td><?php echo isset($row["Remplacement"]) ? $row["Remplacement"] : ''; ?></td>
        <td><?php echo isset($row["numPoste"]) ? $row["numPoste"] : ''; ?></td>
        <td><?php echo isset($row["libelleNatureDemande"]) ? $row["libelleNatureDemande"] : ''; ?></td>
        <td><?php echo isset($row["matriculeDemandeur"]) ? $row["matriculeDemandeur"] : ''; ?></td>
        <td><?php echo isset($row["nomDemandeur"]) ? $row["nomDemandeur"] : ''; ?></td>
        <td><?php echo isset($row["numTelDemandeur"]) ? $row["numTelDemandeur"] : ''; ?></td>
    </tr>
   <?php $i++;?>
<?php endwhile; ?>
</table>



