<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $id_match = $_POST['id_match'];
    $id_joueur = $_POST['id_joueur'];
    $minute = $_POST['minute'];
    $type_faute = $_POST['type_faute'];
    $checksql = "SELECT COUNT(*) FROM faute WHERE id_match = :id_match AND id_joueur = :id_joueur AND `minute` = `:minute` AND type_faute = :type_faute";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur,
        ':minute' => $minute,
        ':type_faute' => $type_faute
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if (nb_ligne>0)
    {
        echo "Une faute similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";
    }
    else 
    {
        $sql = "INSERT INTO but(id_faute, id_match, id_joueur, `minute`, typefaute) 
        VALUES (NULL, '$id_joueur', '$id_joueur1', '$id_joueur2', '$minute', '$typefaute')";
        $resultat=$base->execute($sql);
        echo "Faute ajoutée avec succès !";
    }    
?>