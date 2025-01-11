<?php

session_start();

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Détails du Match</title>
    <link rel='stylesheet' href='styles.css'>


</head>
<body>";

if (isset($_GET['id_match']) && !empty($_GET['id_match'])) {
    $id_match = $_GET['id_match'];

    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmtMatch = $base->prepare("
            SELECT 
                m.score_local, m.score_visiteur,
                pays1.nom AS nom_nation_locale, pays2.nom AS nom_nation_visiteur,
                pays1.id_nation AS id_nation_local, pays2.id_nation AS id_nation_visiteur,
                stade.nom AS nom_stade, m.date_match, m.heure, m.affluence
            FROM `match` m
            JOIN nation pays1 ON m.id_nation_local = pays1.id_nation
            JOIN nation pays2 ON m.id_nation_visiteur = pays2.id_nation
            JOIN stade ON m.id_stade = stade.id_stade
            WHERE m.id_match = :id_match
        ");
        
        $stmtButs = $base->prepare("
            SELECT 
                'goal' AS type_event,
                but.timing,
                but.penalty,
                joueur.nom AS nom_joueur,
                joueur.id_nation AS joueur_nation,
                passeur.nom AS nom_passeur,
                but.csc
            FROM but 
            JOIN joueur ON but.id_joueur = joueur.id_joueur
            LEFT JOIN joueur passeur ON but.id_passeur = passeur.id_joueur
            WHERE but.id_match = :id_match
        ");

        $stmtFautes = $base->prepare("
            SELECT 
                'faute' AS type_event,
                f.minute AS timing,
                joueur.nom AS nom_joueur,
                joueur.id_nation AS joueur_nation,
                f.type_faute
            FROM faute f
            JOIN joueur ON f.id_joueur = joueur.id_joueur
            WHERE f.id_match = :id_match
        ");

        $stmtChangements = $base->prepare("
            SELECT 
                'changement' AS type_event,
                c.timing,
                joueur1.nom AS nom_joueur_sortant,
                joueur2.nom AS nom_joueur_entrant,
                joueur1.id_nation AS joueur_nation
            FROM changement c
            JOIN joueur joueur1 ON c.id_joueur1 = joueur1.id_joueur
            JOIN joueur joueur2 ON c.id_joueur2 = joueur2.id_joueur
            WHERE c.id_match = :id_match
        ");

        $stmtCompo = $base->prepare("
            SELECT 
                temps_de_jeu.temps_de_jeu as temps,
                temps_de_jeu.id_joueur,
                temps_de_jeu.numero,
                temps_de_jeu.poste,
                joueur.nom,
                joueur.id_nation
            FROM temps_de_jeu
            LEFT JOIN `match` ON temps_de_jeu.id_match = `match`.id_match
            LEFT JOIN joueur ON temps_de_jeu.id_joueur = joueur.id_joueur
            WHERE temps_de_jeu.id_match = :id_match
        ");

        $stmtMatch->execute(['id_match' => $id_match]);
        $stmtButs->execute(['id_match' => $id_match]);
        $stmtFautes->execute(['id_match' => $id_match]);
        $stmtChangements->execute(['id_match' => $id_match]);
        $stmtCompo->execute(['id_match' => $id_match]);

        $matchInfo = $stmtMatch->fetch(PDO::FETCH_ASSOC);
        $events = array_merge($stmtButs->fetchAll(PDO::FETCH_ASSOC),$stmtFautes->fetchAll(PDO::FETCH_ASSOC), $stmtChangements->fetchAll(PDO::FETCH_ASSOC));
        $compositions = $stmtCompo->fetchAll(PDO::FETCH_ASSOC);

        usort($events, function($a, $b) {
            return $a['timing'] - $b['timing'];
        }); 

        echo "<div class='score-board'>
                <strong>{$matchInfo['nom_nation_locale']}</strong> 
                {$matchInfo['score_local']} - {$matchInfo['score_visiteur']} 
                <strong>{$matchInfo['nom_nation_visiteur']}</strong>
              </div>";

        echo "<h2>Déroulement du match</h2>
              <div class='team-column'>
                <div class='timeline-container'>";

                foreach ($events as $event) {
                    $class = ($event['joueur_nation'] == $matchInfo['id_nation_local']) ? 'local' : 'visiteur';
                
                    if (isset($event['csc']) && $event['csc']) {
                        $class = ($class == 'local') ? 'visiteur' : 'local';
                    }
                
                    $event_text = '';
                    $image_path = '';
                
                    switch ($event['type_event']) {
                        case 'goal':
                            $image_path = (isset($event['penalty']) && $event['penalty']) ? 'penalty.png' : 'goal.png'; 
                            $event_text = "<strong>  &nbsp {$event['nom_joueur']}</strong>" . 
                                          (isset($event['csc']) && $event['csc'] ? "<sub> &nbsp &nbsp csc</sub>" : "") . 
                                          (isset($event['nom_passeur']) && $event['nom_passeur'] ? "<sub> &nbsp &nbsp passeur: {$event['nom_passeur']}</sub>" : "");
                            break;
                        case 'faute':
                            $event_text = "<strong> &nbsp {$event['nom_joueur']}</strong>";
                            $image_path = ($event['type_faute'] == 'yellow_card') ? 'jaune.png' : 'rouge.png'; 
                            break;
                            case 'changement':
                                $image_path = 'changement.png';
                                $event_text = "<strong>{$event['nom_joueur_sortant']} &nbsp </strong> <img src='{$image_path}' alt='{$event['type_event']}' style='width: 20px; height: 20px; vertical-align: middle;'> <strong> &nbsp {$event['nom_joueur_entrant']}</strong>";
                                    
                            break;
                        default:
                            break;
                    }
                
                    echo "<div class='event-row'>
                            <div class='event " . ($class === 'local' ? 'local' : 'empty-event') . "'>
                                " . ($class === 'local' && $event['type_event'] !== 'changement' ? "<img src='{$image_path}' alt='{$event['type_event']}' style='width: 20px; height: 20px;'> " . $event_text : ($class === 'local' ? $event_text : '')) . "
                            </div>
                            <div class='timing'><span>{$event['timing']}'</span></div>
                            <div class='event " . ($class === 'visiteur' ? 'visiteur' : 'empty-event') . "'>
                                " . ($class === 'visiteur' && $event['type_event'] !== 'changement' ? "<img src='{$image_path}' alt='{$event['type_event']}' style='width: 20px; height: 20px;'> " . $event_text : ($class === 'visiteur' ? $event_text : '')) . "
                            </div>
                        </div>";
                }
                

        echo "</div></div>";

        echo "<h2>Informations supplémentaires</h2>";
        echo "<div class='team-column'>";
        echo "<div>Stade : " . htmlspecialchars($matchInfo['nom_stade']) . "</div>";
        $date = new DateTime($matchInfo['date_match']);
        echo "<div>Date : " . $date->format('d/m/Y') . "</div>";
        echo "<div>Heure : " . $matchInfo['heure'] . "</div>";
        echo "<div>Affluence : " . htmlspecialchars($matchInfo['affluence']) . " supporters</div>";
        echo "</div>";

        $equipe_locale = array_filter($compositions, function($joueur) use ($matchInfo) {
            return $joueur['id_nation'] == $matchInfo['id_nation_local'];
        });
        $equipe_visiteur = array_filter($compositions, function($joueur) use ($matchInfo) {
            return $joueur['id_nation'] == $matchInfo['id_nation_visiteur'];
        });

        echo "<h2>Compositions des équipes</h2>";
        echo "<div class='composition-container'>";

        function afficherTableauEquipe($equipe, $nom_equipe) {
            echo "<div style='flex: 1;'>
                    <div class='team-column'>
                        <h3 style='text-align: center; margin-bottom: 15px;'>{$nom_equipe}</h3>
                        <table class='composition-table'>
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Joueur</th>
                                    <th>Poste</th>
                                    <th>Temps de jeu</th>
                                </tr>
                            </thead>
                            <tbody>";
            
                            foreach ($equipe as $joueur) {
                                echo "<tr>
                                        <td style='text-align: center;'>{$joueur['numero']}</td>
                                        <td style='text-align: center;'>
                                            <a class='joueur-lien' href='info_joueur.php?id_joueur=" . urlencode($joueur['id_joueur']) . "'>{$joueur['nom']}</a>
                                        </td>
                                        <td style='text-align: center;'>{$joueur['poste']}</td>
                                        <td style='text-align: center;'>{$joueur['temps']} min</td>
                                      </tr>";
                            }
                            
            
            echo "</tbody></table></div></div>";
        }

        afficherTableauEquipe($equipe_locale, $matchInfo['nom_nation_locale']);
        afficherTableauEquipe($equipe_visiteur, $matchInfo['nom_nation_visiteur']);

        echo "</div>";


        echo "<h2>Avis</h2>";
        echo "<div class='team-column'>";
       
        if (isset($_SESSION['id_utilisateur'])) 
        {
            echo "<form action='' method='POST'>
                <label for='note'>Notez le match (0 à 10) :</label><br>
                <input type='number' id='note' name='note' min='0' max='10' step='0.1' required>
          		 <button type='submit'>Envoyer</button>
              </form>";
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
                $note = floatval($_POST['note']);
                if ($note >= 0 && $note <= 10) {
                    try {
                        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
                        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                        $stmtCheck = $base->prepare("SELECT id_note FROM note WHERE id_utilisateur = :id_utilisateur AND id_match = :id_match");
                        $stmtCheck->execute(['id_utilisateur' => $_SESSION['id_utilisateur'], 'id_match' => $id_match]);
                        $existingNote = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
                        if ($existingNote) {
                            $stmtUpdate = $base->prepare("UPDATE note SET note = :note WHERE id_utilisateur = :id_utilisateur AND id_match = :id_match");
                            $stmtUpdate->execute(['note' => $note, 'id_utilisateur' => $_SESSION['id_utilisateur'], 'id_match' => $id_match]);
                        } else {
                            $stmtInsert = $base->prepare("INSERT INTO note (id_utilisateur, id_match, note) VALUES (:id_utilisateur, :id_match, :note)");
                            $stmtInsert->execute(['id_utilisateur' => $_SESSION['id_utilisateur'], 'id_match' => $id_match, 'note' => $note]);
                        }
    
                        echo "<div class='message'>Votre note a été enregistrée avec succès !</div>";
    
                    } catch (Exception $e) {
                        echo "<div class='message'>Erreur : " . $e->getMessage() . "</div>";
                    }
                } else {
                    echo "<div class='message'>La note doit être entre 0 et 10 !</div>";
                }
            }
        }
        else {
            $_SESSION['redirect_url']=$_SERVER['REQUEST_URI'];
            echo "<div class='message'> Vous devez être connecté pour noter un match.</div class>";
            echo "<div class='button-container'>
            <button onclick='window.location.href=\"connexion_inscription.php\"'>Se connecter/S'inscrire</button>
            </div>";
        }

        try {
            $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
            $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $stmtAvg = $base->prepare("SELECT AVG(note) AS moyenne FROM note WHERE id_match = :id_match");
            $stmtAvg->execute(['id_match' => $id_match]);
            $avgNote = $stmtAvg->fetch(PDO::FETCH_ASSOC);
        
            if ($avgNote['moyenne'] !== null) {
                echo "<div class='message'>Note moyenne du match : " . round($avgNote['moyenne'], 1) . "/10</div>";
            } else {
                echo "<div class='message'>Aucune note n'a été donnée pour ce match.</div>";
            }
        } catch (Exception $e) {
            echo "<div class='message'>Erreur lors de la récupération de la note moyenne : " . $e->getMessage() . "</div>";
        }

        echo "</div>";

    } catch (Exception $e) {
        echo "<h1>World Cup Tracker</h1>
              <div class='message'>Erreur : " . $e->getMessage() . "</div>";
    }
}

echo "</body></html>";
?>