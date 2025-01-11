<?php

session_start();

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Coupe du Monde</title>
    <link rel='stylesheet' href='styles.css'>

    <style>
        .phase {
            margin: 20px 0;
            text-align: center;
        }
        .bracket {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 50px;
        }
        .round {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .match {
            background-color: #007BFF;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #007BFF;
            text-align: center;
            width: 250px;
            text-decoration: none; 
            color: #2B2B2B; 
            transition: background-color 0.3s, color 0.3s;
            cursor: pointer; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap:5px;
        }
        .match:hover {
            background-color: #003d80; 
            color: #FFFFFF; 
        }
        .trophee {
            width: 20px;
            height: auto;
        }
    </style>
</head>
<body>";

if (isset($_POST['annee']) && !empty($_POST['annee'])) {
    $annee = $_POST['annee'];
    try {       
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql2 = $base->prepare("
        SELECT pays_organisateur
        FROM competition
        WHERE annee=:annee
        ");
        $sql2->bindParam(':annee', $annee, PDO::PARAM_INT);
        $sql2->execute();
        $resultat = $sql2->fetch(PDO::FETCH_ASSOC);
        $hote =  $resultat['pays_organisateur'];
        echo "<h1>Coupe du Monde $annee ($hote) </h1>";

        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
        $sql1 = $base->prepare("SELECT id_competition FROM competition WHERE annee = :annee");
        $sql1->bindParam(':annee', $annee, PDO::PARAM_INT);
        $sql1->execute();
        $competition = $sql1->fetch(PDO::FETCH_ASSOC);

        if ($competition) {
            $id_competition = $competition['id_competition'];

            $phases = ['Group stage', 'Round of 16', 'Quarter-finals', 'Semi-finals', 'Final'];

            echo "<div class='bracket'>"; 

            foreach ($phases as $phase) {
                echo "<div class='phase'>";
                echo "<h2>$phase</h2>";
                
                $matchQuery = $base->prepare("
                    SELECT m.id_match, m.tour, m.score_local, m.score_visiteur, nl.nom AS equipe_locale, nv.nom AS equipe_visiteur, competition.pays_organisateur AS hote, competition.id_vainqueur AS gagnant, nl.id_nation AS loc 
                    FROM `match` m
                    JOIN nation nl ON m.id_nation_local = nl.id_nation
                    JOIN nation nv ON m.id_nation_visiteur = nv.id_nation
                    JOIN competition ON m.id_competition= competition.id_competition
                    WHERE m.id_competition = :id_competition AND m.tour = :tour
                    ORDER BY m.date_match
                ");
                $matchQuery->bindParam(':id_competition', $id_competition, PDO::PARAM_INT);
                $matchQuery->bindParam(':tour', $phase, PDO::PARAM_STR);
                $matchQuery->execute();
                
                $matches = $matchQuery->fetchAll();

                if (!empty($matches)) {
                    echo "<div class='round'>";
                    foreach ($matches as $match) {
                        echo "<a class='match' href='detail_match.php?id_match=" . urlencode($match['id_match']) . "'>";
                        if ($phase == 'Final') {
                            if (isset($match['gagnant']) && $match['gagnant'] == $match['loc']) {
                                echo "<img src='pngegg.png' alt='Trophée' class='trophee'>";
                                echo "<span><strong>{$match['equipe_locale']}</strong> {$match['score_local']} - {$match['score_visiteur']} <strong>{$match['equipe_visiteur']}</strong></span>";
                            } else {
                                echo "<span><strong>{$match['equipe_locale']}</strong> {$match['score_local']} - {$match['score_visiteur']} <strong>{$match['equipe_visiteur']}</strong></span>";
                                echo "<img src='pngegg.png' alt='Trophée' class='trophee'>";
                            }
                        } else {
                            echo "<span><strong>{$match['equipe_locale']}</strong> {$match['score_local']} - {$match['score_visiteur']} <strong>{$match['equipe_visiteur']}</strong></span>";
                        }
                        echo "</a>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>Aucun match trouvé pour la phase $phase.</p>";
                }
                echo "</div>";
            }
            echo "</div>"; 
        } else {
            echo "<div class='message'>Aucune compétition trouvée pour l'année $annee</div>";
        }
    } catch (Exception $e) {
        echo "<div class='message'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
}
echo "</body></html>";
?>
