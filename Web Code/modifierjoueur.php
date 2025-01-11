<?php

    try
    {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données. <br>";
        
        if (isset($_POST['id_joueur']) && !isset($_POST['update']))
        {
            $id_joueur = $_POST['id_joueur'];
            $sql = 'SELECT * FROM joueur WHERE id_joueur = :id';
            $stmt = $base->prepare($sql);
            $stmt->execute(['id'=>$id_joueur]);

            if ($joueur = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo "<h3>Modifier les informations du joueur</h3>";
                echo "<form action='' method='post'>";
                echo "Nom : <input type='text' name='nom' value='" . $joueur['nom'] . "' required><br>";
                echo "Prénom : <input type='text' name='prenom' value='" . htmlspecialchars($joueur['prenom']) . "' required><br>";
                echo "Poste : <input type='text' name='poste' value='" . htmlspecialchars($joueur['poste']) . "' required><br>";
                echo "ID Nation : <input type='number' name='id_nation' value='" . htmlspecialchars($joueur['id_nation'] ?? '') . "' required><br>";
                echo "<input type='hidden' name='id_joueur' value='" . $joueur['id_joueur'] . "'>";
                echo "<input type='hidden' name='update' value='1'>";
                echo "<input type='submit' value='Mettre à jour'>";
                echo "</form>";
            }
            else
            {
                echo "<h3> Aucun joueur avec cet ID </h3>";
            }
        }
        if (isset($_POST['id_joueur']) && isset($_POST['update']))
        {
            $id_joueur = $_POST['id_joueur'];
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $poste = $_POST['poste'] ??'';
            $id_nation = $_POST['id_nation'] ??'';

            $sql = 'UPDATE joueur SET nom = :nom, prenom = :prenom,
            poste = :poste, id_nation = :id_nation WHERE id_joueur = :id';
            $stmt = $base->prepare($sql);
            $stmt->execute([
                'id' => $id_joueur,
                'nom' => $nom,
                'prenom' => $prenom,
                'poste' => $poste,
                'id_nation' => $id_nation,    
            ]);

            echo "<h3>Les informations du joueur ont été mises à jour avec succès !</h3>";
        }
    }
    catch (Exception $e) {
        // Message en cas d'erreur
        die('Erreur : ' . $e->getMessage());
    }
?>