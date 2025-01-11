<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_but']) && !isset($_POST['update'])) {
        $id_but = $_POST['id_but'];
        $sql = 'SELECT * FROM but WHERE id_but = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_but]);

        if ($but = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations du but</h3>";
            echo "<form action='' method='post'>";
            echo "ID Match : <input type='number' name='id_match' value='" . htmlspecialchars($but['id_match']) . "' required><br>";
            echo "ID Joueur : <input type='number' name='id_joueur' value='" . htmlspecialchars($but['id_joueur']) . "' required><br>";
            echo "Timing : <input type='number' name='timing' value='" . htmlspecialchars($but['timing']) . "' required><br>";
            echo "ID Passeur : <input type='number' name='id_passeur' value='" .($but['id_passeur']) . "'><br>";
            echo "Penalty : <input type='checkbox' name='penalty' " . ($but['penalty'] ? "checked" : "") . "><br>";
            echo "CSC : <input type='checkbox' name='csc' " . ($but['csc'] ? "checked" : "") . "><br>";
            echo "<input type='hidden' name='id_but' value='" . $but['id_but'] . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucun but avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_but']) && isset($_POST['update'])) {
        $id_but = $_POST['id_but'];
        $id_match = $_POST['id_match'] ?? '';
        $id_joueur = $_POST['id_joueur'] ?? '';
        $timing = $_POST['timing'] ?? '';
        $id_passeur = $_POST['id_passeur'] ?? '';
        $penalty = isset($_POST['penalty']) ? 1 : 0;
        $csc = isset($_POST['csc']) ? 1 : 0;

        $sql = 'UPDATE but SET id_match = :id_match, id_joueur = :id_joueur, timing = :timing, id_passeur = :id_passeur, penalty = :penalty, csc = :csc WHERE id_but = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_but,
            'id_match' => $id_match,
            'id_joueur' => $id_joueur,
            'timing' => $timing,
            'id_passeur' => $id_passeur,
            'penalty' => $penalty,
            'csc' => $csc,
        ]);

        echo "<h3>Les informations du but ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>