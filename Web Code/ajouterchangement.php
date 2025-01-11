<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $id_match = $_POST['id_match'];
    $id_joueur1 = $_POST['id_joueur1'];
    $id_joueur2 = $_POST['id_joueur2'];
    $timing = $_POST['timing'];
    $checksql = "SELECT COUNT(*) FROM changement WHERE id_match = :id_match AND id_joueur1 = :id_joueur1 AND id_joueur2 = :id_joueur2";
    $stmt = $base ->prepare($checksql);
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur1' => $id_joueur1,
        ':id_joueur2' => $id_joueur2,
        ':timing' => $timing
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if (nb_ligne>0)
    {
        echo "Un changement similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";

    }
    else
    {
        $sql = "INSERT INTO changement(id_changement, id_match, id_joueur1, id_joueur2, timing) 
        VALUES (NULL, '$id_joueur', '$id_joueur1', '$id_joueur2', '$timing')";
        $resultat=$base->execute($sql);
        echo "Changement ajouté avec succès !";
    }

    
?>