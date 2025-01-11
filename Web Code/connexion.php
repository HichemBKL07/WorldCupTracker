<?php
    session_start();
    ob_start();
    try {
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
		 $email = $_POST['email'];
         $mdp = $_POST['mdp'];
         if (!empty($email) && !empty($mdp)){
                $sql = 'SELECT * FROM utilisateur WHERE email = :email';
                $stmt = $base -> prepare($sql);
                $stmt -> execute(['email'=> $email]);
                $utilisateur = $stmt -> fetch(PDO::FETCH_ASSOC);
                if ($utilisateur){
                    if ($mdp === $utilisateur['mdp']){
                    $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
                        if ($utilisateur['role']==='admin'){
                            $_SESSION['is_admin'] = true;
                            header('Location: admin_interface.php');

                        }
                        else{
                            if (isset($_SESSION['redirect_url'])) {
                                $redirect_url = $_SESSION['redirect_url'];
                                unset($_SESSION['redirect_url']);
                                header("Location: $redirect_url");
                            }
                            else {
                                header('Location: index.html');
                            }

                       }
                       exit();
                    }
                    else {
                        echo "Mot de passe incorrect.";
                    }
                }
                else {
                    echo "Email incorrect";
                }
            }
        else {
            echo "Veuillez remplir les champs.";
        }
      }
      catch(Exception $e) {
                die('Erreur : ' . $e->getMessage());
      }
	ob_end_flush();
   
?> 