import requests
import json
import time

# Configuration de base
BASE_URL = "https://api.galorelotoservices.com/api"  # Modifiez cette URL selon votre configuration
TOKEN = "24|AkJxR469ax1Pw3oqqfzLuebYskI3RrmrLyQTtTTn99064578"  # Remplacez par votre token, ou supprimez l'en-tête si non requis

HEADERS = {
    "Authorization": f"Bearer {TOKEN}",
    "Content-Type": "application/json"
}

def send_message(receiver_id, content):
    """
    Envoie un message à l'utilisateur spécifié par receiver_id.
    """
    url = f"{BASE_URL}/messages/send"
    data = {
        "receiver_id": receiver_id,
        "content": content
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
    receiver_id = 6  # Identifiant de l'utilisateur destinataire (à modifier)
    nb_messages = 100  # Nombre de messages à envoyer

    print("Envoi de messages...")
    for i in range(nb_messages):
        content = f"Message numéro {i+1}"
        response = send_message(receiver_id, content)
        if response.status_code == 200:
            print(f"Envoyé: {content}")
        else:
            print(f"Erreur lors de l'envoi du message {i+1} :", response.status_code, response.text)
        time.sleep(0.1)  # Petit délai entre les envois pour éviter la surcharge

    print("\nRécupération de la conversation...")
    conv_response = get_conversation(receiver_id)
    if conv_response.status_code == 200:
        conversation = conv_response.json()
        print(json.dumps(conversation, indent=4, ensure_ascii=False))
    else:
        print("Erreur lors de la récupération de la conversation :", conv_response.status_code, conv_response.text)

if __name__ == "__main__":
    main()
