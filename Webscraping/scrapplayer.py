import requests
from bs4 import BeautifulSoup
import mysql.connector
import time

# Connexion à la base de données
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="wcdb",
    port=3307,
    charset='utf8mb4',
    collation='utf8mb4_general_ci'
)
cursor = conn.cursor()

# Clé API ScraperAPI
SCRAPER_API_KEY = '8f94dc6e821798a2bbde762199cbdf28'

# Création de la table 'Joueur' si elle n'existe pas déjà
cursor.execute('''
CREATE TABLE IF NOT EXISTS Joueur (
    id_joueur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE DEFAULT NULL,
    poste VARCHAR(10),
    id_nation INT,
    FOREIGN KEY (id_nation) REFERENCES Nation(id_nation),
    UNIQUE KEY unique_joueur (nom, prenom, id_nation)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
''')

# Fonction pour obtenir l'id_nation à partir du nom de la nation
def get_nation_id(nation_name):
    cursor.execute('SELECT id_nation FROM Nation WHERE nom = %s', (nation_name,))
    result = cursor.fetchone()
    return result[0] if result else None

# Fonction pour faire une requête avec ScraperAPI avec gestion de temporisation et de réessais
def fetch_url_with_scraperapi(url, retries=3, delay=5, timeout=10):
    proxy_url = f"http://api.scraperapi.com/?api_key={SCRAPER_API_KEY}&url={url}"
    for attempt in range(retries):
        try:
            response = requests.get(proxy_url, timeout=timeout)
            if response.status_code == 200:
                return response
            else:
                print(f"Erreur {response.status_code} pour {url}, tentative {attempt + 1}")
        except requests.exceptions.ConnectTimeout:
            print(f"Erreur de connexion : tentative {attempt + 1} expirée pour l'URL {url}")
        except requests.exceptions.RequestException as e:
            print(f"Erreur de requête pour l'URL {url} : {e}")
        time.sleep(delay)  # Temporisation entre les tentatives
    return None

# Fonction pour extraire les informations des joueurs à partir de la page de l'équipe
def extract_players_from_team_page(team_url, nation_name):
    team_response = fetch_url_with_scraperapi(team_url)
    if not team_response:
        print(f"Erreur : impossible d'obtenir la réponse pour {team_url}")
        return

    team_soup = BeautifulSoup(team_response.content, 'html.parser')

    # Récupérer l'identifiant de la nation
    id_nation = get_nation_id(nation_name)
    if not id_nation:
        print(f"Nation non trouvée dans la base de données : {nation_name}")
        return

    # Identifier la table des joueurs
    player_table = team_soup.find('table')
    if player_table:
        for row in player_table.find_all('tr')[1:]:
            th = row.find('th')
            cols = row.find_all('td')
            
            # Vérifier que `th` et `cols` ne sont pas vides
            if th and cols:
                full_name = cols[0].get_text(strip=True)
                poste = cols[1].get_text(strip=True)
                
                # Vérifier si `full_name` n'est pas vide
                if full_name:
                    # Diviser le nom complet en prénom et nom
                    name_parts = full_name.split()
                    if len(name_parts) == 1:
                        prenom = None  # Laisser le prénom vide si un seul nom
                        nom = name_parts[0]
                    else:
                        prenom = name_parts[0]
                        nom = " ".join(name_parts[1:])

                    if nom:  # Assurer que 'nom' n'est pas vide
                        cursor.execute('''
                            SELECT COUNT(*) FROM Joueur
                            WHERE nom = %s AND (prenom = %s OR %s IS NULL) AND id_nation = %s
                        ''', (nom, prenom, prenom, id_nation))

                        if cursor.fetchone()[0] == 0:
                            cursor.execute('''
                                INSERT INTO Joueur (nom, prenom, date_naissance, poste, id_nation)
                                VALUES (%s, %s, %s, %s, %s)
                            ''', (nom, prenom, None, poste, id_nation))
                            print(f"Ajouté joueur : {nom} {prenom if prenom else ''}, Poste: {poste}, Nation ID: {id_nation}")
                        else:
                            print(f"Doublon détecté pour le joueur : {nom} {prenom if prenom else ''}, Nation: {nation_name}")
                    else:
                        print(f"Informations incomplètes pour le joueur : Nom complet récupéré = {full_name}, Position = {poste}")
                else:
                    print("Nom du joueur non trouvé pour cette ligne.")

# Liste des années des éditions de la Coupe du Monde ici y a que 2022 car le fortmat est un peu different que les annes precedentes mais de 1950 a 2014 je peux tout faire d'un coup
world_cup_years = [2022]

# Boucle sur chaque année pour récupérer les joueurs
for year in world_cup_years:
    print(f"--- Traitement de la Coupe du Monde {year} ---")
    
    # URL de la page FBref pour l'année spécifique
    url = f'https://fbref.com/fr/comps/1/{year}/Stats-{year}-World-Cup'
    response = fetch_url_with_scraperapi(url)
    
    if not response:
        print(f"Erreur lors de l'accès à la page principale pour l'année {year}.")
        continue
    
    soup = BeautifulSoup(response.content, 'html.parser')

    # Chercher toutes les tables sur la page pour identification
    tables = soup.find_all('table')
    for table in tables:
        header_row = table.find('th', attrs={'aria-label': 'Équipe'})
        if header_row:
            for row in table.find_all('tr')[1:]:
                cols = row.find_all('td')
                if len(cols) > 1:
                    team_cell = cols[0]
                    a_tag = team_cell.find('a')

                    if a_tag:
                        team_name = a_tag.get_text(strip=True)
                        team_url = 'https://fbref.com' + a_tag['href']

                        print(f"Traitement de l'équipe : {team_name}")
                        extract_players_from_team_page(team_url, team_name)

                        # Temporisation pour respecter le quota de ScraperAPI
                        time.sleep(1)  # Délai pour limiter le nombre de requêtes par minute

    # Commit des changements dans la base de données pour chaque année
    conn.commit()
    print(f"Changements sauvegardés pour la Coupe du Monde {year} dans la base de données.")

    # Temporisation entre les éditions pour ne pas surcharger l'API
    time.sleep(1)  # Délai entre les années pour limiter le nombre de requêtes

# Fermer la connexion à la base de données
conn.close()
print("Tous les changements sont sauvegardés et la connexion à la base de données est fermée.")
