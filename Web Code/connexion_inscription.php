<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion / Inscription</title>
        <link rel='stylesheet' href='styles.css'>
    
    <style>
       
        input[type="text"], input[type="email"], input[type="password"] { width: calc(100% - 22px); padding: 10px; margin: 10px 0; border: 1px solid #007BFF; border-radius: 5px; }
        input[type="submit"] { background-color: #0056B3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        h2 { color: #0056B3; }
    </style>
</head>
<body>

<h2>Connexion</h2>
<form action="connexion.php" method="post">
    <label for="email">Email :</label>
    <input type="text" id="email" name="email" required>
    <label for="mdp">Mot de passe :</label>
    <input type="password" id="mdp" name="mdp" required>
    <input type="submit" value="Se connecter">
</form>

<h2>Inscription</h2>
<form action="inscription.php" method="post">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>
    <label for="prenom">Prénom :</label>
    <input type="text" name="prenom" id="prenom" required>
    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required>
    <label for="mdp">Mot de passe :</label>
    <input type="password" name="mdp" id="mdp" required>
    <input type="hidden" name="role" value="utilisateur">
    <input type="submit" value="S'inscrire">
</form>

</body>
</html>