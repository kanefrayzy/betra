const fs = require('fs');
const https = require('https');
const WebSocket = require('ws');


const server = https.createServer({
    key: fs.readFileSync('/etc/letsencrypt/live/flashgame.live/privkey.pem', 'utf8'),
    cert: fs.readFileSync('/etc/letsencrypt/live/flashgame.live/fullchain.pem', 'utf8')
});


const wss = new WebSocket.Server({server});

let online = 200;
const clients = new Map();

wss.on('connection', function connection(ws) {
    online++;
    //console.log('Новое соединение установлено');
    updateOnlineCount();

    ws.on('message', function incoming(message) {
        try {
            const messageData = JSON.parse(message.toString());

            if (messageData.type === 'register') {
                clients.set(messageData.user_id, ws);
                return;
            }


            if (messageData.type === 'deleteMessage') {
                wss.clients.forEach(function each(client) {
                    if (client.readyState === WebSocket.OPEN) {
                        client.send(JSON.stringify(messageData));
                    }
                });
                return;
            }

            if (messageData.type === 'notification') {
                const recipientWs = clients.get(messageData.user_id);
                if (recipientWs && recipientWs.readyState === WebSocket.OPEN) {
                    recipientWs.send(JSON.stringify(messageData));
                }
                return;
            }

            if (messageData.type === 'rain') {
                wss.clients.forEach(function each(client) {
                    if (client.readyState === WebSocket.OPEN) {
                        client.send(JSON.stringify(messageData));
                    }
                });
                return;
            }

            wss.clients.forEach(function each(client) {
                if (client !== ws && client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify(messageData));
                }
            });
        } catch (error) {
            console.error('Ошибка при обработке сообщения:', error);
            ws.close(1011, 'Unexpected error');
        }
    });

    ws.on('close', function () {
        online--;
        //console.log('Соединение закрыто');
        updateOnlineCount();
        clients.forEach((value, key) => {
            if (value === ws) {
                clients.delete(key);
            }
        });
    });

    ws.on('error', function (error) {
        console.error('WebSocket ошибка:', error);
    });
});

server.listen(3000, () => {
    console.log('WebSocket сервер запущен на порту 3000');
});

function updateOnlineCount() {
    const onlineMessage = JSON.stringify({type: 'onlineCount', count: online});
    wss.clients.forEach(function each(client) {
        if (client.readyState === WebSocket.OPEN) {
            client.send(onlineMessage);
        }
    });
}
