<?php
    $supp=$_POST['idsupp'];

    if(isset($supp))
    {	
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données<br>";

        $sql = "DELETE FROM `match` WHERE id_match='$supp'";
        $base->exec($sql);
        echo "<br> Le match ".$supp." est supprimé";
    }
    else
    {
        echo "<br> Cet ID de match est inexistant.";
    }
?>