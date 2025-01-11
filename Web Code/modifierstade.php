<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_stade']) && !isset($_POST['update'])) {
        $id_stade = $_POST['id_stade'];
        $sql = 'SELECT * FROM stade WHERE id_stade = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_stade]);

        if ($stade = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations du stade</h3>";
            echo "<form action='' method='post'>";
            echo "Nom : <input type='text' name='nom' value='" . htmlspecialchars($stade['nom']) . "' required><br>";
            echo "Ville : <input type='text' name='ville' value='" . htmlspecialchars($stade['ville']) . "' required><br>";
            echo "Capacité : <input type='number' name='capacite' value='" . htmlspecialchars($stade['capacite']) . "' required><br>";
            echo "<input type='hidden' name='id_stade' value='" . $stade['id_stade'] . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucun stade avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_stade']) && isset($_POST['update'])) {
        $id_stade = $_POST['id_stade'];
        $nom = $_POST['nom'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $capacite = $_POST['capacite'] ?? '';

        $sql = 'UPDATE stade SET nom = :nom, ville = :ville, capacite = :capacite WHERE id_stade = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_stade,
            'nom' => $nom,
            'ville' => $ville,
            'capacite' => $capacite,
        ]);

        echo "<h3>Les informations du stade ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>