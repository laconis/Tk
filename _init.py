import os
import shutil
import time
import re
from datetime import datetime
import mysql.connector
import traceback
import logging

# --- LOG LOCAL ---
logging.basicConfig(
    filename="mouvements.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

source = "chemin/vers/source"
destination = "chemin/vers/destination"

pattern = r"^Tk_(.*?)_\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}\.mp4$"

def log_mysql(username, fichier, action, taille, src, dst, erreur=None):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="motdepasse",
            database="logs_db"
        )
        cursor = conn.cursor()

        date_log = datetime.now()

        sql = """
            INSERT INTO logs (username, fichier, action, taille_fichier, chemin_source, chemin_destination, date_log, erreur)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """

        cursor.execute(sql, (username, fichier, action, taille, src, dst, date_log, erreur))
        conn.commit()
        cursor.close()
        conn.close()

    except Exception as e:
        logging.error(f"Erreur MySQL : {e}")

def traitement_fichiers():
    for fichier in os.listdir(source):
        chemin_fichier = os.path.join(source, fichier)

        try:
            if os.path.isfile(chemin_fichier) and os.path.getsize(chemin_fichier) > 0:

                taille = os.path.getsize(chemin_fichier)

                match = re.match(pattern, fichier)
                username = match.group(1) if match else "inconnu"

                user_folder = os.path.join(destination, username)
                os.makedirs(user_folder, exist_ok=True)

                chemin_destination = os.path.join(user_folder, fichier)

                shutil.move(chemin_fichier, chemin_destination)

                logging.info(f"Déplacement réussi : {fichier} → {chemin_destination}")

                log_mysql(username, fichier, "DEPLACEMENT", taille, chemin_fichier, chemin_destination)

        except Exception as e:
            erreur = traceback.format_exc()
            logging.error(f"Erreur sur {fichier} : {erreur}")

            log_mysql(
                username="inconnu",
                fichier=fichier,
                action="ERREUR",
                taille=0,
                src=chemin_fichier,
                dst="",
                erreur=erreur
            )

# --- BOUCLE CONTINUE ---
while True:
    traitement_fichiers()
    time.sleep(2)
