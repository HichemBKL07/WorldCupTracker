<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $id_joueur = $_POST['id_joueur'];
    $id_match = $_POST['id_match'];
    $temps = $_POST['temps'];
    $numero = $_POST['numero'];
    $poste = $_POST['poste'];
    $checksql = "SELECT COUNT(*) FROM temps_de_jeu WHERE id_joueur = :id_joueur 
	AND id_match = :id_match 
	AND temps_de_jeu = :temps
	AND numero = :numero 
	AND poste = :poste";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':id_joueur' => $id_joueur,
        ':id_match' => $id_match,
        ':temps' => $temps,
        ':numero' => $numero,
        ':poste' => $poste
    ]);
    $nb_ligne = $stmt->fetchColumn();
    if ($nb_ligne > 0)
    {
        echo "Un temps de jeu similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";
    }   
    else 
    {
        $sql = "INSERT INTO temps_de_jeu(id_temps_de_jeu, id_joueur, id_match, temps_de_jeu, numero, poste) 
        VALUES (NULL, '$id_joueur', '$id_match', '$temps', '$numero', '$poste')";
        $resultat=$base->exec($sql); 
        echo "Temps de jeu ajouté avec succès !";
     }
    
?>
