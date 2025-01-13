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

# Clé API ScraperAPI
SCRAPER_API_KEY = '438f06b07036279e24a403ec7790d0de'

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


def get_nation_id(nom_nation):
    if "cote divoire" in nom_nation:
        nom_nation = nom_nation.replace("cote divoire", "Côte d'Ivoire")
    mots_nation = nom_nation.split()
    
    premier_mot = mots_nation[0]
    if len(mots_nation)>1 and mots_nation[1]=="and":
        print("coucou amk")
        mots_nation[1]= "&"
    if premier_mot in ['Korea', 'serbia'] and len(mots_nation) > 1:
        
        print("coucou")
        nom_nation_complete = ' '.join(mots_nation[:2])
    else:
        nom_nation_complete = ' '.join(mots_nation[:len(mots_nation)])
        print(f"LE NOM NATION COMPLET: {nom_nation_complete}")

    # Crée un nouveau curseur pour éviter les conflits de résultats non lus
    with conn.cursor() as cursor:
        try:
            cursor.execute('SELECT id_nation FROM Nation WHERE nom LIKE %s', (nom_nation_complete + '%',))
            result = cursor.fetchone()
            cursor.fetchall()  # Vider tous les résultats restants pour éviter les erreurs

            if result:
                return result[0]
            else:
                print(f"Nation {nom_nation} non trouvée dans la base de données.")
                return None
        except Exception as e:
            print(f"Erreur lors de la récupération de l'ID de la nation : {e}")
            cursor.fetchall()  # Vider les résultats restants en cas d'exception
            return None


