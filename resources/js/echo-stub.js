// Заглушка для Laravel Echo
if (typeof window.Echo === 'undefined') {
    window.Echo = {
        socketId: function() { return null; },
        private: function() {
            return {
                listen: function() {},
                listenForWhisper: function() {},
            };
        },
        channel: function() {
            return {
                listen: function() {},
                listenForWhisper: function() {},
            };
        },
        join: function() {
            return {
                listen: function() {},
                listenForWhisper: function() {},
                here: function() {},
                joining: function() {},
                leaving: function() {},
            };
        }
    };

    console.info('Laravel Echo stub loaded - real-time features will not work');
}

export default window.Echo;
