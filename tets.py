import asyncio
import websockets
import requests
import json

# WebSocket and API Configuration
WS_URL = "ws://localhost:6001/app/b28dffd574565dc8344d"
API_URL = "https://localhost/api"
AUTH_TOKEN = "your-auth-token"
HEADERS = {
    "Authorization": f"Bearer {AUTH_TOKEN}",
    "Content-Type": "application/json"
}

# Conversation and User IDs (Replace with real IDs)
CONVERSATION_ID = 1
USER_ID = 123


### ✅ 1. WebSocket Listener ###
async def listen_websocket():
    async with websockets.connect(WS_URL) as websocket:
        while True:
            message = await websocket.recv()
            print("📩 New WebSocket Message:", message)


### ✅ 2. Send a Message ###
def send_message(content):
    payload = {
        "conversation_id": CONVERSATION_ID,
        "content": content
    }
    response = requests.post(f"{API_URL}/messages/send", json=payload, headers=HEADERS)
    if response.status_code == 200:
        print("✅ Message Sent:", response.json())
    else:
        print("❌ Failed to Send Message:", response.text)


### ✅ 3. Fetch Messages ###
def fetch_messages():
    response = requests.get(f"{API_URL}/messages/{CONVERSATION_ID}", headers=HEADERS)
    if response.status_code == 200:
        messages = response.json()
        print("📥 Messages Received:", json.dumps(messages, indent=2))
    else:
        print("❌ Failed to Fetch Messages:", response.text)


### ✅ 4. Mark Message as Read ###
def mark_message_as_read(message_id):
    response = requests.post(f"{API_URL}/messages/{message_id}/read", headers=HEADERS)
    if response.status_code == 200:
        print("✅ Message Marked as Read")
    else:
        print("❌ Failed to Mark Message as Read:", response.text)


if __name__ == "__main__":
    print("🔹 Testing WebSocket & API Messaging System 🔹")

    # Start WebSocket Listener in Background
    loop = asyncio.get_event_loop()
    loop.create_task(listen_websocket())

    # Send a test message
    send_message("Hello, this is a test message!")

    # Fetch messages
    fetch_messages()

    # Mark a message as read (Replace with an actual message ID)
    mark_message_as_read(10)

    # Keep script running
    loop.run_forever()
