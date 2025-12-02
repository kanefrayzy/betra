import WebSocket from 'ws';

const websocketUrl = 'ws://localhost:8080';
const numberOfRequests = 100;
let requestsSent = 0;
let responsesReceived = 0;
let startTimestamp;

async function testWebSocket() {
    const ws = new WebSocket(websocketUrl);

    await new Promise((resolve, reject) => {
        ws.on('open', () => {
            console.log('WebSocket connected');
            startTimestamp = Date.now();

            for (let i = 0; i < numberOfRequests; i++) {
                const message = `Message ${i + 1}`;
                const messageTimestamp = Date.now();
                ws.send(message, (err) => {
                    if (err) {
                        reject(err);
                    } else {
                        console.log(`Sent: ${message} at ${new Date(messageTimestamp).toISOString()}`);
                        requestsSent++;
                    }
                });
            }
        });

        ws.on('message', (data) => {
            const responseTimestamp = Date.now();
            console.log(`Received: ${data} at ${new Date(responseTimestamp).toISOString()}`);
            responsesReceived++;

            if (responsesReceived === numberOfRequests) {
                const endTimestamp = Date.now();
                const duration = endTimestamp - startTimestamp;
                console.log(`Test duration: ${duration} ms`);
                ws.close();
                resolve();
            }
        });

        ws.on('close', () => {
            console.log('WebSocket disconnected');
            console.log(`Total requests sent: ${requestsSent}`);
            console.log(`Total responses received: ${responsesReceived}`);
        });

        ws.on('error', (err) => {
            console.error('WebSocket error:', err.message);
            reject(err);
        });
    });
}

testWebSocket().catch(err => {
    console.error('Test encountered an error:', err);
});
