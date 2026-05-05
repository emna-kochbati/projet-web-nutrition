<!-- Chatbot Widget — inclure dans header.php ou footer.php -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
/* ── Bouton flottant ── */
#chatbot-btn {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    width: 58px; height: 58px; border-radius: 50%;
    background: #2e7d32; color: #fff; border: none; cursor: pointer;
    font-size: 1.6rem; box-shadow: 0 4px 16px rgba(0,0,0,.25);
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s;
}
#chatbot-btn:hover { transform: scale(1.1); }
#chatbot-badge {
    position: absolute; top: -4px; right: -4px;
    background: #e53935; color: #fff; border-radius: 50%;
    width: 18px; height: 18px; font-size: .7rem;
    display: flex; align-items: center; justify-content: center;
    display: none;
}

/* ── Fenêtre chatbot ── */
#chatbot-window {
    position: fixed; bottom: 100px; right: 28px; z-index: 9998;
    width: 360px; max-height: 560px;
    background: #fff; border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,.18);
    display: none; flex-direction: column; overflow: hidden;
}
#chatbot-header {
    background: #2e7d32; color: #fff;
    padding: 14px 18px; display: flex; align-items: center; gap: 10px;
}
#chatbot-header .avatar { font-size: 1.4rem; }
#chatbot-header .title  { font-weight: 700; font-size: .95rem; }
#chatbot-header .sub    { font-size: .75rem; opacity: .8; }
#chatbot-close { margin-left: auto; background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; }

#chatbot-messages {
    flex: 1; overflow-y: auto; padding: 14px;
    display: flex; flex-direction: column; gap: 10px;
    max-height: 340px;
}

