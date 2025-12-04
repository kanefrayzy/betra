/**
 * Chat System - Production Version
 *  SPA-Compatible Singleton Pattern
 */

if (typeof window.ChatSystem === 'undefined') {
    window.ChatSystem = class ChatSystem {
        constructor() {
            this.ws = null;
            this.preserveConnection = false; //  флаг для SPA навигации
            this.config = {
                wsUrl: 'wss://betra1.com:3000',
                maxMessages: 50,
                scrollThreshold: 100,
                messageLimit: 160,
                reconnectDelay: 3000,
                debounceDelay: 300
            };

            this.state = {
                addedMessageIds: new Set(),
                isScrolledToBottom: true,
                currentChannel: 'global', //  Канал по умолчанию
                currentUser: null,
                userCache: new Map(),
                emojiObj: {},
                isNavigating: false, //  флаг навигации
                lastOnlineCount: 0 //  Сохраняем последний онлайн счёт
            };

            this.elements = {};
            this.sounds = {};

            this.init();
            this.setupNavigationListeners(); //  инициализация слушателей навигации
        }

    /**
     * Инициализация системы
     */
    init() {
        // Проверяем, загружен ли DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.performInit();
            });
        } else {
            // DOM уже загружен, инициализируем сразу
            this.performInit();
        }
    }

    /**
     *  Выполнение инициализации
     */
    performInit() {
        this.initElements();
        this.initSounds();
        this.initWebSocket();
        this.initEventListeners();
        this.initCurrentUser();
        this.loadInitialMessages();
        this.initChatState();
    }

    /**
     * Настройка слушателей Livewire Navigate
     */
    setupNavigationListeners() {
        // Закрытие WebSocket при выгрузке страницы
        window.addEventListener('beforeunload', () => {
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                this.ws.close(1000, 'Page unload');
            }
        });
        
        // Перед навигацией - сохраняем состояние
        document.addEventListener('livewire:navigating', () => {
            this.state.isNavigating = true;
            // Не закрываем WebSocket при SPA навигации
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                this.preserveConnection = true;
            }
        });

        // После навигации - проверяем WebSocket
        document.addEventListener('livewire:navigated', () => {
            this.state.isNavigating = false;
            
            // Проверяем, что WebSocket всё ещё подключен
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {

            } else if (this.ws && this.ws.readyState === WebSocket.CONNECTING) {
            } else {
                console.warn('⚠️ WebSocket lost, reconnecting...');
                this.initWebSocket();
            }

            this.initElements();
            this.initEventListeners();

            // Проверяем состояние сообщений
            if (this.elements.messagesContainer) {
                const messageCount = this.elements.messagesContainer.childElementCount;
                
                if (messageCount > 0) {
                } else if (this.state.currentChannel) {
                    this.loadMessages(this.state.currentChannel);
                }
            }
        });
    }

    /**
     * Инициализация элементов DOM
     */
    initElements() {
        // Если элементы уже закэшированы и существуют - не ищем заново
        if (this._elementsCache && this._elementsCache.messagesContainer?.isConnected) {
            this.elements = this._elementsCache;
            
            // Восстанавливаем онлайн счёт
            if (this.state.lastOnlineCount > 0 && this.elements.onlineCountElement) {
                this.elements.onlineCountElement.textContent = `Online: ${this.state.lastOnlineCount}`;
            }
            return;
        }
        
        this.elements = {
            messagesContainer: document.getElementById('messages'),
            messageInput: document.getElementById('message-input'),
            sendButton: document.getElementById('send-message'),
            charCounter: document.getElementById('char-counter'),
            emojiButton: document.getElementById('emoji-button'),
            emojiPicker: document.getElementById('emoji-picker'),
            emojiCloseButton: document.querySelector('.emoji-close'),
            scrollToNewButton: document.getElementById('scroll-to-new') || this.createScrollButton(),
            currentChannelInput: document.getElementById('currentChannelInput'),
            onlineCountElement: document.getElementById('online-count'),
            chatSidebar: document.querySelector('#right-sidebar'),
            chatToggleButton: document.querySelector('.chat-toggle-button')
        };

        // Сохраняем кэш
        this._elementsCache = this.elements;

        //  Восстанавливаем онлайн счёт после переинициализации
        if (this.state.lastOnlineCount > 0 && this.elements.onlineCountElement) {
            this.elements.onlineCountElement.textContent = `Online: ${this.state.lastOnlineCount}`;
        }
    }

    /**
     * Инициализация звуков
     */
    initSounds() {
        // Создаём заглушки - загрузка при первом воспроизведении
        this.sounds = {
            notification: null,
            moneyReceived: null,
            rain: null
        };
        this.soundPaths = {
            notification: '/assets/sounds/new_message_tone.mp3',
            moneyReceived: '/assets/sounds/money_tone.mp3',
            rain: '/assets/sounds/raindrop.mp3'
        };
    }

    /**
     * Создание кнопки прокрутки
     */
    createScrollButton() {
        const button = document.createElement('button');
        button.id = 'scroll-to-new';
        button.className = 'absolute bottom-4 left-1/2 -translate-x-1/2 rounded-lg bg-[#2a2f3a] hover:bg-[#323844] px-4 py-2 text-sm font-medium text-white shadow-lg border border-[#ffb300]/20 hover:border-[#ffb300]/40 z-50 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 transform translate-y-2';
        button.innerHTML = `
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#ffb300]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="7 13 12 18 17 13"></polyline>
                    <polyline points="7 6 12 11 17 6"></polyline>
                </svg>
                <span>Новые сообщения</span>
            </div>
        `;
        document.body.appendChild(button);
        return button;
    }

    /**
     * Инициализация WebSocket
     */
    initWebSocket() {
        // Если уже подключены - не переподключаемся
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            return;
        }

        // Если соединение закрывается - ждём
        if (this.ws && this.ws.readyState === WebSocket.CONNECTING) {
            return;
        }

        // Закрываем только если соединение мертво
        if (this.ws && this.ws.readyState === WebSocket.CLOSED) {
            this.ws = null;
        }

        // Экспоненциальный backoff для переподключения
        if (!this._reconnectAttempts) {
            this._reconnectAttempts = 0;
        }

        try {
            this.ws = new WebSocket(this.config.wsUrl);

            this.ws.addEventListener('open', () => {
                this._reconnectAttempts = 0; // Сбрасываем счётчик
            });

            this.ws.addEventListener('message', (event) => {
                this.handleWebSocketMessage(event);
            });

            this.ws.addEventListener('close', () => {
                
                // Переподключаемся только если НЕ в процессе навигации
                if (!this.state.isNavigating) {
                    // Экспоненциальный backoff: 3s, 6s, 12s, max 30s
                    const delay = Math.min(
                        this.config.reconnectDelay * Math.pow(2, this._reconnectAttempts),
                        30000
                    );
                    this._reconnectAttempts++;
                    
                    setTimeout(() => this.initWebSocket(), delay);
                }
            });

            this.ws.addEventListener('error', (error) => {
                console.error('❌ WebSocket ошибка:', error);
            });
        } catch (error) {
            console.error('❌ Ошибка создания WebSocket:', error);
        }
    }

    /**
     * Обработка сообщений WebSocket
     */
    handleWebSocketMessage(event) {
        if (typeof event.data !== 'string') return;

        try {
            const messageData = JSON.parse(event.data);

            switch (messageData.type) {
                case 'onlineCount':
                    this.updateOnlineCount(messageData.count);
                    break;
                case 'deleteMessage':
                    this.removeMessage(messageData.id);
                    break;
                case 'chatMessage':
                    this.handleChatMessage(messageData);
                    break;
                default:
                    if (this.shouldDisplayMessage(messageData)) {
                        this.appendMessage(messageData);
                        this.checkMention(messageData);
                    }
            }
        } catch (error) {
            console.error('Ошибка при разборе WebSocket сообщения:', error);
        }
    }

    /**
     * Обработка чат-сообщения
     */
    handleChatMessage(messageData) {
        if (messageData.is_winning_share) {
            this.appendWinningShareMessage(messageData);
        } else {
            this.appendMessage(messageData);
        }
    }

    /**
     * Проверка, должно ли сообщение отображаться
     */
    shouldDisplayMessage(messageData) {
        return (messageData.room && messageData.room === this.state.currentChannel) ||
               messageData.type === 'rain';
    }

    /**
     * Проверка упоминания пользователя
     */
    checkMention(messageData) {
        if (!this.state.currentUser?.username || !messageData.message) return;

        // Ищем упоминание текущего пользователя (с учетом пробелов в имени)
        const mentionRegex = new RegExp(`@${this.state.currentUser.username.replace(/\s+/g, '\\s+')}(?=[,.\n\\s]|$)`, 'i');

        if (mentionRegex.test(messageData.message)) {
            this.playSound('notification');
        }
    }

    /**
     * Обновление счетчика онлайн
     */
    updateOnlineCount(count) {
        //  Сохраняем последнее значение
        this.state.lastOnlineCount = count;
        
        if (this.elements.onlineCountElement) {
            this.elements.onlineCountElement.textContent = `Online: ${count}`;
        }
    }

    /**
     * Удаление сообщения
     */
    removeMessage(messageId) {
        const messageElement = this.elements.messagesContainer?.querySelector(
            `.message[data-id="${messageId}"]`
        );
        if (messageElement) {
            messageElement.remove();
            this.state.addedMessageIds.delete(messageId);
        }
    }

    /**
     * Инициализация обработчиков событий
     */
    initEventListeners() {
        // Отправка сообщения
        const sendBtn = this.elements.sendButton;
        if (sendBtn) {
            sendBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        const msgInput = this.elements.messageInput;
        if (msgInput) {
            msgInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Счетчик символов
            msgInput.addEventListener('input', () => {
                this.updateCharCounter();
            });
        }

        // Эмодзи пикер
        const emojiBtn = this.elements.emojiButton;
        if (emojiBtn) {
            emojiBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleEmojiPicker();
            });
        }

        const emojiClose = this.elements.emojiCloseButton;
        if (emojiClose) {
            emojiClose.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeEmojiPicker();
            });
        }

        // Стикеры - отправка сразу после выбора
        setTimeout(() => {
            document.querySelectorAll('.chat-emoji-img').forEach(emoji => {
                emoji.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const emojiText = e.target.alt;
                    this.sendEmojiMessage(emojiText);
                });
            });
        }, 500);

        // Закрытие эмодзи при клике вне
        document.addEventListener('click', (e) => {
            if (this.elements.emojiPicker &&
                !this.elements.emojiPicker.contains(e.target) &&
                !this.elements.emojiButton?.contains(e.target)) {
                this.closeEmojiPicker();
            }
        });

        // Прокрутка
        if (this.elements.messagesContainer) {
            const scrollHandler = this.throttle(() => {
                this.updateScrollButtonVisibility();
            }, 150);
            this.elements.messagesContainer.addEventListener('scroll', scrollHandler, {
                passive: true // Не блокирует рендеринг
            });
        }

        if (this.elements.scrollToNewButton) {
            this.elements.scrollToNewButton.addEventListener('click', () => {
                // Плавная прокрутка при клике на кнопку
                this.scrollToBottom(true);
            });
        }

        // Livewire события
        document.addEventListener('livewire:init', () => this.initLivewireListeners());
    }

    /**
     * Отправка стикера
     */
    async sendEmojiMessage(emojiText) {
        if (!emojiText) return;

        // Закрываем пикер
        this.closeEmojiPicker();

        try {
            const filteredMessage = await this.filterMessage(emojiText);

            const response = await fetch('/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    message: filteredMessage,
                    channel: this.state.currentChannel
                })
            });

            const data = await response.json();

            if (response.ok) {
                const messageData = {
                    id: data.id,
                    user_id: data.user_id,
                    username: data.username,
                    message: data.message,
                    avatar: data.avatar,
                    rank: data.rank,
                    rank_picture: data.rank_picture,
                    is_moder: data.is_moder,
                    room: data.room
                };

                if (!this.state.addedMessageIds.has(messageData.id)) {
                    this.appendMessage(messageData);
                    this.state.addedMessageIds.add(messageData.id);
                    this.sendWebSocketMessage(messageData);
                }
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка при отправке стикера:', error);
            this.showNotification('Ошибка при отправке стикера', 'error');
        }
    }

    /**
     * Инициализация Livewire слушателей
     */
    initLivewireListeners() {
        if (typeof Livewire === 'undefined') return;

        Livewire.on('channel-switched', (event) => {
            const channel = event.channel;
            if (channel && this.elements.currentChannelInput) {
                this.elements.currentChannelInput.value = channel;
                this.state.currentChannel = channel;
                this.loadMessages(channel);
            }
        });
    }

    /**
     * Инициализация текущего пользователя
     */
    initCurrentUser() {
        if (typeof isAuthenticated !== 'undefined' && isAuthenticated) {
            this.state.currentUser = {
                id: currentUserId,
                username: currentUserUsername,
                currency: currentUserCurrency
            };
        }

        // Используем Alpine.store если доступен, иначе fallback на window.chatEmojis
        if (typeof Alpine !== 'undefined' && Alpine.store && Alpine.store('chat')) {
            this.state.emojiObj = Alpine.store('chat').emojis;
        } else if (typeof chatEmojis !== 'undefined') {
            this.state.emojiObj = chatEmojis;
        } else if (typeof window.chatEmojis !== 'undefined') {
            this.state.emojiObj = window.chatEmojis;
        }
    }

    /**
     * Загрузка начальных сообщений
     */
    loadInitialMessages() {
        //  Устанавливаем канал: сначала пробуем из input, иначе 'global' по умолчанию
        this.state.currentChannel = this.elements.currentChannelInput?.value || 'global';
        
        this.loadMessages(this.state.currentChannel);
    }

    /**
     * Загрузка сообщений
     */
    async loadMessages(channel) {
        try {
            const response = await fetch(`/messages?channel=${encodeURIComponent(channel)}`);
            const messages = await response.json();

            //  ПРОФЕССИОНАЛЬНО: Очищаем только если нужно
            this.elements.messagesContainer.innerHTML = '';
            this.state.addedMessageIds.clear();

            // Добавляем сообщения
            messages.reverse().forEach(messageData => {
                this.appendMessage(messageData);
                this.state.addedMessageIds.add(messageData.id);
            });

            // Мгновенная прокрутка при загрузке
            this.scrollToBottom(false);
            this.updateScrollButtonVisibility();
        } catch (error) {
            console.error('Ошибка при получении сообщений:', error);
            this.showNotification('Ошибка при загрузке сообщений', 'error');
        }
    }

    /**
     * Отправка сообщения
     */
    async sendMessage() {
        const message = this.elements.messageInput?.value?.trim();
        if (!message) return;

        // Предотвращаем множественную отправку
        if (this._isSending) return;
        this._isSending = true;

        try {
            const filteredMessage = await this.filterMessage(message);

            const response = await fetch('/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    message: filteredMessage,
                    channel: this.state.currentChannel
                })
            });

            const data = await response.json();

            if (response.ok) {
                this.clearMessageInput();

                const messageData = {
                    id: data.id,
                    user_id: data.user_id,
                    username: data.username,
                    message: data.message,
                    avatar: data.avatar,
                    rank: data.rank,
                    rank_picture: data.rank_picture,
                    is_moder: data.is_moder,
                    room: data.room
                };

                if (!this.state.addedMessageIds.has(messageData.id)) {
                    this.appendMessage(messageData);
                    this.state.addedMessageIds.add(messageData.id);
                    this.sendWebSocketMessage(messageData);
                }
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка при отправке сообщения:', error);
            this.showNotification('Ошибка при отправке сообщения', 'error');
        } finally {
            // Разблокируем отправку через 500ms
            setTimeout(() => {
                this._isSending = false;
            }, 500);
        }
    }

    /**
     * Фильтрация сообщения
     */
    async filterMessage(message) {
        try {
            const response = await fetch('/filter-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            if (data.message === 'Message is valid') {
                return data.filteredMessage;
            }

            throw new Error('Ошибка при фильтрации сообщения');
        } catch (error) {
            console.error('Ошибка при фильтрации:', error);
            throw error;
        }
    }

    /**
     * Добавление сообщения в чат
     */
    appendMessage(messageData, forceScroll = false) {
        if (!this.elements.messagesContainer) {
            console.warn('⚠️ Messages container not found, reinitializing elements...');
            this.initElements();
            
            // Если всё ещё нет - выходим
            if (!this.elements.messagesContainer) {
                console.error('❌ Failed to find messages container!');
                return;
            }
        }

        const wasAtBottom = this.isNearBottom();
        const isSelfMessage = messageData.user_id === this.state.currentUser?.id;

        // Удаляем старые сообщения ПЕРЕД добавлением нового
        if (this.elements.messagesContainer.childElementCount >= this.config.maxMessages) {
            const firstChild = this.elements.messagesContainer.firstElementChild;
            if (firstChild) {
                firstChild.remove();
            }
        }

        // Используем DocumentFragment для батчинга DOM операций
        const fragment = document.createDocumentFragment();
        const messageElement = this.createMessageElement(messageData);
        fragment.appendChild(messageElement);
        
        // Одна операция вместо множественных appendChild
        this.elements.messagesContainer.appendChild(fragment);

        // Если это сообщение от текущего пользователя или пользователь был внизу - скроллим
        if (isSelfMessage || forceScroll || wasAtBottom) {
            // requestAnimationFrame для плавности
            requestAnimationFrame(() => {
                this.scrollToBottom(false);
            });
        } else {
            // Если пользователь не внизу и это не его сообщение, показываем кнопку
            this.updateScrollButtonVisibility();
        }
    }

    /**
     * Создание элемента сообщения
     */
    createMessageElement(messageData) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message chat-message-appear';
        messageElement.dataset.id = messageData.id;

        const isSelfMessage = messageData.user_id === this.state.currentUser?.id;
        if (isSelfMessage) {
            messageElement.classList.add('self');
        }

        // Контейнер отправителя
        const senderElement = this.createSenderElement(messageData);
        messageElement.appendChild(senderElement);

        // Кнопки действий
        if (!isSelfMessage) {
            const replyButton = this.createReplyButton(messageData);
            messageElement.appendChild(replyButton);
        }

        if ((typeof isModerator !== 'undefined' && isModerator) || isSelfMessage) {
            const deleteButton = this.createDeleteButton(messageData.id);
            messageElement.appendChild(deleteButton);
        }

        return messageElement;
    }

    /**
     * Создание элемента отправителя
     */
    createSenderElement(messageData) {
        const senderElement = document.createElement('span');
        senderElement.className = 'sender';

        // Убираем аватар и оставляем только иконку ранга

        // Информация о пользователе
        const userInfo = document.createElement('div');
        userInfo.className = 'user-info';

        const userLine = document.createElement('div');
        userLine.className = 'user-line';

        // Имя пользователя (иконка ранга будет перед именем)
        const usernameSpan = document.createElement('span');
        usernameSpan.className = 'username';
        usernameSpan.textContent = messageData.username || 'Unknown';
        usernameSpan.addEventListener('click', () => this.openUserInfo(messageData.user_id));
            // Ранг (иконка 18x18 сразу перед именем)
            if (messageData.rank && messageData.rank_picture) {
                const rankImgInline = document.createElement('img');
                rankImgInline.src = messageData.rank_picture;
                rankImgInline.alt = messageData.rank;
                rankImgInline.style.width = '18px';
                rankImgInline.style.height = '18px';
                rankImgInline.style.marginRight = '6px';
                rankImgInline.style.verticalAlign = 'middle';
                rankImgInline.style.display = 'inline-block';
                userLine.appendChild(rankImgInline);
            }
        userLine.appendChild(usernameSpan);

        // Время сообщения удалено по требованию — не отображаем

        // Бейдж модератора
        if (messageData.is_moder) {
            const modBadge = document.createElement('span');
            modBadge.className = 'mod-badge';
            modBadge.innerHTML = '🛡️';
            userLine.appendChild(modBadge);
        }

        userInfo.appendChild(userLine);

        // Текст сообщения
        const messageTextDiv = document.createElement('div');
        messageTextDiv.className = 'message-content';
        messageTextDiv.innerHTML = this.processMessageText(messageData.message);
        userInfo.appendChild(messageTextDiv);

        senderElement.appendChild(userInfo);

        // Отдельный контейнер ранга убран, иконка добавлена перед именем

        return senderElement;
    }

    /**
     * Обработка текста сообщения
     */
    processMessageText(message) {
        // Кэшируем регулярные выражения
        if (!this._cachedRegex) {
            this._cachedRegex = {
                mention: /@([^,.\n]+?)(?=[,.\n]|$)/g,
                winning: /#(\d+)/g
            };
        }
        
        let processed = message;

        // Замена упоминаний - улучшенная версия для имен с пробелами
        // Ищем паттерн: @ + любые символы до запятой, точки или конца строки
        processed = processed.replace(this._cachedRegex.mention, (match) => {
            const username = match.substring(1).trim(); // Убираем @ и пробелы

            if (this.state.currentUser?.username &&
                username.toLowerCase() === this.state.currentUser.username.toLowerCase()) {
                return `<span class="mention-current-user">@${username}</span>`;
            }
            return `<span class="mention-other-user">@${username}</span>`;
        });

        // Замена ссылок на выигрыши
        processed = processed.replace(this._cachedRegex.winning, (match, winId) => {
            return `<a href="#" onclick="openWinningModal(${winId}); return false;" class="winning-link">${match}</a>`;
        });

        // Замена эмодзи
        processed = this.replaceEmojis(processed);

        return processed;
    }

    /**
     * Замена эмодзи
     */
    replaceEmojis(text) {
        if (typeof this.state.emojiObj !== 'object' || !this.state.emojiObj) {
            return text;
        }

        let result = text;
        Object.keys(this.state.emojiObj).forEach(key => {
            const re = new RegExp(key, 'gi');
            result = result.replace(re,
                `<img draggable="false" class="emoji-in-message" src="/assets/images/emoj/${this.state.emojiObj[key]}" alt="${key}">`
            );
        });

        return result;
    }

    /**
     * Создание кнопки ответа
     */
    createReplyButton(messageData) {
        const button = document.createElement('button');
        button.className = 'reply-button';
        button.innerHTML = '💬';
        button.addEventListener('click', () => {
            // Если в имени есть пробелы, не добавляем запятую сразу после имени
            const username = messageData.username;
            this.elements.messageInput.value = `@${username}, `;
            this.elements.messageInput.focus();
        });
        return button;
    }

    /**
     * Создание кнопки удаления
     */
    createDeleteButton(messageId) {
        const button = document.createElement('button');
        button.className = 'delete-button';
        button.innerHTML = '🗑️';
        button.addEventListener('click', () => this.deleteMessage(messageId));
        return button;
    }

    /**
     * Добавление сообщения о выигрыше
     */
    appendWinningShareMessage(messageData) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.dataset.id = messageData.id || 'undefined';

        if (messageData.user_id === this.state.currentUser?.id) {
            messageElement.classList.add('self');
        }

        const senderElement = document.createElement('span');
        senderElement.classList.add('sender');
        // Без аватара, только иконка ранга перед именем
            const rankHtml = (messageData.rank && messageData.rank_picture)
                ? `<img src="${messageData.rank_picture}" alt="${messageData.rank}" style="width:18px;height:18px;margin-right:6px;vertical-align:middle;display:inline-block;">`
                : '';
        senderElement.innerHTML = `
            ${rankHtml}
            <span class="username">${messageData.username}</span>
        `;

        const textElement = document.createElement('span');
        const messageParts = messageData.message.split(/(#\d+)/);
        let htmlMessage = '';
        messageParts.forEach(part => {
            if (part.startsWith('#')) {
                const winningId = part.slice(1);
                htmlMessage += `<a href="#" onclick="openWinningModal(${winningId}); return false;" style="color: #00a8ff; font-weight: bold;">${part}</a>`;
            } else {
                htmlMessage += part;
            }
        });
        textElement.innerHTML = htmlMessage;

        messageElement.appendChild(senderElement);
        messageElement.appendChild(textElement);

        if (messageData.user_id === this.state.currentUser?.id) {
            const deleteButton = this.createDeleteButton(messageData.id);
            messageElement.appendChild(deleteButton);
        }

        this.elements.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    /**
     * Удаление сообщения
     */
    async deleteMessage(messageId) {
        try {
            const response = await fetch(`/delete-message/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            if (response.ok) {
                this.showNotification('Сообщение удалено', 'success');

                const deleteMessageData = {
                    type: 'deleteMessage',
                    id: messageId
                };
                this.sendWebSocketMessage(deleteMessageData);
            } else {
                const data = await response.json();
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка при удалении сообщения:', error);
            this.showNotification('Ошибка при удалении сообщения', 'error');
        }
    }

    /**
     * Открытие информации о пользователе
     */
    openUserInfo(userId) {
        window.dispatchEvent(new CustomEvent('open-user-info', {
            detail: { userId }
        }));
    }

    /**
     * Отправка сообщения через WebSocket
     */
    sendWebSocketMessage(data) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
        }
    }

    /**
     * Управление эмодзи пикером
     */
    toggleEmojiPicker() {
        if (!this.elements.emojiPicker) return;
        const isVisible = this.elements.emojiPicker.style.display === 'block';
        this.elements.emojiPicker.style.display = isVisible ? 'none' : 'block';
    }

    closeEmojiPicker() {
        if (this.elements.emojiPicker) {
            this.elements.emojiPicker.style.display = 'none';
        }
    }

    /**
     * Управление прокруткой
     */
    isNearBottom() {
        if (!this.elements.messagesContainer) return true;
        
        const container = this.elements.messagesContainer;
        const scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
        
        return scrollBottom < this.config.scrollThreshold;
    }

    scrollToBottom(smooth = true) {
        if (!this.elements.messagesContainer) return;
        
        if (smooth) {
            this.elements.messagesContainer.scrollTo({
                top: this.elements.messagesContainer.scrollHeight,
                behavior: 'smooth'
            });
            // Обновляем видимость кнопки после анимации
            setTimeout(() => this.updateScrollButtonVisibility(), 300);
        } else {
            requestAnimationFrame(() => {
                this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
                setTimeout(() => this.updateScrollButtonVisibility(), 50);
            });
        }
    }

    updateScrollButtonVisibility() {
        if (!this.elements.scrollToNewButton) return;

        const isAtBottom = this.isNearBottom();
        
        if (isAtBottom) {
            // Скрываем кнопку с анимацией
            this.elements.scrollToNewButton.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
            this.elements.scrollToNewButton.classList.add('opacity-0', 'translate-y-2', 'pointer-events-none');
            this.state.isScrolledToBottom = true;
        } else {
            // Показываем кнопку с анимацией
            this.elements.scrollToNewButton.classList.remove('opacity-0', 'translate-y-2', 'pointer-events-none');
            this.elements.scrollToNewButton.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
            this.state.isScrolledToBottom = false;
        }
    }

    /**
     * Обновление счетчика символов
     */
    updateCharCounter() {
        if (!this.elements.charCounter || !this.elements.messageInput) return;

        const remaining = this.config.messageLimit - this.countCharacters(this.elements.messageInput.value);
        this.elements.charCounter.textContent = remaining;
    }

    /**
     * Подсчет символов с поддержкой Unicode
     */
    countCharacters(str) {
        const segmenter = new Intl.Segmenter('en', { granularity: 'grapheme' });
        const graphemes = [...segmenter.segment(str)];
        return graphemes.length;
    }

    /**
     * Очистка поля ввода
     */
    clearMessageInput() {
        if (this.elements.messageInput) {
            this.elements.messageInput.value = '';
            this.updateCharCounter();
        }
        this.closeEmojiPicker();
    }

    /**
     * Инициализация состояния чата
     */
    initChatState() {
        const isMobile = window.innerWidth < 768;

        // Не восстанавливаем состояние здесь - это делает Alpine.js
        // Не добавляем обработчик для кнопки чата - Alpine.js управляет этим

        // Обработчики для кнопок закрытия
        this.setupChatCloseButtons();
        
        // Инициализация видимости кнопки "новые сообщения"
        if (this.elements.scrollToNewButton) {
            this.updateScrollButtonVisibility();
        }
    }

    /**
     * Переключение чата
     */
    toggleChat() {
        // Проверяем состояние Alpine.js
        const bodyElement = document.querySelector('body[x-data]');
        let isChatOpen = false;
        
        if (bodyElement && window.Alpine) {
            try {
                const alpineData = window.Alpine.$data(bodyElement);
                isChatOpen = alpineData && alpineData.chatOpen;
            } catch (e) {
                console.warn('Cannot read Alpine.js chat state:', e);
            }
        }
        
        if (isChatOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    /**
     * Открытие чата
     */
     openChat() {
         // Обновляем Alpine.js состояние - Alpine управляет CSS
         this.updateAlpineState(true);

         // Настраиваем кнопки закрытия после открытия
         setTimeout(() => {
             this.setupChatCloseButtons();
         }, 100);

         // Сохраняем состояние только на ПК
         if (window.innerWidth >= 768) {
             localStorage.setItem('chatOpen', 'true');
         }
     }

    /**
     * Закрытие чата
     */
    closeChat() {
        // Обновляем Alpine.js состояние - Alpine управляет CSS
        this.updateAlpineState(false);

        // Сохраняем состояние только на ПК
        if (window.innerWidth >= 768) {
            localStorage.setItem('chatOpen', 'false');
        }
    }

    /**
     * Настройка кнопок закрытия чата
     */
     setupChatCloseButtons() {
         // Находим кнопку закрытия по более надежному селектору
         const closeButton = document.querySelector('#right-sidebar button[onclick*="closeChat"]');

         if (closeButton && !closeButton._hasCloseListener) {
             closeButton._hasCloseListener = true;

             // Удаляем старый обработчик onclick и добавляем новый
             closeButton.removeAttribute('onclick');
             closeButton.addEventListener('click', (e) => {
                 e.preventDefault();
                 e.stopPropagation();
                 this.closeChat();
             });
         }
     }
    /**
     * Воспроизведение звука
     */
    playSound(soundName) {
        // Ленивая загрузка звука
        if (!this.sounds[soundName] && this.soundPaths[soundName]) {
            this.sounds[soundName] = new Audio(this.soundPaths[soundName]);
            this.sounds[soundName].preload = 'none'; // Не загружаем заранее
        }
        
        if (this.sounds[soundName]) {
            this.sounds[soundName].play().catch(error => {
                console.error(`Ошибка при воспроизведении звука ${soundName}:`, error);
            });
        }
    }

    /**
     * Показ уведомления
     */
    showNotification(text, type = 'info') {
        if (typeof Noty === 'undefined') {
            return;
        }

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };

        new Noty({
            text: `<div class="noty-content-container"><i class="fas ${icons[type] || icons.info}"></i><span>${text}</span></div>`,
            type: type,
            theme: 'premium',
            timeout: 3000,
            progressBar: true,
            closeWith: ['click', 'button']
        }).show();
    }

    /**
     * Получение CSRF токена
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Debounce функция
     */
    debounce(func, wait) {
        let timeout;
        return (...args) => {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Throttle функция (более эффективна для scroll)
     */
    throttle(func, wait) {
        let timeout = null;
        let previous = 0;
        
        return (...args) => {
            const now = Date.now();
            const remaining = wait - (now - previous);
            
            if (remaining <= 0 || remaining > wait) {
                if (timeout) {
                    clearTimeout(timeout);
                    timeout = null;
                }
                previous = now;
                func.apply(this, args);
            } else if (!timeout) {
                timeout = setTimeout(() => {
                    previous = Date.now();
                    timeout = null;
                    func.apply(this, args);
                }, remaining);
            }
        };
    }

    /**
     * Обновление состояния Alpine.js
     */
    updateAlpineState(isOpen) {
        // Находим элемент body с Alpine.js данными
        const bodyElement = document.querySelector('body[x-data]');
        if (bodyElement && window.Alpine) {
            try {
                const alpineData = window.Alpine.$data(bodyElement);
                if (alpineData && typeof alpineData.chatOpen !== 'undefined') {
                    alpineData.chatOpen = isOpen;
                }
            } catch (e) {
                console.warn('Cannot update Alpine.js chat state:', e);
            }
        }
    }
    }; //  Закрытие class ChatSystem
} //  Закрытие if (typeof window.ChatSystem === 'undefined')

//  Глобальные функции для обратной совместимости (защита от повторного объявления)
if (typeof window.openWinningModal === 'undefined') {
    window.openWinningModal = function(winningId) {
    fetch(`/winning-info/${winningId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('winning-info-modal');
            if (modal) {
                document.getElementById('winning-id').textContent = data.id;
                document.getElementById('bet-amount').textContent = `${data.bet_amount} ${data.currency}`;
                document.getElementById('win-amount').textContent = `${data.win_amount} ${data.currency}`;
                document.getElementById('winning-date').textContent = data.date;
                document.getElementById('game-name').textContent = data.game;
                document.getElementById('username').textContent = data.username || 'N/A';
                document.getElementById('coefficient').textContent = data.coefficient || 'N/A';

                const playButton = document.getElementById('play-game-button');
                if (playButton) {
                    playButton.href = `/slots/play/${data.game}`;
                }

                modal.style.display = 'block';
                document.getElementById('overlay').style.display = 'block';

                const closeButton = modal.querySelector('.close-button');
                if (closeButton) {
                    closeButton.onclick = function() {
                        modal.style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    };
                }
            }
        })
        .catch(error => {
            console.error('Ошибка при получении информации о выигрыше:', error);
        });
    };
} //  Закрытие if для window.openWinningModal

if (typeof window.shareWinning === 'undefined') {
    window.shareWinning = function(winningId) {
    fetch('/share-winning', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ winning_id: winningId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message && window.chatSystem?.ws?.readyState === WebSocket.OPEN) {
            const notificationData = {
                type: 'chatMessage',
                user_id: data.user_id,
                username: data.username,
                message: data.message,
                is_winning_share: data.is_winning_share,
                winning_id: data.winning_id,
                avatar: data.avatar,
                rank: data.rank,
                rank_picture: data.rank_picture,
                is_moder: data.is_moder || false
            };

            window.chatSystem.ws.send(JSON.stringify(notificationData));
        }
    })
    .catch(error => {
        console.error('Ошибка при отправке выигрыша в чат:', error);
    });
    };
} //  Закрытие if для window.shareWinning

//  Инициализация системы (singleton pattern - создаём только один раз)
if (!window.chatSystem) {
    window.chatSystem = new window.ChatSystem();
}

// Глобальные функции для обратной совместимости
if (typeof window.closeChat === 'undefined') {
    window.closeChat = function() {
        if (window.chatSystem) {
            window.chatSystem.closeChat();
        }
    };
}

if (typeof window.openChat === 'undefined') {
    window.openChat = function() {
        if (window.chatSystem) {
            window.chatSystem.openChat();
        }
    };
}
