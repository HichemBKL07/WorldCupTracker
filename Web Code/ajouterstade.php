<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $nom = $_POST['nom'];
    $checksql = "SELECT COUNT(*) FROM stade WHERE nom = :nom";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':nom' => $nom,
    ]);
    $nb_ligne = $stmt->fetchColumn();
    if ($nb_ligne>0)
    {
        echo "Un stade similaire existe déjà. Votre ajout n'a pas eu lieu.";
    }
    else
    {
        $sql = "INSERT INTO stade(id_stade, nom) 
        VALUES (NULL,'$nom')";
        $resultat=$base->exec($sql);
        echo "Stade ajouté avec succès !";
    }
    
?>
