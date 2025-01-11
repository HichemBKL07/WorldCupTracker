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
SCRAPER_API_KEY = '8f94dc6e821798a2bbde762199cbdf28'

def fetch_url_with_scraperapi(url, retries=3, delay=5, timeout=10):
    proxy_url = f"http://api.scraperapi.com/?api_key={SCRAPER_API_KEY}&url={url}"
    for attempt in range(retries):
        try:
            response = requests.get(proxy_url, timeout=timeout)
            if response.status_code == 200:
                return response
            else:
                print(f"Erreur {response.status_code} pour {url}, tentative {attempt + 1}")
        except requests.exceptions.RequestException as e:
            print(f"Erreur de requête pour {url} : {e}")
        time.sleep(delay)
    return None

def get_nation_id(nom_nation):
    mots_nation = nom_nation.split()
    premier_mot = mots_nation[0]
    if premier_mot in ['Korea', 'Serbia'] and len(mots_nation) > 1:
        nom_nation_complete = ' '.join(mots_nation[:2])
    else:
        nom_nation_complete = premier_mot

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
    "Italy", "Honduras", "Bosnia & Herz'na", "Algeria", "South Africa", 
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
        # Extraction de la dernière partie de l'URL (après le dernier '/')
        url_part = url_match.split('/')[-1]

        # Cas spécifique pour "Côte d'Ivoire" dans l'URL
        if "Cote-dIvoire" in url_part:
            url_part = url_part.replace("Cote-dIvoire", "Côte d'Ivoire")

        # Suppression des parties de l'URL qui contiennent des chiffres (comme les dates)
        url_part = url_part.split('-')
        filtered_parts = [part for part in url_part if not part.isdigit()]

        # Reconstruction des noms de nations possibles
        potential_nations = ' '.join(filtered_parts).split()
        nation_a_nom = []
        nation_b_nom = []

        for i in range(1, len(potential_nations)):
            test_nom = ' '.join(potential_nations[:i])
            if test_nom in nation_list:
                nation_a_nom = potential_nations[:i]
                nation_b_nom = potential_nations[i:]
                break

        nation_a_nom = ' '.join(nation_a_nom)
        nation_b_nom = ' '.join(nation_b_nom)

        id_nation_a = get_nation_id(nation_a_nom)
        id_nation_b = get_nation_id(nation_b_nom)

        print(f"{nation_a_nom}: {id_nation_a}  VS  {nation_b_nom}: {id_nation_b}")
        return id_nation_a, id_nation_b
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

def extract_own_goal_event(event, id_match, id_nation_a, id_nation_b, event_class):
    try:
        minute = extract_minute(event)
        joueur_element = event.find('a')
        if joueur_element:
            joueur_nom = joueur_element.get_text(strip=True)
            print(joueur_nom)
            # Si event_class est 'a', on attribue le joueur à l'équipe 'b' et vice-versa
            id_nation = id_nation_b if event_class == 'a' else id_nation_a
            id_joueur = get_player_id(joueur_nom, id_nation)

            if minute is not None and id_joueur is not None:
                with conn.cursor() as cursor:
                    cursor.execute('''
                        INSERT INTO but (id_match, id_joueur, timing, csc)
                        VALUES (%s, %s, %s, %s)
                    ''', (id_match, id_joueur, minute, True))
                print(f"Own goal ajouté pour le joueur {joueur_nom} à la minute {minute}")

    except Exception as e:
        print(f"Erreur lors de l'extraction d'un own goal : {e}")

def extract_penalty_goal_event(event, id_match, id_nation):
    try:
        minute = extract_minute(event)
        joueur_element = event.find('a')
        if joueur_element:
            joueur_nom = joueur_element.get_text(strip=True)
            id_joueur = get_player_id(joueur_nom, id_nation)

            if minute is not None and id_joueur is not None:
                with conn.cursor() as cursor:
                    cursor.execute('''
                        INSERT INTO but (id_match, id_joueur, timing, penalty)
                        VALUES (%s, %s, %s, %s)
                    ''', (id_match, id_joueur, minute, True))
                print(f"But sur penalty ajouté pour le joueur {joueur_nom} à la minute {minute}")

    except Exception as e:
        print(f"Erreur lors de l'extraction d'un but sur penalty : {e}")

def extract_second_yellow_card_event(event, id_match, id_nation):
    try:
        minute = extract_minute(event)
        joueur_element = event.find('a')
        if joueur_element:
            joueur_nom = joueur_element.get_text(strip=True)
            id_joueur = get_player_id(joueur_nom, id_nation)

            if minute is not None and id_joueur is not None:
                with conn.cursor() as cursor:
                    cursor.execute('''
                        INSERT INTO faute (id_match, id_joueur, minute, type_faute)
                        VALUES (%s, %s, %s, %s)
                    ''', (id_match, id_joueur, minute, 'second_yellow'))
                print(f"Deuxième carton jaune (transformé en rouge) ajouté pour {joueur_nom} à la minute {minute}")

    except Exception as e:
        print(f"Erreur lors de l'extraction d'un deuxième carton jaune : {e}")

def extract_events_from_match(url_match, id_match):
    id_nation_a, id_nation_b = extract_nations_from_url(url_match)
    response = fetch_url_with_scraperapi(url_match)
    if not response:
        print(f"Erreur : impossible d'obtenir la réponse pour {url_match}")
        return

    soup = BeautifulSoup(response.content, 'html.parser')
    for event_class, id_nation in [('a', id_nation_a), ('b', id_nation_b)]:
        if not id_nation:
            print(f"Nation {event_class} non trouvée pour le match {id_match}")
            continue

        events = soup.find_all('div', class_=f'event {event_class}')
        if not events:
            print(f"Aucun événement trouvé pour {event_class} dans le match ID {id_match}")
            continue
        hich=0
        for event in events:
            
            event_icon = event.find('div', class_='event_icon')
            if not event_icon:
                print(f"Événement sans icône pour {event_class} dans le match ID {id_match}")
                continue

            icon_class = event_icon.get('class', [])
           

            if 'own_goal' in icon_class:
                hich=1
                print(f"Own goal détecté pour le match ID {id_match}")
                extract_own_goal_event(event, id_match, id_nation_a, id_nation_b, event_class)
            elif 'penalty_goal' in icon_class:
                hich=1
                print(f"Penalty goal détecté pour le match ID {id_match}")
                extract_penalty_goal_event(event, id_match, id_nation)
            elif 'yellow_red_card' in icon_class:
                hich=1
                print(f"Deuxième carton jaune détecté pour le match ID {id_match}")
                extract_second_yellow_card_event(event, id_match, id_nation)
        if(hich==0):
            print("rien de nouveau pour ce match pour cette équipe")
           
            
            




def extract_all_match_events():
    with conn.cursor() as cursor:
        # Récupère l'ID du match, l'URL, et l'année de la compétition associée via une jointure
        cursor.execute('''
            SELECT m.id_match, m.url_match, c.annee
            FROM `match` AS m
            JOIN `Competition` AS c ON m.id_competition = c.id_competition
        ''')
        all_matches = cursor.fetchall()

    for id_match, url_match, annee in all_matches:
        print(f"Extraction des événements pour le match ID {id_match} - Année de la compétition : {annee}")
        extract_events_from_match(url_match, id_match)

    conn.commit()
    print("Tous les événements de matchs ont été extraits et enregistrés.")


# Appel de la fonction principale pour lancer l'extraction
extract_all_match_events()

# Fermer la connexion
conn.close()
