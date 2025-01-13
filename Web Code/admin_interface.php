<?php
    session_start();

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) 
    {
        header("Location: index.html");
        exit();
    }
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <link rel='stylesheet' href='styles.css'>
        <style> 

        body { 

            font-family: Arial, sans-serif; 

            line-height: 1.6; 

            margin: 0; 

            padding: 0; 

            background-color: #f8f9fa; 

        } 

        header { 

            background: #343a40; 

            color: #fff; 

            padding: 10px 20px; 

            text-align: center; 

        } 

        main { 

            padding: 20px; 

        } 

        section { 

            margin-bottom: 40px; 

        } 

        .table-container { 

            display: flex; 

            align-items: flex-start; 

            justify-content: space-between; 

            border: 1px solid #ddd; 

            padding: 10px; 

            background-color: rgba(224, 231, 255, 0.1); 

            margin-bottom: 20px; 

            border-radius: 5px; 

            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 

        } 

        form { 

            display: flex; 

            flex-direction: column; 

            width: 30%; 

        } 

        form label, form input,  { 

            margin-bottom: 20px; 

        } 

        form input { 

            padding: 5px; 

            font-size: 10px; 

        } 

  

        h1, h2 { 

            text-align: center; 

        } 

        h2 { 

            margin-bottom: 20px; 

        } 

        .action-title { 

            font-weight: bold; 

            text-align: center; 

            margin-bottom: 15px; 

        }

        
       </style>
   
    



    
    
