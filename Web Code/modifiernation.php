<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_nation']) && !isset($_POST['update'])) {
        $id_nation = $_POST['id_nation'];
        $sql = 'SELECT * FROM nation WHERE id_nation = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_nation]);

        if ($nation = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations de la nation</h3>";
            echo "<form action='' method='post'>";
            echo "Nom : <input type='text' name='nom' value='" . htmlspecialchars($nation['nom']) . "' required><br>";
            echo "Confédération : <input type = 'text' name = 'nom' value = ' " . htmlspecialchars($nation['confederation'] ?? '')."'required<br>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucune nation avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_nation']) && isset($_POST['update'])) {
        $id_nation = $_POST['id_nation'];
        $nom = $_POST['nom'];
        $confderation = $_POST['confederation'];

        $sql = 'UPDATE nation SET nom = :nom, confederation = :confederation WHERE id_nation = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_nation,
            'nom' => $nom,
            'confederation' => $confederation,
        ]);

        echo "<h3>Les informations de la nation ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>