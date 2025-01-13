<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

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
	$checksql = "SELECT COUNT(*) 
	FROM but WHERE id_match = :id_match 
	AND id_joueur = :id_joueur 
	AND timing = :timing 
	AND (id_passeur = :id_passeur OR id_passeur IS NULL)
	AND (penalty = :penalty OR penalty IS NULL)
    AND (csc = :csc OR csc IS NULL)";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur,
        ':timing' => $timing,
        ':id_passeur' => $id_passeur,
        ':penalty' => $penalty,
        ':csc' => $csc
    ]);
    $nb_ligne = $stmt->fetchColumn();
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
