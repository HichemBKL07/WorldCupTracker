<?php

function defini($input) {
    return $input ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : '';
}

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Recherche sur le joueur</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        body {
            background-color: #F0F8FF;
            color: #2B2B2B;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .message {
            background-color: #2B2B2B;
            color: #FFFFFF;
            border-radius: 8px;
            padding: 15px;
            margin: 20px auto;
            text-align: center;
            font-size: 1.1em;
            border: 1px solid #007BFF;
            width: 90%;
        }

        .message form {
            margin: 10px 0;
        }
    </style>
</head>
<body>";

if(isset($_POST['nom_joueur']) && !empty($_POST['nom_joueur']) )
{
    $nom_joueur=$_POST['nom_joueur'];
    $prenom_joueur=$_POST['prenom_joueur'];

    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (empty($prenom_joueur)) {
            $stmt = $base->prepare("
                SELECT joueur.nom, joueur.prenom, joueur.poste, nation.nom as nation_nom, joueur.id_joueur
                FROM joueur 
                JOIN nation ON joueur.id_nation = nation.id_nation
                WHERE joueur.nom = :nom_joueur
            ");
            $stmt->execute(['nom_joueur' => $nom_joueur]);
        } else {
            $stmt = $base->prepare("
                SELECT joueur.nom, joueur.prenom, joueur.poste, nation.nom as nation_nom, joueur.id_joueur
                FROM joueur 
                JOIN nation ON joueur.id_nation = nation.id_nation
                WHERE joueur.nom = :nom_joueur AND joueur.prenom = :prenom_joueur
            ");
            $stmt->execute(['nom_joueur' => $nom_joueur, 'prenom_joueur' => $prenom_joueur]);
        }

        if ($stmt->rowCount() > 0) {
            echo "<div class='message'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "Nom: " . defini($row['nom']) . " | ";
                echo "Prénom: " . (isset($row['prenom']) ? defini($row['prenom']) : "Non spécifié") . " | ";
                echo "Nation: " . defini($row['nation_nom']);
                echo "<form action='info_joueur.php' method='post'>
                    <input type='hidden' name='id_joueur' value='" . defini($row['id_joueur']) . "'>
                    <input type='submit' value='Informations supplémentaires'>
                </form><hr>";
            }
            echo "</div>";
        } else {
            echo "<div class='message'>Aucun joueur trouvé avec ces informations.</div>";
        }
        
    } catch (Exception $e) {
        echo "<h1>World Cup Tracker</h1>";
        echo "<div class='message'>Erreur : Connexion à la base de données impossible</div>";
    }
}
echo "</body></html>";
?>
