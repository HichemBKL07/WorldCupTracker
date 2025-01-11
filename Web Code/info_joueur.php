<?php

function defini($input) {
    return $input ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : '';
}

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Informations sur le joueur</title>
    <link rel='stylesheet' href='styles.css'>
</head>
<body>";

if ((isset($_GET['id_joueur']) && !empty($_GET['id_joueur'])) || (isset($_POST['id_joueur']) && !empty($_POST['id_joueur']))) {
    $id_joueur = isset($_GET['id_joueur']) ? $_GET['id_joueur'] : $_POST['id_joueur'];


    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $base->prepare("
            SELECT 
                j.nom, 
                j.prenom, 
                j.poste, 
                n.nom AS nation_nom,
                COALESCE(COUNT(DISTINCT CASE WHEN b.id_joueur = j.id_joueur AND b.csc IS NULL THEN b.id_but END), 0) AS total_buts,
                COALESCE(COUNT(DISTINCT CASE WHEN b.id_passeur = j.id_joueur THEN b.id_but END), 0) AS passeD,
                COALESCE(COUNT(DISTINCT f.id_faute), 0) AS nb_fautes,
                COALESCE(COUNT(DISTINCT tdj.id_match), 0) AS matches_played,
                COALESCE(SUM(tdj.temps_de_jeu), 0) AS temps_total
            FROM joueur j
            JOIN nation n ON j.id_nation = n.id_nation
            LEFT JOIN but b ON j.id_joueur = b.id_joueur OR j.id_joueur = b.id_passeur
            LEFT JOIN faute f ON j.id_joueur = f.id_joueur
            LEFT JOIN temps_de_jeu tdj ON j.id_joueur = tdj.id_joueur
            WHERE j.id_joueur = :id_joueur
            GROUP BY j.id_joueur
        ");
        $stmt->execute(['id_joueur' => $id_joueur]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h1>Informations sur le joueur</h1>";
            echo "<table>
                    <tr><th>Critère</th><th>Valeur</th></tr>
                    <tr><td><strong>Prénom</strong></td><td>" . defini($row['prenom']) . "</td></tr>
                    <tr><td><strong>Nom</strong></td><td>" . defini($row['nom']) . "</td></tr>
                    <tr><td><strong>Poste</strong></td><td>" . defini($row['poste']) . "</td></tr>
                    <tr><td><strong>Nation</strong></td><td>" . defini($row['nation_nom']) . "</td></tr>
                    <tr><td><strong>Nombre de buts marqués</strong></td><td>" . (int)$row['total_buts'] . "</td></tr>
                    <tr><td><strong>Nombre de passes décisives</strong></td><td>" . (int)$row['passeD'] . "</td></tr>
                    <tr><td><strong>Nombre de cartons</strong></td><td>" . (int)$row['nb_fautes'] . "</td></tr>
                    <tr><td><strong>Nombre de matches joués</strong></td><td>" . (int)$row['matches_played'] . "</td></tr>
                    <tr><td><strong>Temps de jeu total (min) </strong></td><td>" . (int)$row['temps_total'] . " </td></tr>
                </table>";

            $stmtMatches = $base->prepare("
                SELECT 
                    m.date_match,
                    m.heure,
                    m.score_local,
                    m.score_visiteur,
                    pays1.nom AS nom_nation_locale,
                    pays2.nom AS nom_nation_visiteur,
                    m.id_match
                FROM `match` m
                JOIN nation pays1 ON m.id_nation_local = pays1.id_nation
                JOIN nation pays2 ON m.id_nation_visiteur = pays2.id_nation
                JOIN temps_de_jeu tdj ON m.id_match = tdj.id_match
                WHERE tdj.id_joueur = :id_joueur
                ORDER BY m.date_match DESC

            ");
            $stmtMatches->execute(['id_joueur' => $id_joueur]);

            if ($stmtMatches->rowCount() > 0) {
                echo "<h2>Matchs joués</h2>";
                echo "<table>
                        <tr>
                            <th>Date</th>
                            <th>Équipe locale</th>
                            <th>Score</th>
                            <th>Équipe visiteur</th>
                        </tr>";
                
                while ($match = $stmtMatches->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                          <td>" . defini((new DateTime($match['date_match']))->format('d/m/Y')) . "</td>
                          <td>" . defini($match['nom_nation_locale']) . "</td>
                          <td>" . (isset($match['score_local']) && $match['score_local'] !== null && $match['score_local'] !== 0 ? $match['score_local'] : '0') . " - " .
                                (isset($match['score_visiteur']) && $match['score_visiteur'] !== null && $match['score_visiteur'] !== 0 ? $match['score_visiteur'] : '0') . "</td>
                          <td>" . defini($match['nom_nation_visiteur']) . "</td>
                          <td>
                            <a href='detail_match.php?id_match=" . htmlspecialchars($match['id_match']) . "' class='details-btn'>Voir les détails</a>
                          </td>
                      </tr>";

                }

                echo "</table>";
            } else {
                echo "<div class='message'>Aucun match trouvé pour ce joueur.</div>";
            }
        } else {
            echo "<div class='message'>Aucun joueur trouvé avec ces informations.</div>";
        }
    } catch (Exception $e) {
        echo "<h1>World Cup Tracker</h1>";
        echo "<div class='message'>Erreur : " . $e->getMessage() . "</div>";
    }
}

echo "</body></html>";
?>
