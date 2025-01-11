<?php
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Performance des joueurs</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        
        
       
    </style>
</head>
<body>";

try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "<h1>Performance des joueurs</h1>";

    $performance = $_POST['performance'];
    $annee1 = $_POST['annee1'];
    $annee2 = $_POST['annee2'];
    $confederation = $_POST['confederation'];
    $pays = $_POST['pays'];
    $params = [':annee1' => $annee1, ':annee2' => $annee2];

    if ($performance == "meilleur buteur") {
        $sql = "SELECT joueur.nom, joueur.prenom, COUNT(but.id_but) as total_buts
                FROM joueur, `match`, competition, but, nation
                WHERE joueur.id_joueur = but.id_joueur
                AND but.id_match = match.id_match
                AND match.id_competition = competition.id_competition
                AND joueur.id_nation = nation.id_nation
                AND competition.annee BETWEEN :annee1 AND :annee2";

        if (!empty($confederation)) {
            $sql .= " AND nation.confederation = :confederation";
            $params[':confederation'] = $confederation;
        }
        if (!empty($pays)) {
            $sql .= " AND nation.nom = :pays";
            $params[':pays'] = $pays;
        }

        $sql .= " GROUP BY joueur.id_joueur
                ORDER BY total_buts DESC
                LIMIT 10";

        $resultat = $base->prepare($sql);
        $resultat->execute($params);

        echo "<table>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Total des buts</th>
                </tr>";

        if ($resultat->rowCount() > 0) {
            while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                       <td>" . htmlspecialchars($ligne['prenom'] ?? '') . "</td>
                       <td>" . htmlspecialchars($ligne['nom'] ?? '') . "</td>
                       <td>" . htmlspecialchars($ligne['total_buts'] ?? '') . "</td>
                      </tr>";

            }
        } else {
            echo "<tr><td colspan='3'>Aucun résultat trouvé pour les critères sélectionnés.</td></tr>";
        }
        echo "</table>";
    } elseif ($performance == "meilleur passeur") {
        $sql = "SELECT joueur.nom, joueur.prenom, COUNT(but.id_passeur) AS nb_passes_decisives
                FROM joueur, `match`, competition, but, nation
                WHERE joueur.id_joueur = but.id_passeur
                AND but.id_match = match.id_match
                AND match.id_competition = competition.id_competition
                AND joueur.id_nation = nation.id_nation
                AND competition.annee BETWEEN :annee1 AND :annee2";

        if (!empty($confederation)) {
            $sql .= " AND nation.confederation = :confederation";
            $params[':confederation'] = $confederation;
        }
        if (!empty($pays)) {
            $sql .= " AND nation.nom = :pays";
            $params[':pays'] = $pays;
        }

        $sql .= " GROUP BY joueur.id_joueur
                ORDER BY nb_passes_decisives DESC
                LIMIT 10";

        $resultat = $base->prepare($sql);
        $resultat->execute($params);

        echo "<table>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Passes décisives</th>
                </tr>";

        if ($resultat->rowCount() > 0) {
            while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                       <td>" . htmlspecialchars($ligne['prenom'] ?? '') . "</td>
                        <td>" . htmlspecialchars($ligne['nom']) . "</td>
                        <td>" . htmlspecialchars($ligne['nb_passes_decisives']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Aucun résultat trouvé pour les critères sélectionnés.</td></tr>";
        }
        echo "</table>";
    }
    elseif($performance == "le plus de cartons") {
            $sql = "SELECT joueur.nom, joueur.prenom, COUNT(faute.id_joueur) as nb_fautes
            FROM joueur, `match`, competition, nation, faute
    		WHERE joueur.id_joueur = faute.id_joueur
            AND faute.id_match = match.id_match
            AND match.id_competition = competition.id_competition
            AND joueur.id_nation = nation.id_nation
            AND competition.annee BETWEEN :annee1 AND :annee2";

            if (!empty($confederation)) {
                $sql .= " AND nation.confederation = :confederation";
                $params[':confederation'] = $confederation;
            }
            if (!empty($pays)) {
                $sql .= " AND nation.nom = :pays";
                $params[':pays'] = $pays;
            }

            $sql .= " GROUP BY joueur.id_joueur
                    ORDER BY nb_fautes DESC
                    LIMIT 10";

            $resultat = $base->prepare($sql);
            $resultat->execute($params);

            echo "<table>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Cartons </th>
                        </tr>";

                if ($resultat->rowCount() > 0) {
                    while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                       <td>" . htmlspecialchars($ligne['prenom'] ?? '') . "</td>
                                <td>" . htmlspecialchars($ligne['nom']) . "</td>
                                <td>" . htmlspecialchars($ligne['nb_fautes']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun résultat trouvé pour les critères sélectionnés.</td></tr>";
                }
                echo "</table>";
    } 
    elseif($performance == "le plus de cartons jaunes") {
        $sql = 'SELECT joueur.nom, joueur.prenom, COUNT(faute.id_joueur) as nb_fautes
        FROM joueur, `match`, competition, nation, faute
        WHERE joueur.id_joueur = faute.id_joueur
        AND faute.id_match = match.id_match
        AND match.id_competition = competition.id_competition
        AND joueur.id_nation = nation.id_nation
        AND competition.annee BETWEEN :annee1 AND :annee2
        AND type_faute = "yellow_card" ';

        if (!empty($confederation)) {
            $sql .= " AND nation.confederation = :confederation";
            $params[':confederation'] = $confederation;
        }
        if (!empty($pays)) {
            $sql .= " AND nation.nom = :pays";
            $params[':pays'] = $pays;
        }

        $sql .= " GROUP BY joueur.id_joueur
                ORDER BY nb_fautes DESC
                LIMIT 10";

        $resultat = $base->prepare($sql);
        $resultat->execute($params);

        echo "<table>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Cartons jaunes </th>
                    </tr>";

            if ($resultat->rowCount() > 0) {
                while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                   <td>" . htmlspecialchars($ligne['prenom'] ?? '') . "</td>
                            <td>" . htmlspecialchars($ligne['nom']) . "</td>
                            <td>" . htmlspecialchars($ligne['nb_fautes']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucun résultat trouvé pour les critères sélectionnés.</td></tr>";
            }
            echo "</table>";
    }
    elseif($performance == "le plus de cartons rouges") {
        $sql = 'SELECT joueur.nom, joueur.prenom, COUNT(faute.id_joueur) as nb_fautes
        FROM joueur, `match`, competition, nation, faute
        WHERE joueur.id_joueur = faute.id_joueur
        AND faute.id_match = match.id_match
        AND match.id_competition = competition.id_competition
        AND joueur.id_nation = nation.id_nation
        AND competition.annee BETWEEN :annee1 AND :annee2
        AND type_faute = "red_card" ';

        if (!empty($confederation)) {
            $sql .= " AND nation.confederation = :confederation";
            $params[':confederation'] = $confederation;
        }
        if (!empty($pays)) {
            $sql .= " AND nation.nom = :pays";
            $params[':pays'] = $pays;
        }

        $sql .= " GROUP BY joueur.id_joueur
                ORDER BY nb_fautes DESC
                LIMIT 10";

        $resultat = $base->prepare($sql);
        $resultat->execute($params);

        echo "<table>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Cartons rouges </th>
                    </tr>";

            if ($resultat->rowCount() > 0) {
                while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                   <td>" . htmlspecialchars($ligne['prenom'] ?? '') . "</td>
                            <td>" . htmlspecialchars($ligne['nom']) . "</td>
                            <td>" . htmlspecialchars($ligne['nb_fautes']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucun résultat trouvé pour les critères sélectionnés.</td></tr>";
            }
            echo "</table>";
    }              
    else {
        echo "<div class='message'>Aucun critère valide sélectionné.</div>";
    }
} catch (Exception $e) {
    echo "<div class='message'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body>
</html>";
?>