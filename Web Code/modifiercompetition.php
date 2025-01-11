<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_competition']) && !isset($_POST['update'])) {
        $id_competition = $_POST['id_competition'];
        $sql = 'SELECT * FROM competition WHERE id_competition = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_competition]);

        if ($competition = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations de la compétition</h3>";
            echo "<form action='' method='post'>";
            echo "Année : <input type='number' name='annee' value='" . ($competition['annee']) . "' required><br>";
            echo "Pays organisateur : <input type='text' name='pays_organisateur' value='" . ($competition['pays_organisateur']) . "' required><br>";
            echo "ID du pays vainqueur : <input type = 'text' name = 'id_vainqueur' value = '".$competition['id_vainqueur']."'required><br>";
            echo "<input type='hidden' name='id_competition' value='" . $competition['id_competition'] . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucune compétition avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_competition']) && isset($_POST['update'])) {
        $id_competition = $_POST['id_competition'];
        $annee = $_POST['annee'] ?? '';
        $pays_organisateur = $_POST['pays_organisateur'] ?? '';
		$id_vainqueur = $_POST['id_vainqueur'];
        $sql = 'UPDATE competition SET annee = :annee, pays_organisateur = :pays_organisateur, id_vainqueur = :id_vainqueur WHERE id_competition = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_competition,
            'annee' => $annee,
            'pays_organisateur' => $pays_organisateur,
            'id_vainqueur' => $id_vainqueur,
        ]);

        echo "<h3>Les informations de la compétition ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