.msg { max-width: 85%; padding: 10px 14px; border-radius: 14px; font-size: .85rem; line-height: 1.5; }
.msg.bot  { background: #f1f8e9; color: #1a1a1a; align-self: flex-start; border-bottom-left-radius: 4px; }
.msg.user { background: #2e7d32; color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
.msg.bot strong { color: #2e7d32; }

/* Carte Leaflet dans le chat */
.chat-map { width: 100%; height: 200px; border-radius: 10px; margin-top: 8px; }

/* Liste restaurants dans le chat */
.chat-resto-list { margin-top: 8px; display: flex; flex-direction: column; gap: 6px; }
.chat-resto-item {
    background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;
    padding: 8px 10px; font-size: .8rem;
}
.chat-resto-item strong { color: #2e7d32; display: block; margin-bottom: 2px; }
.chat-resto-item .dist  { color: #888; font-size: .75rem; }

/* Typing indicator */
.typing { display: flex; gap: 4px; align-items: center; padding: 10px 14px; }
.typing span { width: 7px; height: 7px; background: #aaa; border-radius: 50%; animation: bounce .8s infinite; }
.typing span:nth-child(2) { animation-delay: .15s; }
.typing span:nth-child(3) { animation-delay: .3s; }
@keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

/* Input zone */
#chatbot-input-area {
    padding: 10px 14px; border-top: 1px solid #eee;
    display: flex; gap: 8px; align-items: center;
}
#chatbot-input {
    flex: 1; padding: 9px 12px; border: 1px solid #ddd; border-radius: 20px;
    font-size: .85rem; outline: none;
}
#chatbot-input:focus { border-color: #2e7d32; }
#chatbot-send {
    background: #2e7d32; color: #fff; border: none; border-radius: 50%;
    width: 36px; height: 36px; cursor: pointer; font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
}
#chatbot-loc {
    background: #e8f5e9; color: #2e7d32; border: none; border-radius: 50%;
    width: 36px; height: 36px; cursor: pointer; font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    title: "Partager ma position";
}

/* Suggestions rapides */
.quick-btns { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 14px 10px; }
.quick-btn {
    background: #f1f8e9; color: #2e7d32; border: 1px solid #a5d6a7;
    border-radius: 20px; padding: 5px 12px; font-size: .78rem; cursor: pointer;
    transition: background .2s;
}
.quick-btn:hover { background: #c8e6c9; }
</style>

<!-- Bouton flottant -->
<button id="chatbot-btn" onclick="toggleChatbot()" title="Assistant EcoNutri">
    🤖
    <span id="chatbot-badge">1</span>
</button>

<!-- Fenêtre chatbot -->
<div id="chatbot-window">
    <div id="chatbot-header">
        <span class="avatar">🤖</span>
        <div>
            <div class="title">Assistant EcoNutri</div>
            <div class="sub">En ligne • Répond instantanément</div>
        </div>
        <button id="chatbot-close" onclick="toggleChatbot()">✕</button>
    </div>

    <div id="chatbot-messages"></div>

    <!-- Suggestions rapides -->
    <div class="quick-btns">
        <button class="quick-btn" onclick="sendQuick('Restaurants proches de moi')">📍 Proches de moi</button>
        <button class="quick-btn" onclick="sendQuick('Liste des restaurants')">🍴 Tous les restaurants</button>
        <button class="quick-btn" onclick="sendQuick('Restaurant tunisien')">🇹🇳 Tunisien</button>
        <button class="quick-btn" onclick="sendQuick('Restaurant italien')">🇮🇹 Italien</button>
    </div>

    <div id="chatbot-input-area">
        <button id="chatbot-loc" onclick="shareLocation()" title="Partager ma position">📍</button>
        <input type="text" id="chatbot-input" placeholder="Écrivez votre message…"
               onkeydown="if(event.key==='Enter') sendMessage()">
        <button id="chatbot-send" onclick="sendMessage()">➤</button>
    </div>
</div>

<script>
let chatOpen   = false;
let userLat    = null;
let userLng    = null;
let mapInstances = {};

function toggleChatbot() {
    chatOpen = !chatOpen;
    document.getElementById('chatbot-window').style.display = chatOpen ? 'flex' : 'none';
    document.getElementById('chatbot-badge').style.display  = 'none';
    if (chatOpen && document.getElementById('chatbot-messages').children.length === 0) {
        addBotMessage("Bonjour ! 👋 Je suis votre assistant EcoNutri.\nJe peux vous aider à trouver les restaurants **proches de vous** ou filtrer par cuisine.\n\nQue puis-je faire pour vous ?");
    }
}

function addBotMessage(text, extras) {
    const msgs = document.getElementById('chatbot-messages');
    const div  = document.createElement('div');
    div.className = 'msg bot';
    div.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');

    if (extras && extras.type === 'map' && extras.restaurants) {
        const mapId = 'map-' + Date.now();
        const mapDiv = document.createElement('div');
        mapDiv.id = mapId;
        mapDiv.className = 'chat-map';
        div.appendChild(mapDiv);

        // Liste sous la carte
        const list = document.createElement('div');
        list.className = 'chat-resto-list';
        extras.restaurants.forEach(r => {
            const item = document.createElement('div');
            item.className = 'chat-resto-item';
            const dist = r.distance_km ? parseFloat(r.distance_km).toFixed(1) + ' km' : '';
            item.innerHTML = `<strong>${escHtml(r.nom)}</strong>
                <span>${escHtml(r.adresse)}</span>
                ${dist ? `<span class="dist">📍 ${dist}</span>` : ''}`;
            list.appendChild(item);
        });
        div.appendChild(list);
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;

        // Init carte après rendu
        setTimeout(() => {
            const map = L.map(mapId).setView([extras.userLat, extras.userLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Marqueur utilisateur
            L.marker([extras.userLat, extras.userLng], {
                icon: L.divIcon({ html: '📍', className: '', iconSize: [24,24] })
            }).addTo(map).bindPopup('Votre position').openPopup();

            // Marqueurs restaurants
            extras.restaurants.forEach(r => {
                if (r.latitude && r.longitude) {
                    L.marker([r.latitude, r.longitude])
                     .addTo(map)
                     .bindPopup(`<strong>${r.nom}</strong><br>${r.adresse}`);
                }
            });
        }, 100);
        return;
    }

    if (extras && extras.type === 'list' && extras.restaurants) {
        const list = document.createElement('div');
        list.className = 'chat-resto-list';
        extras.restaurants.forEach(r => {
            const item = document.createElement('div');
            item.className = 'chat-resto-item';
            item.innerHTML = `<strong>${escHtml(r.nom)}</strong>
                <span>${escHtml(r.type_cuisine)} • ${escHtml(r.adresse)}</span>`;
            list.appendChild(item);
        });
        div.appendChild(list);
    }

    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function addUserMessage(text) {
    const msgs = document.getElementById('chatbot-messages');
    const div  = document.createElement('div');
    div.className = 'msg user';
    div.textContent = text;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function showTyping() {
    const msgs = document.getElementById('chatbot-messages');
    const div  = document.createElement('div');
    div.className = 'msg bot typing-indicator';
    div.innerHTML = '<div class="typing"><span></span><span></span><span></span></div>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
}

function sendMessage() {
    const input = document.getElementById('chatbot-input');
    const text  = input.value.trim();
    if (!text) return;
    input.value = '';
    addUserMessage(text);
    callChatbot(text);
}

function sendQuick(text) {
    addUserMessage(text);
    callChatbot(text);
}

function callChatbot(text) {
    const typing = showTyping();
    fetch('/2A35/Chatbot/ask', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: text, lat: userLat, lng: userLng })
    })
    .then(r => r.json())
    .then(data => {
        typing.remove();
        if (data.type === 'map') {
            addBotMessage(data.message, { type: 'map', restaurants: data.restaurants, userLat: data.userLat, userLng: data.userLng });
        } else if (data.type === 'list') {
            addBotMessage(data.message, { type: 'list', restaurants: data.restaurants });
        } else if (data.type === 'request_location') {
            addBotMessage(data.message);
            shareLocation();
        } else {
            addBotMessage(data.message);
        }
    })
    .catch(() => {
        typing.remove();
        addBotMessage("Une erreur s'est produite. Veuillez réessayer.");
    });
}

function shareLocation() {
    if (!navigator.geolocation) {
        addBotMessage("Votre navigateur ne supporte pas la géolocalisation.");
        return;
    }
    addBotMessage("Récupération de votre position en cours... 📍");
    navigator.geolocation.getCurrentPosition(
        pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            addBotMessage(`✅ Position récupérée ! (${userLat.toFixed(4)}, ${userLng.toFixed(4)})\nJe recherche maintenant les restaurants proches...`);
            callChatbot('restaurants proches de moi');
        },
        err => {
            addBotMessage("❌ Impossible d'accéder à votre position. Vérifiez les permissions du navigateur.");
        }
    );
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

// Afficher le badge après 2s
setTimeout(() => {
    if (!chatOpen) document.getElementById('chatbot-badge').style.display = 'flex';
}, 2000);
</script>
