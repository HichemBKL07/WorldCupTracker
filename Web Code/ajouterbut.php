<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";
	// Hichem lis le mot secret = mortadon
    $id_match = $_POST['id_match'];
    $id_joueur = $_POST['id_joueur'];
    $timing = $_POST['timing'];
    $id_passeur = $_POST['id_passeur'];
    $penalty = $_POST['penalty'];
    $csc = $_POST['csc'];
	if (empty($id_passeur)) 
    { 
        $id_passeur = 'NULL' ;
    }
	if (empty ($penalty))
    {
            $penalty='NULL';
    }
	if (empty($csc))
    {
    		$csc=  'NULL';
    }
	$checksql = "SELECT COUNT(*) FROM but WHERE id_match = :id_match AND id_joueur = :id_joueur AND timing = :timing AND id_passeur = :id_passeur AND penalty = :penalty AND csc = :csc";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur,
        ':timing' => $timing,
        ':id_passeur' => $id_passeur,
        ':penalty' => $penalty,
        ':csc' => $csc
    ]);
    $ligne = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_ligne = count($ligne);
    if ($nb_ligne >0)
    {
            echo "Un but similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";
    }
    else
    {
        $sql = "INSERT INTO but(id_but, id_match, id_joueur, timing, id_passeur, penalty, csc) 
            VALUES (NULL, $id_match, $id_joueur, $timing, $id_passeur, $penalty, $csc)";
        $resultat=$base->exec($sql);
        echo "But ajouté avec succès !";
    }
    
?>
