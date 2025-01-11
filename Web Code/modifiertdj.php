<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_temps_de_jeu']) && !isset($_POST['update'])) {
        $id_temps_de_jeu = $_POST['id_temps_de_jeu'];
        $sql = 'SELECT * FROM temps_de_jeu WHERE id_temps_de_jeu = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_temps_de_jeu]);

        if ($but = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations du temps de jeu </h3>";
            echo "<form action='' method='post'>";
            echo "ID Joueur : <input type='number' name='id_joueur' value='" . htmlspecialchars($temps_de_jeu['id_joueur']) . "' required><br>";
            echo "ID Match : <input type='number' name='id_match' value='" . htmlspecialchars($temps_de_jeu['id_match']) . "' required><br>";
            echo "Temps : <input type='number' name='temps' value='" . htmlspecialchars($temps_de_jeu['temps']) . "'> <br>";
            echo "Numero : <input type='number' name='numero' value='" . htmlspecialchars($temps_de_jeu['numero']) . "'> <br>";
            echo "Poste : <input type='checkbox' name='poste' " . htmlspecialchars($temps_de_jeu['poste']. "'><br>";
            echo "<input type='hidden' name='id_temps_de_jeu' value='" . htmlspecialchars($temps_de_jeu['id_temps_de_jeu']) . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucun temps de jeu avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_but']) && isset($_POST['update'])) {
        $id_temps_de_jeu = $_POST['id_temps_de_jeu'];
        $id_joueur = $_POST['id_joueur'];
        $id_match = $_POST['id_match'];
        $temps = $_POST['temps'];
        $numero = $_POST['numero'];
        $poste = $_POST['poste'];

        $sql = 'UPDATE temps_de_jeu SET id_joueur = :id_joueur, id_match = :id_match, temps = :temps, numero = :numero, poste = :poste,
        WHERE id_temps_de jeu = :id';


        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_temps_de_jeu,
            'id_joueur' => $id_joueur,
            'id_match' => $id_match,
            'temps' => $temps,
            'numero' => $numero,
            'poste' => $poste,
        ]);

        echo "<h3>Les informations du temps de jeu ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>