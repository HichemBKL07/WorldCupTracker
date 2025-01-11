<?php
    $supp=$_POST['idsupp'];

    if(isset($supp))
    {	
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données<br>";

        $sql = "DELETE FROM competition WHERE id_competition='$supp'";
        $base->exec($sql);
        echo "<br> La compétition ".$supp." est supprimée";
    }
    else
    {
        echo "<br> Cet ID de compétition est inexistant.";
    }
?>