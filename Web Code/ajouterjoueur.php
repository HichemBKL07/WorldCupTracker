<?php
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie. <br>";

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $poste = $_POST['poste'];
    $id_nation = $_POST['id_nation'];
    $checksql = "SELECT COUNT(*) FROM joueur WHERE nom = :nom AND prenom = :prenom AND poste = :poste AND id_nation = :id_nation";
    $stmt = $base->prepare($checksql);
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':poste' => $poste,
        ':id_nation' => $id_nation
    ]);
    $nb_ligne = $stmt->fetchColumn();
    if ($nb_ligne>0)
    {
        echo "Un joueur similaire existe déjà dans la base de données. Votre ajout n'a pas eu lieu.";

    }
    else
    {
        $sql = "INSERT INTO joueur(id_joueur, nom, prenom, poste, id_nation) 
        VALUES (NULL, '$nom', '$prenom', '$poste', '$id_nation')";
        $resultat=$base->exec($sql);
        echo "Joueur ajouté avec succès !";
    }
    

    
?>
