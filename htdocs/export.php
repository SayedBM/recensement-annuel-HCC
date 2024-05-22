<?php
// Headers for download
$nomFichier = "Liste_Demandes_".date('d-m-Y');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; Filename = $nomFichier.xls");
?>
<?php
require "db/connexion.php";
?>
<table>
<tr>
    <td>Date</td>
    <td>ID Demande</td>
    <td>Pôle</td>
    <td>N° UF</td>
    <td>Description</td>
    <td>Justificatifs</td>
    <td>Localité</td>
    <td>Priorité</td>
    <td>Remplacement</td>
    <td>N° de Poste</td>
    <td>Nature de la demande</td>
    <td>Nom du demandeur</td>
    <td>Prénom du demandeur</td>
    <td>Téléphone du demandeur</td>
</tr>
<?php
$conn = connexionPDO();
$stmt = $conn->query("SELECT dateD.dated, idDemande, pole.libelle AS libPol, numUF, description, justificatifs, localite, priorite, IF(demande.remplacement=1, 'OUI', 'NON') AS Remplacement, numPoste, natureDemande.libelle, demandeur.nom, demandeur.prenom, demandeur.numTel FROM demande JOIN demandeur ON demande.idDEmandeur = demandeur.idDemandeur JOIN natureDemande ON demande.idNature = natureDemande.idNature JOIN pole ON demandeur.idPole = pole.idPole LEFT JOIN dated ON demande.idDate = dated.id ORDER BY idDate DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
?>
    <tr>
        <td><?php echo isset($row["dated"]) ? $row["dated"] : ''; ?></td>
        <td><?php echo isset($row["idDemande"]) ? $row["idDemande"] : ''; ?></td>
        <td><?php echo isset($row["libPol"]) ? $row["libPol"] : ''; ?></td>
        <td><?php echo isset($row["numUF"]) ? $row["numUF"] : ''; ?></td>
        <td><?php echo isset($row["description"]) ? $row["description"] : ''; ?></td>
        <td><?php echo isset($row["justificatifs"]) ? $row["justificatifs"] : ''; ?></td>
        <td><?php echo isset($row["localite"]) ? $row["localite"] : ''; ?></td>
        <td><?php echo isset($row["priorite"]) ? $row["priorite"] : ''; ?></td>
        <td><?php echo isset($row["Remplacement"]) ? $row["Remplacement"] : ''; ?></td>
        <td><?php echo isset($row["numPoste"]) ? $row["numPoste"] : ''; ?></td>
        <td><?php echo isset($row["libelle"]) ? $row["libelle"] : ''; ?></td>
        <td><?php echo isset($row["nom"]) ? $row["nom"] : ''; ?></td>
        <td><?php echo isset($row["prenom"]) ? $row["prenom"] : ''; ?></td>
        <td><?php echo isset($row["numTel"]) ? $row["numTel"] : ''; ?></td>
    </tr>
<?php endwhile; ?>
</table>



