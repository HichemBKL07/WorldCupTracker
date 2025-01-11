<?php 
session_start();
if (isset($_SESSION['id_utilisateur'])) {

} else {
    header('Location: connexion_inscription.php');
    exit();
}
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>recherche match</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        
        .match-container {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #007BFF;
        }
    </style>
</head>
<body>";

/*if (isset($_POST['pays1']) && !empty($_POST['pays1']) && isset($_POST['pays2']) && !empty($_POST['pays2'])) {*/
    
    $pays1 = $_POST['pays1'];
    $pays2 = $_POST['pays2'];
    
    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "<h1>$pays1 vs $pays2</h1>";
    
        $stmt = "
            SELECT 
                `match`.id_match,
                `match`.url_match,
                `match`.date_match,
                `match`.score_local,
                `match`.score_visiteur,
                local_nation.nom AS nom_nation_locale,
                visitor_nation.nom AS nom_nation_visiteur,
                stade.nom AS nom_stade
            FROM `match`
            JOIN nation AS local_nation ON `match`.id_nation_local = local_nation.id_nation
            JOIN nation AS visitor_nation ON `match`.id_nation_visiteur = visitor_nation.id_nation
            JOIN stade ON `match`.id_stade = stade.id_stade
        ";
    
        $params = [];
    
        if (!empty($pays1) && !empty($pays2)) {
            $stmt .= "
                WHERE (local_nation.nom = :pays1 AND visitor_nation.nom = :pays2)
                   OR (local_nation.nom = :pays2 AND visitor_nation.nom = :pays1)";
            $params[':pays1'] = $pays1;
            $params[':pays2'] = $pays2;
        } elseif (!empty($pays1)) {
            $stmt .= "
                WHERE (local_nation.nom = :pays1)
                   OR (visitor_nation.nom = :pays1)";
            $params[':pays1'] = $pays1;
        } elseif (!empty($pays2)) {
            $stmt .= "
                WHERE (local_nation.nom = :pays2)
                   OR (visitor_nation.nom = :pays2)";
            $params[':pays2'] = $pays2;
        }
    
        $stmt .= " ORDER BY `match`.date_match DESC";
    
        $resultat = $base->prepare($stmt);
        $resultat->execute($params);        
        if ($resultat->rowCount() > 0) {
            echo "<div class='message'>";
            echo "Résultat(s) trouvé(s) : <br>";
            while ($row = $resultat->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='match-container'>";
                echo htmlspecialchars($row['nom_nation_locale']) . " " . htmlspecialchars($row['score_local']) . " - " .
                     htmlspecialchars($row['score_visiteur']) . " " . htmlspecialchars($row['nom_nation_visiteur']) . "<br>" .
                     "Date : " . htmlspecialchars($row['date_match']) . "<br>" .
                     "Stade : " . htmlspecialchars($row['nom_stade']) . "<br>";
                
                echo "<a href='detail_match.php?id_match=" . htmlspecialchars($row['id_match']) . "' class='details-btn'>Voir les détails</a>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div class='message'>Aucun match trouvé entre $pays1 et $pays2.</div>";
        }
        
    } catch (Exception $e) {
        echo "<h1>World Cup Tracker</h1>";
        echo "<div class='message'>Erreur : Connexion à la base de données impossible</div>";
    }
/*}*/

echo "</body></html>";
?>