<?php 
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Connexion à la base de données</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        
    </style>
</head>
<body>";

try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $perf = $_POST['perf'] ?? '';
    $annee1 = $_POST['annee1'] ?? '';
    $annee2 = $_POST['annee2'] ?? '';
    $confederation = $_POST['confederation'] ?? '';

    if(isset($_POST['perf'])) {
        if ($perf == "nombre de victoire") {
            $sqlVictoires = "
            SELECT nation.nom AS vainqueur, COUNT(competition.id_vainqueur) as nombre_victoire  
            FROM competition
            LEFT JOIN nation ON nation.id_nation=competition.id_vainqueur
            WHERE competition.annee BETWEEN :annee1 AND :annee2
            ";

            $params = [
                ':annee1' => $annee1,
                ':annee2' => $annee2
            ];

            if (!empty($confederation)) {
                $sqlVictoires .= " AND nation.confederation = :confederation";
                $params[':confederation'] = $confederation;
            }

            $sqlVictoires .= " GROUP BY nation.nom ORDER BY nombre_victoire DESC ";

            $result = $base->prepare($sqlVictoires);
            $result->execute($params);

            echo "<h1>Top nations victoires de " . htmlspecialchars($annee1) . " à " . htmlspecialchars($annee2) . "</h1>";
            echo "<table>
                    <tr>
                        <th>Pays</th>
                        <th>Nombre de victoires</th>
                    </tr>";

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['vainqueur']) . "</td>
                        <td>" . htmlspecialchars($row['nombre_victoire']) . "</td>
                    </tr>";
            }
            echo "</table>";
        }
        else if ($perf == "nombre de buts") {
            $sqlButs = "
                SELECT nation.nom AS pays, COUNT(but.id_but) AS total_buts
                FROM but
                LEFT JOIN joueur ON joueur.id_joueur = but.id_joueur
                LEFT JOIN nation ON joueur.id_nation = nation.id_nation
                LEFT JOIN `match` ON but.id_match = `match`.id_match
                LEFT JOIN competition ON `match`.id_competition = competition.id_competition
                WHERE competition.annee BETWEEN :annee1 AND :annee2";

            $params = [
                ':annee1' => $annee1,
                ':annee2' => $annee2
            ];

            if (!empty($confederation)) {
                $sqlButs .= " AND nation.confederation = :confederation";
                $params[':confederation'] = $confederation;
            }

            $sqlButs .= " GROUP BY nation.nom
                         ORDER BY total_buts DESC";

            $result = $base->prepare($sqlButs);
            $result->execute($params);

            echo "<h1>Top nations buts de " . htmlspecialchars($annee1) . " à " . htmlspecialchars($annee2) . "</h1>";
            echo "<table>
                    <tr>
                        <th>Pays</th>
                        <th>Nombre de buts</th>
                    </tr>";

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['pays']) . "</td>
                        <td>" . htmlspecialchars($row['total_buts']) . "</td>
                    </tr>";
            }
            echo "</table>";
        }
    }
} catch (Exception $e) {
    echo "<h1>World Cup Tracker</h1>
          <div class='message'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body>
</html>";
?>