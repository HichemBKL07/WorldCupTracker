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
SCRAPER_API_KEY = #insérer ici la clé api de ScraperAPI

# Fonction pour obtenir l'id_nation à partir du nom de la nation
def get_nation_id(nation_name):
    cursor.execute('SELECT id_nation FROM Nation WHERE nom = %s', (nation_name,))
    result = cursor.fetchone()
    return result[0] if result else None

# Fonction pour obtenir l'id de la compétition à partir de l'année
def get_competition_id(year):
    cursor.execute('SELECT id_competition FROM Competition WHERE annee = %s', (year,))
    result = cursor.fetchone()
    return result[0] if result else None

# Fonction pour insérer ou récupérer l'id du stade
def get_or_insert_stade(stade_name):
    cursor.execute('SELECT id_stade FROM Stade WHERE nom = %s', (stade_name,))
    result = cursor.fetchone()
    if result:
        return result[0]
    cursor.execute('INSERT INTO Stade (nom) VALUES (%s)', (stade_name,))
    conn.commit()
    return cursor.lastrowid

# Fonction pour utiliser ScraperAPI
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
        time.sleep(delay)
    return None

# Fonction principale pour extraire les matchs
def extract_matches_from_calendar(calendar_url):
    year = 2022
    
    id_competition = get_competition_id(year)
    if not id_competition:
        print(f"Erreur : aucune compétition trouvée pour l'année {year}")
        return

    response = fetch_url_with_scraperapi(calendar_url)
    if not response:
        print(f"Erreur : impossible d'obtenir la réponse pour {calendar_url}")
        return

    soup = BeautifulSoup(response.content, 'html.parser')
    match_table = soup.find('table')
    if not match_table:
        print("Erreur : table de matchs non trouvée.")
        return

    for row in match_table.find_all('tr')[1:]:
        cells = row.find_all('td')
        if len(cells) < 11:
            continue

        tour = row.find('th').get_text(strip=True)
        date_match = cells[2].get_text(strip=True)
        heure_match = cells[3].get_text(strip=True)
        
        equipe_domicile = cells[4].find('a').get_text(strip=True) if cells[4].find('a') else None
        id_nation_domicile = get_nation_id(equipe_domicile)
        
        equipe_exterieur = cells[8].find('a').get_text(strip=True) if cells[8].find('a') else None
        id_nation_exterieur = get_nation_id(equipe_exterieur)

        if not id_nation_domicile or not id_nation_exterieur:
            print(f"Erreur : nation non trouvée pour {equipe_domicile} ou {equipe_exterieur}")
            continue

        # Extraire le score depuis le texte du lien
        score_text = cells[6].find('a').get_text(strip=True) if cells[6].find('a') else None
        score = score_text.split('–') if score_text else [None, None]
        score_domicile = int(score[0].strip()) if score[0] and score[0].isdigit() else None
        score_exterieur = int(score[1].strip()) if len(score) > 1 and score[1].isdigit() else None

        affluence = int(cells[9].get_text(strip=True).replace(',', '')) if cells[9].get_text(strip=True) else None

        stade_name = cells[10].get_text(strip=True)
        id_stade = get_or_insert_stade(stade_name)
        url_match = 'https://fbref.com' + cells[12].find('a')['href'] if cells[12].find('a') else None

        cursor.execute('''
            INSERT IGNORE INTO `match` (tour, date_match, heure, id_nation_local, score_local, score_visiteur, id_nation_visiteur, affluence, id_stade, id_competition, url_match)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        ''', (tour, date_match, heure_match, id_nation_domicile, score_domicile, score_exterieur, id_nation_exterieur, affluence, id_stade, id_competition, url_match))

        print(f"Ajouté match :\n"
              f"Tour : {tour}\n"
              f"Date : {date_match}\n"
              f"Heure : {heure_match}\n"
              f"Équipe domicile : {equipe_domicile} (Score : {score_domicile})\n"
              f"Équipe extérieur : {equipe_exterieur} (Score : {score_exterieur})\n"
              f"Affluence : {affluence}\n"
              f"Stade : {stade_name}\n"
              f"URL du match : {url_match}\n"
              "---------------------------\n")

# URL de la page des matchs de la Coupe du Monde 
url = 'https://fbref.com/en/comps/1/2022/schedule/2022-World-Cup-Scores-and-Fixtures'
extract_matches_from_calendar(url)

# Commit des changements dans la base de données
conn.commit()
print("Changements sauvegardés dans la base de données.")

# Fermer la connexion à la base de données
conn.close()
