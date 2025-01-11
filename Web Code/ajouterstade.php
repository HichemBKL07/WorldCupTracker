<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $nom = $_POST['nom'];
    $ville = $_POST['ville'];
    $capacite = $_POST['capacite'];
    $checksql = "SELECT COUNT(*) FROM stade WHERE nom = :nom AND ville = :ville AND capacite = :capacite";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':nom' => $nom,
        ':confederation' => $confederation
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if ($nb_ligne>0)
    {
        echo "Un stade similaire existe déjà. Votre ajout n'a pas eu lieu.";
    }
    else
    {
        $sql = "INSERT INTO stade(nom, ville, capacite) 
        VALUES (NULL, '$nom', '$ville', '$capacite')";
        $resultat=$base->execute($sql);
        echo "Stade ajouté avec succès !";
    }
    
?>