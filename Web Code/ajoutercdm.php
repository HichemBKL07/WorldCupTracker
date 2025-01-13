<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $annee = $_POST['annee'];
    $pays_organisateur = $_POST['pays_organisateur'];
    $id_vainqueur = $_POST['id_vainqueur'];
    if (empty($id_vainqueur)) { 
        $id_vainqueur = 'NULL';
    }
    $checksql = "SELECT COUNT(*) FROM competition WHERE annee = :annee AND pays_organisateur = :pays_organisateur AND id_vainqueur = :id_vainqueur";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':annee' => $annee,
        ':pays_organisateur' => $pays_organisateur,
        ':id_vainqueur' => $id_vainqueur
    ]);
    $nb_ligne = $stmt->fetchColumn();
    if ($nb_ligne >0)
    {
        echo "Une competition similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";
    }
    else
    {
        $sql = "INSERT INTO competition(id_competition, annee, pays_organisateur, id_vainqueur) 
        VALUES (NULL, '$annee', '$pays_organisateur', '$id_vainqueur')";
        $resultat=$base->exec($sql);
        echo "Competition ajoutée avec succès !";
    }
    
?>
