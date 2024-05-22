<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" type="text/css" href="style/styl.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    include("style/header.php");
    include("style/footer.php");

    session_start();
    echo'<h1> La date limite pour soumettre vos demandes est passée !</h1>';
    echo'<div class="espaceVide"></div>';
    echo'<p> Pour toutes questions, veuillez contacter le service informatique.</p>';
    echo'<div class="espaceVide"></div>';
    echo'<div class="espaceVide"></div>';
    echo'<div class="espaceVide"></div>';
    echo'<div class="espaceVide"></div>';
    
    ?>
</body>
</html>