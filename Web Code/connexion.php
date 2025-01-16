<?php
session_start();
ob_start();

try {
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    if (!empty($email) && !empty($mdp)) {
        
        $sql = 'SELECT * FROM utilisateur WHERE email = :email';
        $stmt = $base->prepare($sql);
        $stmt->execute(['email' => $email]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur) {
            $hashedPassword = $utilisateur['mdp'];

            // Vérifier avec password_verify
            if (password_verify($mdp, $hashedPassword)) {
                // Mot de passe correct (déjà haché)
                $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
            } elseif ($mdp === $hashedPassword) {
                // Ancien mot de passe non haché : vérifier en clair
                // Mettre à jour avec un mot de passe haché
                $newHashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

                $updateSql = "UPDATE utilisateur SET mdp = :mdp WHERE id_utilisateur = :id";
                $updateStmt = $base->prepare($updateSql);
                $updateStmt->execute([
                    'mdp' => $newHashedPassword,
                    'id' => $utilisateur['id_utilisateur'],
                ]);

                $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
            } else {
                echo "Mot de passe incorrect.";
                exit();
            }

            
            if ($utilisateur['role'] === 'admin') {
                $_SESSION['is_admin'] = true;
                header('Location: admin_interface.php');
            } else {
                if (isset($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    header("Location: $redirect_url");
                } else {
                    header('Location: index.html');
                }
            }
            exit();
        } else {
            echo "Email incorrect.";
        }
    } else {
        echo "Veuillez remplir les champs.";
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

ob_end_flush();
?>
