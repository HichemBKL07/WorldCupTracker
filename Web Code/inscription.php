<?php
    $nom = $_POST['nom'] ??'';
    $prenom = $_POST['prenom']??'';
    $email = $_POST['email'] ??'';
    $mdp = $_POST['mdp'] ??'';
    $role = $_POST['role'] ?? 'utilisateur';
    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base <br>";
    
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        $stmt = $base->prepare($sql);
        $stmt->execute(['email' => $email]);
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            echo "Un compte avec cet email existe déjà.";
        } else {
            $sql = "INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, mdp, role) 
                    VALUES (NULL, :nom, :prenom, :email, :mdp, :role)";
            $stmt = $base->prepare($sql);
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mdp' => $mdp,
                'role' => $role,
            ]);
    
            echo "Inscription réussie !";
        }
    } catch(Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
?>