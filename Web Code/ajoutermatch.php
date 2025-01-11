<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $tour = $_POST['tour'];
    $date_match = $_POST['date_match'];
    $heure = $_POST['heure'];
    $score_local = $_POST['score_local'];
    $score_visiteur = $_POST['score_visiteur'];
    $id_nation_local = $_POST['id_nation_local'];
    $id_nation_visiteur = $_POST['id_nation_visiteur'];
	$id_stade = $_POST['id_stade'];
    $affluence = $_POST['affluence'];
    $url_match = $_POST['url_match'];
    $id_competition = $_POST['id_competition'];
    $checksql = "SELECT COUNT(*) FROM `match` WHERE tour = :tour AND date_match = :date_match 
    AND heure = :heure AND score_local = :score_local AND score_visiteur = :score_visiteur 
    AND id_nation_local = :id_nation_local AND id_nation_visiteur AND id_stade = :id_stade 
    AND affluence = :affluence AND url_match = :url_match AND id_competition =:id_competition";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':tour' => $tour,
        ':date_match' => $date_match,
        ':heure' => $heure,
        ':score_local' => $score_local,
        ':score_visiteur' => $score_visiteur,
        ':id_nation_local' => $id_nation_local,
        ':id_nation_visiteur' => $id_nation_visiteur,
        ':id_stade' => $id_stade,
        ':affluence' => $affluence,
        ':url_match' => $url_match,
        ':id_competition' => $id_competition
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if (nb_ligne >0)
    {
        echo "Un joueur similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";
    }
    else 
    {
        $sql = "INSERT INTO `match`(id_match, tour, date_match, heure, score_local, score_visiteur,
        id_nation_local, id_nation_visiteur, id_stade, affluence, url_match, id_competition) 
        VALUES (NULL, '$tour', '$date_match', '$heure', '$score_local', '$score_visiteur', '$id_nation_local', 
        '$id_nation_visiteur', '$id_stade', '$affluence', '$url_match', '$id_competition')";
        $resultat=$base->exec($sql);
        echo "Match ajouté avec succès !";
    }
    
?>