nation_list = [
    "Uruguay", "Russia", "Saudi Arabia", "Egypt", "Spain", "Portugal", 
    "IR Iran", "Morocco", "France", "Denmark", "Peru", "Australia", 
    "Croatia", "Argentina", "Nigeria", "Iceland", "Brazil", "Switzerland", 
    "Serbia", "Costa Rica", "Sweden", "Mexico", "Korea Republic", "Germany", 
    "Belgium", "England", "Tunisia", "Panama", "Colombia", "Japan", 
    "Senegal", "Poland", "Netherlands", "Ecuador", "Qatar", "United States", 
    "Wales", "Canada", "Cameroon", "Ghana", "Chile", "Greece", "Côte d'Ivoire", 
    "Italy", "Honduras", "Bosnia & Herzegovina", "Algeria", "South Africa", 
    "Slovenia", "Paraguay", "Slovakia", "New Zealand", "Korea DPR", 
    "Trin & Tobago", "Serbia & Montenegro", "Angola", "Czechia", "Togo", 
    "Ukraine", "Türkiye", "China PR", "Rep. of Ireland", "Romania", "Bolivia", 
    "Bulgaria", "Norway", "Austria", "Scotland", "Czechoslovakia", 
    "Soviet Union", "West Germany", "Yugoslavia", "UAE", "Hungary", 
    "Northern Ireland", "Iraq", "El Salvador", "Kuwait", "Germany DR", 
    "Zaire", "Haiti", "Israel", "India", "Cuba", "Dutch East Indies"
]
def extract_nations_from_url(url_match):
    
    
  try:
    # Extraire la dernière partie de l'URL après le dernier slash
    url_part = url_match.split('/')[-1].lower()
    print(url_part)
    # Remplacer "Cote-dIvoire" par "Côte d'Ivoire" si présent
    if "cote-divoire" in url_part:
        print("ehhhhhhhhhhhhhhhh")
        url_part = url_part.replace("cote-divoire", "Côte d'Ivoire")
    if "trinite" in url_part:
        url_part = url_part.replace("trinite", "Trin")
    if "republic-of-ireland" in url_part:
        print("kljdfhigdhiojfhzqauiejrzeoparzbe")
        url_part = url_part.replace("republic-of-ireland", "Rep. of Ireland")
    if "united-arab-emirates" in url_part:
        print("kljdfhigdhiojfhzqauiejrzeoparzbe")
        url_part = url_part.replace("united-arab-emirates", "UAE")
    if "fr-yugoslavia" in url_part:
        print("kljdfhigdhiojfhzqauiejrzeoparzbe")
        url_part = url_part.replace("fr-yugoslavia", "yugoslavia")
    if "dutch-east-indies" in url_part:
        print("kljdfhigdhiojfhzqauiejrzeoparzbe")
        url_part = url_part.replace("dutch-east-indies", "dutch east indies")

    # Limiter la partie analysée en excluant le texte après le mois s'il est présent
    mois = ["january", "february", "march", "april", "may", "june", "july", 
            "august", "september", "october", "november", "december"]
    for mois_nom in mois:
        mois_index = url_part.find(mois_nom)
        if mois_index != -1:
            url_part = url_part[:mois_index]
            break

    # Diviser l'URL en parties basées sur les tirets et enlever la dernière partie (date)
    url_parts = url_part.split('-')
    url_parts.remove(url_parts[-1])
    
  
    # Initialisation des noms de nations
    nation_a_nom, nation_b_nom = None, None

    # Détection des nations autour de "and" ou "&"
    for i, part in enumerate(url_parts):
        if part in ["and", "&"] and i > 0 and i < len(url_parts) - 1:
            # Gestion des cas où "and" ou "&" est présent
            if i == 1:
                nation_a_nom = url_parts[i - 1] + ' ' + part + ' ' + url_parts[i + 1]
                nation_b_nom = ' '.join(url_parts[i + 2:])
            else:
                nation_b_nom = url_parts[i - 1] + ' ' + part + ' ' + url_parts[i + 1]
                nation_a_nom = ' '.join(url_parts[:i - 1])

        # Cas où il y a exactement deux mots, chaque mot étant une nation
        elif len(url_parts) == 2:
            nation_a_nom = url_parts[0]
            nation_b_nom = url_parts[1]

    # Gestion des cas avec une nation composée de deux mots et l'autre d'un seul mot
    if len(url_parts) == 3 and nation_a_nom is None and nation_b_nom is None:
        print("AHHHHHHHHHHHHHHHHHHHH")
        # Tester les deux combinaisons possibles
        # Cas où la première nation est composée de deux mots
        possible_nation_a = ' '.join(url_parts[:2])
        possible_nation_b = url_parts[2]

        if get_nation_id(possible_nation_a):  # Si la nation est trouvée dans la BDD
            nation_a_nom = possible_nation_a
            nation_b_nom = possible_nation_b
        else:
            # Cas où la seconde nation est composée de deux mots
            nation_a_nom = url_parts[0]
            nation_b_nom = ' '.join(url_parts[1:])
    if len(url_parts)==4 and nation_a_nom is None and nation_b_nom is None:
        nation_a_nom = ' '.join(url_parts[:2])
        nation_b_nom = ' '.join(url_parts[2:])
    # Vérifier que les deux nations ont bien été trouvées
    if nation_a_nom and nation_b_nom:
        id_nation_a = get_nation_id(nation_a_nom.replace('-', ' '))  # Remplacer les tirets par des espaces
        id_nation_b = get_nation_id(nation_b_nom.replace('-', ' '))
        print(f"{nation_a_nom} VS {nation_b_nom}")
        
        return id_nation_a, id_nation_b
    else:
        print("Impossible de détecter les deux nations.")
        return None, None

  except Exception as e:
    print(f"Erreur lors de l'extraction des nations de l'URL : {e}")
    return None, None
def get_player_id(player_name, id_nation):
    with conn.cursor() as read_cursor:
        try:
            name_parts = player_name.split()
            prenom = name_parts[0] if len(name_parts) > 1 else None
            nom = ' '.join(name_parts[1:]) if prenom else name_parts[0]

            if prenom:
                read_cursor.execute(
                    'SELECT id_joueur FROM Joueur WHERE nom = %s AND prenom = %s AND id_nation = %s',
                    (nom, prenom, id_nation)
                )
            else:
                read_cursor.execute(
                    'SELECT id_joueur FROM Joueur WHERE nom = %s AND prenom IS NULL AND id_nation = %s',
                    (nom, id_nation)
                )

            result = read_cursor.fetchone()
            if result:
                return result[0]
            else:
                print(f"Joueur {player_name} non trouvé dans la base de données pour la nation {id_nation}.")
                return None
        except Exception as e:
            print(f"Erreur dans get_player_id pour {player_name} : {e}")
            return None

def extract_minute(event):
    try:
        timing_div = event.find_all('div')[0].get_text(strip=True)
        timing_text = timing_div.split('’')[0].replace('\xa0', '').strip()
        if '+' in timing_text:
            base_minute, additional = map(int, timing_text.split('+'))
            return base_minute + additional
        return int(timing_text)
    except Exception as e:
        print(f"Erreur lors de l'extraction de la minute : {e}")
        return None


