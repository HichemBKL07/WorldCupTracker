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
    <titleTop Match</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        
    </style>
</head>
<body>";

try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = $base->prepare("
    SELECT 
        m.id_match,
        pays1.nom AS nom_nation_locale,
        pays2.nom AS nom_nation_visiteur,
        pays1.id_nation AS id_nation_local,
        pays2.id_nation AS id_nation_visiteur,
        m.score_local AS score_local,
        m.score_visiteur AS score_visiteur,
        AVG(note.note) AS moyenne
    FROM  note
    JOIN `match` m ON m.id_match = note.id_match
    JOIN  nation pays1 ON m.id_nation_local = pays1.id_nation
    JOIN nation pays2 ON m.id_nation_visiteur = pays2.id_nation
    GROUP BY 
        m.id_match, pays1.nom, pays2.nom, pays1.id_nation, pays2.id_nation, 
        m.score_local, m.score_visiteur
    ORDER BY moyenne DESC
    LIMIT 50
");

$sql->execute();
    $results = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h1>Top match les mieux notés</h1>";
    echo "<table>";
    echo "<tr>
            <th>Nation Locale</th>
            <th>Nation Visiteur</th>
            <th>Score Locale</th>
            <th>Score Visiteur</th>
            <th>Moyenne des Notes</th>
          </tr>";
    
          foreach ($results as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['nom_nation_locale']) . "</td>
                    <td>" . htmlspecialchars($row['nom_nation_visiteur']) . "</td>
                    <td>" . htmlspecialchars($row['score_local']) . "</td>
                    <td>" . htmlspecialchars($row['score_visiteur']) . "</td>
                    <td>" . number_format((float)$row['moyenne'], 2) . "</td>
                    <td>
                        <a href='detail_match.php?id_match=" . htmlspecialchars($row['id_match']) . "' class='details-btn'>Voir les détails</a>
                    </td>
                  </tr>";
        }
        
    
    echo "</table>";

} catch (Exception $e) {
    echo "<h1>World Cup Tracker</h1>
          <div class='message'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body>
</html>";
?>
    
