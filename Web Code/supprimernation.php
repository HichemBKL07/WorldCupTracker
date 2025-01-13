<?php
    $supp=$_POST['idsupp'];

    if(isset($supp))
    {	
        $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
        echo "Connexion réussie à la base de données<br>";

        $sql = "DELETE FROM nation WHERE id_nation ='$supp'";
        $base->exec($sql);
        echo "<br> La nation ".$supp." est supprimée";
    }
    else
    {
        echo "<br> Cet ID de nation est inexistant.";
    }
?>
