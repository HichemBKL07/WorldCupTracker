<?php
try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "Connexion réussie à la base de données. <br>";

    if (isset($_POST['id_match']) && !isset($_POST['update'])) {
        $id_match = $_POST['id_match'];
        $sql = 'SELECT * FROM `match` WHERE id_match = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute(['id' => $id_match]);

        if ($match = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h3>Modifier les informations du match</h3>";
            echo "<form action='' method='post'>";
            echo "Date : <input type='date' name='date_match' value='" . htmlspecialchars($match['date_match']) . "' required><br>";
            echo "Heure : <input type='time' name='heure' value='" . htmlspecialchars($match['heure']) . "' required><br>";
            echo "Score local : <input type='number' name='score_local' value='" . htmlspecialchars($match['score_local']) . "' required><br>";
            echo "Score visiteur : <input type='number' name='score_visiteur' value='" . htmlspecialchars($match['score_visiteur']) . "' required><br>";
            echo "ID Nation local : <input type='number' name='id_nation_local' value='" . htmlspecialchars($match['id_nation_local']) . "' required><br>";
            echo "ID Nation visiteur : <input type='number' name='id_nation_visiteur' value='" . htmlspecialchars($match['id_nation_visiteur']) . "' required><br>";
            echo "ID Stade : <input type='number' name='id_stade' value='" . htmlspecialchars($match['id_stade']) . "' required><br>";
            echo "Affluence : <input type='number' name='affluence' value='" . htmlspecialchars($match['affluence']) . "'required><br>";
            echo "URL Match : <input type='url' name='url_match' value='" . htmlspecialchars($match['url_match']) . "'required><br>";
            echo "ID Compétition : <input type='number' name='id_competition' value='" . htmlspecialchars($match['id_competition']) . "' required><br>";
            echo "<input type='hidden' name='id_match' value='" . $match['id_match'] . "'>";
            echo "<input type='hidden' name='update' value='1'>";
            echo "<input type='submit' value='Mettre à jour'>";
            echo "</form>";
        } else {
            echo "<h3>Aucun match avec cet ID</h3>";
        }
    }

    if (isset($_POST['id_match']) && isset($_POST['update'])) {
        $id_match = $_POST['id_match'];
        $date_match = $_POST['date_match'] ?? '';
        $heure = $_POST['heure'] ?? '';
        $score_local = $_POST['score_local'] ?? '';
        $score_visiteur = $_POST['score_visiteur'] ?? '';
        $id_nation_local = $_POST['id_nation_local'] ?? '';
        $id_nation_visiteur = $_POST['id_nation_visiteur'] ?? '';
        $id_stade = $_POST['id_stade'] ?? '';
        $affluence = $_POST['affluence'] ?? '';
        $url_match = $_POST['url_match'] ?? '';
        $id_competition = $_POST['id_competition'] ?? '';

        $sql = 'UPDATE `match` SET date_match = :date_match, heure = :heure, score_local = :score_local, score_visiteur = :score_visiteur, id_nation_local = :id_nation_local, id_nation_visiteur = :id_nation_visiteur, id_stade = :id_stade, affluence = :affluence, url_match = :url_match, id_competition = :id_competition WHERE id_match = :id';
        $stmt = $base->prepare($sql);
        $stmt->execute([
            'id' => $id_match,
            'date_match' => $date_match,
            'heure' => $heure,
            'score_local' => $score_local,
            'score_visiteur' => $score_visiteur,
            'id_nation_local' => $id_nation_local,
            'id_nation_visiteur' => $id_nation_visiteur,
            'id_stade' => $id_stade,
            'affluence' => $affluence,
            'url_match' => $url_match,
            'id_competition' => $id_competition,
        ]);

        echo "<h3>Les informations du match ont été mises à jour avec succès !</h3>";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>