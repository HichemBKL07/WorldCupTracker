<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $nom = $_POST['nom'];
    $confederation = $_POST['confederation'];
    $checksql = "SELECT COUNT(*) FROM nation WHERE nom = :nom AND confederation = :confederation";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':nom' => $nom,
        ':confederation' => $confederation
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if ($nb_ligne>0)
    {
        echo "Une nation similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";

    }
    else
    {
        $sql = "INSERT INTO nation(id_nation, nom, confederation) 
        VALUES (NULL, '$nom', '$confederation')";
        $resultat=$base->execute($sql);
        echo "Nation ajoutée avec succès !";
    }
    
?>