def get_player_id(player_name, id_nation):
    with conn.cursor() as read_cursor:
        try:
            name_parts = player_name.split()
            prenom = name_parts[0] if len(name_parts) > 1 else None
            nom = ' '.join(name_parts[1:]) if prenom else name_parts[0]

            if prenom:
                read_cursor.execute(
                    'SELECT id_joueur FROM Joueur WHERE nom = %s AND prenom = %s AND id_nation = %s',
                    (nom, prenom, id_nation)
                )
            else:
                read_cursor.execute(
                    'SELECT id_joueur FROM Joueur WHERE nom = %s AND prenom IS NULL AND id_nation = %s',
                    (nom, id_nation)
                )

            result = read_cursor.fetchone()
            if result:
                return result[0]
            else:
                print(f"Joueur {player_name} non trouvé dans la base de données pour la nation {id_nation}.")
                return None
        except Exception as e:
            print(f"Erreur dans get_player_id pour {player_name} : {e}")
            return None

def extract_playing_time_table(url_match, id_match):
  
    id_nation_a, id_nation_b = extract_nations_from_url(url_match)
    response = fetch_url_with_scraperapi(url_match)
    if not response:
        print(f"Erreur : impossible d'obtenir la réponse pour {url_match}")
        return

    soup = BeautifulSoup(response.content, 'html.parser')
    tables = soup.find_all('table')

    # Variables pour suivre les nations A et B
    table_found_a = False
    table_found_b = False
    table_count=0
    for table in tables:
        # Vérifier si la table a les en-têtes de colonnes "Player", "Age", "Min", etc.
        headers = [th.get_text(strip=True) for th in table.find_all('th')]
        if "Player" in headers and "Age" in headers and "Min" in headers:
            
            # Associer la table à la nation correspondante
            table_count += 1
            
            
            # Associer la table à la nation correspondante
            if not table_found_a:
                id_nation = id_nation_a
                table_found_a = True
            elif table_count == 3:  # Ignorer la deuxième table et passer à la troisième (c'est la 8 eme table pas la 3 en 2018 et 2022)
                id_nation = id_nation_b
            else:
                continue  # Passer à la table suivante

            # Parcourir chaque ligne de joueur dans la table
            rows = table.find_all('tr')[1:]  # Ignorer l'en-tête
            for row in rows:
                cells = row.find_all('td')
                thh=row.find_all('th')
                if len(cells) < 4:
                    continue

                joueur_nom = thh[0].get_text(strip=True)
                id_joueur = get_player_id(joueur_nom, id_nation)
                if not id_joueur:
                    print(f"Joueur {joueur_nom} non trouvé pour la nation avec ID {id_nation}")
                    continue

                try:
                    
                    temps_de_jeu = int(cells[3].get_text(strip=True)) # c est cell4 en 2022
                    numero = int(cells[0].get_text(strip=True)) #on a plus acces à cette donnée a partir de 1950
                    poste = cells[1].get_text(strip=True)

                    # Insertion dans la base de données
                    with conn.cursor() as cursor:
                        cursor.execute('''
                            INSERT INTO temps_de_jeu (id_joueur, id_match, temps_de_jeu, poste)
                            VALUES (%s, %s, %s, %s)
                        ''', (id_joueur, id_match, temps_de_jeu, poste))
                        conn.commit()
                    print(f"Temps de jeu ajouté pour {joueur_nom}: {temps_de_jeu} minutes, poste {poste}")
                except Exception as e:
                    print(f"Erreur lors de l'insertion pour {joueur_nom} : {e}")

def extract_all_playing_time():
    with conn.cursor() as cursor:
        cursor.execute('SELECT id_match, url_match FROM `match` where YEAR(date_match)=2018')# precisez l'année car les tabelaux ne sont plus exactement les memes  en 2018 et 2022  par rapport au reste. 

        all_matches = cursor.fetchall()

    for id_match, url_match in all_matches:
        print(f"Extraction du temps de jeu pour le match ID {id_match}")
        extract_playing_time_table(url_match, id_match)
        print("\n" * 4)  # Séparation entre matchs
        time.sleep(0.05)

    conn.commit()  # Commit final pour enregistrer toutes les données
    print("Tous les temps de jeu ont été extraits et enregistrés.")

# Appel de la fonction pour lancer l'extraction complète
extract_all_playing_time()