</head>
<body>
    <header>
        <h1>Interface Admin</h1>
    </header>
    <main>
        <section>
            <h2>Gestion de la table competition </h2>
            <div class="table-container">
                <form action="ajoutercdm.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>Année :</label>
                    <input type="text" name="annee" required>
                    <label>Pays Organisateur :</label>
                    <input type="text" name="pays_organisateur">
                    <label>ID Vainqueur :</label>
                    <input type="number" name="id_vainqueur">
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifiercompetition.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID de la compétition :</label>
                    <input type="number" name="id_competition" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimercdm.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID de la compétition :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>

        <section>
            <h2>Gestion de la table joueur </h2>
            <div class="table-container">
                <form action="ajouterjoueur.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>Nom :</label>
                    <input type="text" name="nom" required>
                    <label>Prénom :</label>
                    <input type="text" name="prenom">
                    <label>Poste :</label>
                    <input type="text" name="poste">
                    <label>ID de la Nation :</label>
                    <input type="number" name="id_nation" required>
                    <button>Ajouter</button>
                </form>

                <form action="modifierjoueur.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du joueur :</label>
                    <input type="number" name="id_joueur" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimerjoueur.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du joueur :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>

        <section>
            <h2>Gestion de la table nation</h2>
            <div class="table-container">
                <form action="ajouternation.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>Nom :</label>
                    <input type="text" name="nom">
                    <label>Confédération :</label>
                    <input type="text" name="confederation">
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifiernation.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID de la nation :</label>
                    <input type="number" name="id_nation" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimernation.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID de la nation :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>
        
        <section>
            <h2>Gestion de la table match</h2>
            <div class="table-container">
                <form action="ajoutermatch.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>Tour :</label>
                    <input type="text" name="tour" required>
                    <label>Date :</label>
                    <input type="date" name="date_match" required>
                    <label>Heure :</label>
                    <input type="time" name="heure" required>
                    <label>Équipe 1 :</label>
                    <input type="int" name="id_nation_local" required>
                    <label>Équipe 2 :</label>
                    <input type="int" name="id_nation_visiteur" required>
                    <label>Score Équipe 1 :</label>
                    <input type="int" name="score_local" required>  
                    <label>Score Équipe 2 :</label>
                    <input type="int" name="score_visiteur" required>    
                    <label>ID Stade :</label>
                    <input type="int" name="id_stade" >
                    <label>Affluence :</label>
                    <input type="int" name="affluence" >
                    <label>URL match :</label>
                    <input type="text" name="url_match" >
                    <label>ID compétition :</label>
                    <input type="int" name="id_competition" >    
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifiermatch.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du match :</label>
                    <input type="number" name="id_match" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimermatch.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du match :</label>
                    <input type="number" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>    

        <section>
            <h2>Gestion de la table but</h2>
            <div class="table-container">
                <form action="ajouterbut.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>ID du match :</label>
                    <input type="int" name="id_match" required>
                    <label>ID du joueur :</label>
                    <input type="int" name="id_joueur" required>
                    <label>Minute :</label>
                    <input type="number" name="timing" required>
                    <label>ID du passeur :</label>
                    <input type="int" name="id_passeur">
                    <label>Penalty (si oui entrez 1, sinon laissez vide) : </label>
                    <input type ="int" name="penalty">
                    <label>CSC (si oui entrez 1, sinon laissez vide) : </label>
                    <input type ="int" name="csc">    
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifierbut.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du but :</label>
                    <input type="number" name="id_but" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimerbut.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du but :</label>
                    <input type="number" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>
        
        <section>
            <h2>Gestion de la table faute</h2>
            <div class="table-container">
                <form action="ajouterfaute.php" method="POST">
                    <p class ="action-title">Ajouter</p>
                    <label>ID du match :</label>
                    <input type="int" name="id_match" required>
                    <label>ID du joueur :</label>
                    <input type="int" name="id_joueur" required>
                    <label>Minute :</label>
                    <input type="int" name="minute">
                    <label>Type :</label>
                    <input type="text" name="type_faute">
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifierfaute.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID de la faute :</label>
                    <input type="number" name="id_faute" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimerfaute.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID de la faute :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>

        <section>
            <h2>Gestion de la table changement </h2>
            <div class="table-container">
                <form action="ajouterchangement.php" method="POST">
                    <p class ="action-title">Ajouter</p>
                    <label>ID du match :</label>
                    <input type="int" name="id_match" required>
                    <label>Joueur entrant :</label>
                    <input type="int" name="id_joueur1" required>
                    <label>Joueur sortant :</label>
                    <input type="int" name="id_joueur2" required>
                    <label>Minute :</label>
                    <input type="int" name="timing">
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifierchangement.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du changement :</label>
                    <input type="number" name="id_changement" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimerchangement.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du changement :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>

        <section>
            <h2>Gestion de la table temps de jeu</h2>
            <div class="table-container">
                <form action="ajoutertdj.php" method="POST">
                    <p class ="action-title">Ajouter</p>
                    <label>ID joueur :</label>
                    <input type="int" name="id_joueur" required>
                    <label>ID match :</label>
                    <input type="int" name="id_match" required>
                    <label>Temps :</label>
                    <input type="int" name="temps">
                    <label>Numéro :</label>
                    <input type="int" name="numero">
                    <label>Poste :</label>
                    <input type="text" name="poste">
                    <button type="submit">Ajouter</button>
                </form>
                    
                <form action="modifiertdj.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du temps de jeu :</label>
                    <input type="number" name="id_temps_de_jeu" required>
                    <button type="submit">Rechercher</button>
                </form>
                    
                <form action="supprimertdj.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du temps de jeu :</label>
                    <input type="text" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </section>
		
        <section>
            <h2>Gestion de la table stade</h2>
            <div class="table-container">
                <form action="ajouterstade.php" method="POST">
                    <p class="action-title">Ajouter</p>
                    <label>Nom :</label>
                    <input type="text" name="nom" required>
                    <button type="submit">Ajouter</button>
                </form>

                <form action="modifierstade.php" method="POST">
                    <p class="action-title">Modifier</p>
                    <label>ID du stade :</label>
                    <input type="number" name="id_stade" required>
                    <button type="submit">Rechercher</button>
                </form>

                <form action="supprimerstade.php" method="POST">
                    <p class="action-title">Supprimer</p>
                    <label>ID du stade :</label>
                    <input type="number" name="idsupp" required>
                    <button type="submit">Supprimer</button>
                </form>
                    
            </div>
    
    </main>
</body>
</html>
        
       
       

        
        
        
