<?php
    $supp=$_POST['idsupp'];

    if(isset($supp))
    {	
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données<br>";

        $sql = "DELETE FROM joueur WHERE id_joueur ='$supp'";
        $base->exec($sql);
        echo "<br> Le joueur ".$supp." est supprimé";
    }
    else
    {
        echo "<br> Cet ID de joueur est inexistant.";
    }
?>