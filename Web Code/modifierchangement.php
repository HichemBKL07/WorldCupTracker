<?php
    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données. <br>";

        if (isset($_POST ['id_changement']) && !isset($_POST['update']))
        {
            $id_changement = $_POST['id_changement'];
            $sql = 'SELECT * FROM changement where id_changement = :id';
            $stmt = $base->prepare($sql);
            $stmt->execute(['id'=>$id_changement]);

            if ($changement = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo "<h3> Modifier les informations du changement </h3>";
                echo "<form action = '' method = 'post' >";
                echo "ID Match : <input type = 'number' name = 'id_match' value = '" . htmlspecialchars($changement['id_match']) ."' required><br>";
                echo "ID Joueur entrant : <input type = 'number' name = 'id_joueur1' value = '" . htmlspecialchars($changement['id_joueur1'])."'required><br>";
                echo "ID Joueur sortant : <input type = 'number' name = 'id_joueur2' value = '" . htmlspecialchars($changement['id_joueur2'])."'required><br>";
                echo "Timing : <input type = 'number' name = 'timing' value = '" . htmlspecialchars($changement['timing'])."' required><br>";

                echo "<input type = 'hidden' name = 'id_changement' value = '" . $changement['id_changement']."'>";
                echo "<input type = 'submit' value = 'Mettre à jour'>";
                echo "</form>";
            }
            else
            {
                echo "<h3> Aucun changement avec cet ID </h3>";
            }
        }

        if (isset($_POST['id_changement']) && isset($_POST['update']))
        {
            $id_changement = $_POST['id_changement'];
            $id_joueur1 = $_POST['id_joueur1'];
            $id_joueur2 = $_POST['id_joueur2'];
            $timing = $_POST['timing'];

            $sql = 'UPDATE changement SET id_joueur1 = :id_joueur1, id_joueur2 = :id_joueur2, timing = :timing WHERE id_changement = :id';
            $stmt = $base->prepare($sql);
            $stmt->execute([
                'id' => $id_changement,
                'id_joueur1' => $id_joueur1,
                'id_joueur2' => $id_joueur2,
                'timing' => $timing,
            ]);

        }
    }

    catch (Exception $e){
        die('Erreur : ' .$e ->getMessage());
    }
?>