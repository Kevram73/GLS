import requests
import json
import time
from datetime import datetime

# Configuration de base
BASE_URL = "https://api.galorelotoservices.com/api"  # Modifiez cette URL selon votre configuration
TOKEN = "24|AkJxR469ax1Pw3oqqfzLuebYskI3RrmrLyQTtTTn99064578"  # Remplacez par votre token, ou supprimez l'en-tête si non requis

HEADERS = {
    "Authorization": f"Bearer {TOKEN}",
    "Content-Type": "application/json"
}

def send_message(receiver_id, content, date_sent, time_sent):
    """
    Envoie un message à l'utilisateur spécifié par receiver_id avec la date et l'heure précises.
    """
    url = f"{BASE_URL}/messages/send"
    data = {
        "receiver_id": receiver_id,
        "content": content,
        "date_sent": date_sent,  # Format : YYYY-MM-DD
        "time_sent": time_sent   # Format : HH:MM:SS
    }
    response = requests.post(url, json=data, headers=HEADERS)
    return response

def get_conversation(other_user_id):
    """
    Récupère les messages échangés avec l'utilisateur dont l'ID est other_user_id.
    """
    url = f"{BASE_URL}/messages/conversation/{other_user_id}"
    response = requests.get(url, headers=HEADERS)
    return response

def main():
    receiver_id = 6   # Identifiant de l'utilisateur destinataire (à modifier)
    nb_messages = 100  # Nombre de messages à envoyer

    print("Envoi de messages...")
    for i in range(nb_messages):
        content = f"Message numéro {i+1}"
        now_dt = datetime.now()
        date_sent = now_dt.strftime('%Y-%m-%d')
        time_sent = now_dt.strftime('%H:%M:%S')
        response = send_message(receiver_id, content, date_sent, time_sent)
        if response.status_code == 200:
            print(f"Envoyé: {content} à {date_sent} {time_sent}")
        else:
            print(f"Erreur lors de l'envoi du message {i+1} :", response.status_code, response.text)
        time.sleep(0.1)  # Petit délai pour éviter de surcharger le serveur

    print("\nRécupération de la conversation...")
    conv_response = get_conversation(receiver_id)
    if conv_response.status_code == 200:
        conversation = conv_response.json()
        print(json.dumps(conversation, indent=4, ensure_ascii=False))
    else:
        print("Erreur lors de la récupération de la conversation :", conv_response.status_code, conv_response.text)

if __name__ == "__main__":
    main()
