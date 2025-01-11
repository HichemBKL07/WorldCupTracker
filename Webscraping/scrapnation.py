import requests
from bs4 import BeautifulSoup
import mysql.connector

# Connexion à la base de données
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # Pas de mot de passe
    database="wcdb",  # Base de données utilisée
    port=3307,
    charset='utf8mb4',
    collation='utf8mb4_general_ci'
)
cursor = conn.cursor()

# URL de la page FBref pour la Coupe du Monde 1930
url = 'https://fbref.com/en/comps/1/2014/Stats-2014-World-Cup'
response = requests.get(url)
soup = BeautifulSoup(response.content, 'html.parser')

# Création de la table 'Nation' si elle n'existe pas déjà
cursor.execute('''
CREATE TABLE IF NOT EXISTS Nation (
    id_nation INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE,
    confederation VARCHAR(100)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
''')

# Fonction pour vérifier si une nation existe déjà dans la base de données
def nation_exists(nom):
    cursor.execute('SELECT id_nation FROM Nation WHERE nom = %s', (nom,))
    return cursor.fetchone() is not None

# Chercher toutes les tables sur la page pour identification
tables = soup.find_all('table')

# Trouver le tableau qui contient les équipes
for table in tables:
    # Chercher une ligne d'en-tête contenant 'Équipe' via aria-label
    header_row = table.find('th', attrs={'aria-label': 'Équipe'})
    if header_row:
        print("Ligne d'en-tête trouvée.")

        # Récupérer les équipes dans le tableau
        for row in table.find_all('tr')[1:]:  # Ignorer l'en-tête du tableau
            # Trouver toutes les colonnes de la ligne
            cols = row.find_all('td')
            if len(cols) > 1:  # Vérifier que la ligne a au moins 2 colonnes
                # Accéder à la deuxième colonne (index 1)
                team_cell = cols[0]  # Cela correspond à la colonne des équipes
                print("Contenu de la cellule d'équipe:", team_cell)  # Afficher le contenu

                # Chercher la balise <span> pour le titre de l'équipe
                span_tag = team_cell.find('span', title=True)  # Rechercher une balise <span> avec un attribut title
                if span_tag:
                    team_name_from_span = span_tag['title']  # Extraire le nom de l'équipe depuis l'attribut title
                    print(f"Nom d'équipe trouvé (span): {team_name_from_span}")

                # Chercher la balise <a> dans la cellule d'équipe
                a_tag = team_cell.find('a')
                if a_tag:
                    team_name_from_a = a_tag.get_text(strip=True)  # Extraire le texte de l'équipe
                    print(f"Nom d'équipe trouvé (a): {team_name_from_a}")  # Afficher le nom de l'équipe
                    if team_name_from_a and team_name_from_a != "Clubs":
                        confederation = "Inconnu"  # Ajuster selon les besoins
                        
                    cursor.execute('INSERT INTO Nation (nom, confederation) VALUES (%s, %s)', (team_name_from_a, confederation))
                    print(f"Ajouté : {team_name_from_a}")
                else:
                    print("Aucune balise <a> trouvée dans la cellule.")

# Commit des changements dans la base de données
hichem=0
if(hichem==1):
    conn.commit()
    print("Changements sauvegardés dans la base de données.")
else:
    print("test sans consequence fini")
# Fermer la connexion à la base de données
conn.close()
