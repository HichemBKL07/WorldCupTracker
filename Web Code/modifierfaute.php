<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_faute']) && !isset($_POST['update'])) {
        $id_faute = $_POST['id_faute'];
        $sql = 'SELECT * FROM faute WHERE id_faute = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_faute]);

        if ($faute = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations de la faute</h3>";
            echo "<form action='' method='post'>";
            echo "ID Match : <input type='number' name='id_match' value='" . htmlspecialchars($faute['id_match']) . "' required><br>";
            echo "ID Joueur : <input type='number' name='id_joueur' value='" . htmlspecialchars($faute['id_joueur']) . "' required><br>";
            echo "Minute : <input type='number' name='minute' value='" . htmlspecialchars($faute['minute']) . "' required><br>";
            echo "Type de faute : <input type='text' name='type_faute' value='" . htmlspecialchars($faute['type_faute']) . "' required><br>";
            echo "<input type='hidden' name='id_faute' value='" . $faute['id_faute'] . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucune faute avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_faute']) && isset($_POST['update'])) {
        $id_faute = $_POST['id_faute'];
        $id_match = $_POST['id_match'] ?? '';
        $id_joueur = $_POST['id_joueur'] ?? '';
        $minute = $_POST['minute'] ?? '';
        $type_faute = $_POST['type_faute'] ?? '';

        $sql = 'UPDATE faute SET id_match = :id_match, id_joueur = :id_joueur, `minute` = `:minute`, type_faute = :type_faute WHERE id_faute = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_faute,
            'id_match' => $id_match,
            'id_joueur' => $id_joueur,
            'minute' => $minute,
            'type_faute' => $type_faute,
        ]);

        echo "<h3>Les informations de la faute ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>