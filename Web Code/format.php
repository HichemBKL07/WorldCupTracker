<?php
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Connexion à la base de données</title>
    <style>
   
        body 
        {
            background-color: #F0F8FF;
            color: #2B2B2B;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        h1 
        {
            text-align: center;
            color: #000000;
        }


        h2
        {
                color: #0056B3; /* Sous-titres*/
                border-bottom: 2px solid #0056B3; /* Ligne sous les titres */
                padding-bottom: 5px;
        }
    
         form 
        {
                background-color: #E0E7FF; /* Fond des formulaires */
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
        }
    
        input[type='text'], input[type='number'] 
        {
                width: calc(100% - 22px); 
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #007BFF; /* Bord */
                border-radius: 5px;
                background-color: #FFFFFF; /* Fond des champs de texte */
                color: #2B2B2B; /* Texte des champs de texte */
        }
        .message 
        {
            background-color: #E0E7FF;
            color: #2B2B2B;
            border-radius: 8px;
            padding: 15px;
            margin: 20px auto;
            text-align: center;
            font-size: 1.1em;
            border: 1px solid #007BFF;
            width: 50%;
        }
    </style>
</head>
<body>";

try 
{
    $base = new PDO('mysql:host=fdb1030.awardspace.net;dbname=4544603_user', '4544603_user', 'BD0AA2323');
    echo "<h1>World Cup Tracker</h1>";
    echo "<div class='message'>Connexion à la base de données réussie</div>";
} 

catch (Exception $e) 
{
  
    echo "<h1>World Cup Tracker</h1>";
    echo "<div class='message'>Erreur : Connexion à la base de données impossible</div>";
}

echo "</body></html>";
?>
