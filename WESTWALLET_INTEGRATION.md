# WestWallet Crypto Integration

## Описание

Полная интеграция WestWallet API для приёма криптовалютных платежей с персональными кошельками для каждого пользователя.

## Возможности

✅ **Персональные крипто-кошельки** - каждый пользователь получает уникальный адрес для каждой криптовалюты  
✅ **Автоматическое зачисление** - средства зачисляются автоматически через IPN уведомления  
✅ **QR-коды** - удобное отображение QR-кода для быстрой оплаты  
✅ **Поддержка нескольких криптовалют**: BTC, ETH, USDT, TRX, LTC, XRP  
✅ **Красивый UI** - интегрировано в модальное окно пополнения с современным дизайном  
✅ **Production-ready** - чистый, оптимизированный код с логированием  

---

## Установка

### 1. Запуск миграций

```bash
php artisan migrate
```

Это создаст таблицу `user_crypto_wallets` для хранения адресов.

### 2. Добавление Payment Handlers

Запустите сидер для создания платёжных обработчиков:

```bash
php artisan db:seed --class=WestWalletSeeder
```

### 3. Настройка .env

Добавьте следующие переменные в файл `.env`:

```env
# WestWallet API Configuration
WESTWALLET_API_URL=https://api.westwallet.io
WESTWALLET_PUBLIC_KEY=your_public_key_here
WESTWALLET_PRIVATE_KEY=your_private_key_here
WESTWALLET_TRUSTED_IPS=5.188.51.47
WESTWALLET_SKIP_IP_CHECK=false
```

**Получение ключей:**
1. Зарегистрируйтесь на https://westwallet.io
2. Перейдите в настройки профиля
3. Скопируйте Public Key и Private Key

### 4. Настройка IPN URL

В личном кабинете WestWallet укажите IPN URL:

```
https://your-domain.com/pay/westwallet
```

⚠️ **Важно:** WestWallet отправляет уведомления только с IP `5.188.51.47`. Убедитесь, что этот IP не заблокирован на вашем сервере.

---

## Использование

### Для пользователей

1. Открыть модальное окно пополнения
2. Выбрать любой крипто-обработчик (BTC, ETH, USDT и т.д.)
3. Система автоматически покажет:
   - QR-код адреса
   - Адрес кошелька для копирования
   - Dest Tag (если требуется, например для XRP)
4. Отправить криптовалюту на указанный адрес
5. После подтверждения в блокчейне средства автоматически зачислятся

### Как работает

1. **Первое пополнение:** При выборе криптовалюты система генерирует уникальный адрес через WestWallet API
2. **Сохранение:** Адрес сохраняется в БД и привязывается к пользователю
3. **Последующие пополнения:** Показывается сохраненный адрес (один адрес на валюту)
4. **IPN уведомления:** При получении платежа WestWallet отправляет уведомление на `/pay/westwallet`
5. **Автозачисление:** Система находит пользователя по label и зачисляет средства

---

## Структура файлов

### Миграции
- `database/migrations/2024_12_02_000001_create_user_crypto_wallets_table.php`

### Модели
- `app/Models/UserCryptoWallet.php` - модель крипто-кошельков

### Сервисы
- `app/Services/WestWalletService.php` - работа с WestWallet API

### Контроллеры
- `app/Http/Controllers/WestWalletCallbackController.php` - обработка IPN
- `app/Http/Controllers/CashController.php` - метод `getCryptoAddress()`

### Views
- `resources/views/components/modals/cash.blade.php` - обновлено с UI для крипто

### Seeders
- `database/seeders/WestWalletSeeder.php` - создание платёжных обработчиков

### Конфигурация
- `config/payment.php` - добавлен раздел `westwallet`

### Маршруты
- `routes/web.php` - добавлены:
  - `POST /pay/westwallet` - IPN callback
  - `POST /crypto/get-address` - получение адреса

---

## API Endpoints

### Получение крипто-адреса
```
POST /crypto/get-address
Authorization: Bearer token (auth required)

Request:
{
  "currency": "BTC",
  "network": "TRC20" // опционально
}

Response:
{
  "success": true,
  "data": {
    "address": "1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa",
    "dest_tag": null,
    "currency": "BTC",
    "network": null,
    "qr_data": "1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa",
    "existing": false
  }
}
```

### IPN Callback (WestWallet → ваш сервер)
```
POST /pay/westwallet

Request (form-data):
{
  "label": "user_123_BTC_1733145600",
  "status": "completed",
  "amount": "0.001",
  "currency": "BTC",
  "txid": "...",
  "transaction": "12345",
  ...
}

Response: 200 OK
```

---

## Безопасность

1. **IP Whitelist**: Проверка IP отправителя IPN (только 5.188.51.47)
2. **HMAC Signature**: Все запросы к API подписываются
3. **Уникальные Labels**: Каждый адрес имеет уникальный идентификатор
4. **Проверка дублей**: Система не зачисляет одну транзакцию дважды

---

## Отладка

### Логи

Все операции логируются в `storage/logs/laravel.log`:

```php
// Генерация адреса
Log::info('Crypto wallet created', [...]);

// IPN получен
Log::info('WestWallet IPN Request', [...]);

// Депозит обработан
Log::info('WestWallet Deposit Processed', [...]);

// Ошибки
Log::error('WestWallet API Error', [...]);
```

### Отключение проверки IP (только для разработки)

```env
WESTWALLET_SKIP_IP_CHECK=true
```

---

## Поддерживаемые криптовалюты

| Валюта | Код | Мин. депозит | Особенности |
|--------|-----|--------------|-------------|
| Bitcoin | BTC | 0.0001 | - |
| Ethereum | ETH | 0.001 | - |
| Tether | USDT | 1 | TRC20/ERC20 |
| TRON | TRX | 10 | - |
| Litecoin | LTC | 0.01 | - |
| Ripple | XRP | 1 | Требует Dest Tag |

---

## Troubleshooting

### Адрес не генерируется
- Проверьте API ключи в `.env`
- Проверьте логи: `tail -f storage/logs/laravel.log`
- Убедитесь что WestWallet API доступен

### IPN не приходят
- Проверьте что IPN URL настроен в WestWallet
- Проверьте что IP 5.188.51.47 не заблокирован
- Проверьте логи веб-сервера (nginx/apache)

### Средства не зачисляются
- Проверьте логи обработки IPN
- Убедитесь что транзакция подтверждена в блокчейне
- Проверьте статус в таблице `transactions`

---

## Дальнейшее развитие

- [ ] Добавить вывод криптовалют через WestWallet API
- [ ] Поддержка дополнительных сетей (ERC20, BEP20)
- [ ] История крипто-транзакций в личном кабинете
- [ ] Email уведомления при получении платежа
- [ ] Конвертация между криптовалютами

---

## Поддержка

При возникновении проблем:
1. Проверьте логи: `storage/logs/laravel.log`
2. Проверьте документацию WestWallet: https://api.westwallet.io/
3. Убедитесь что все миграции применены: `php artisan migrate:status`

---

## Лицензия

Этот код является частью проекта Betra и доступен только для использования в рамках проекта.

© 2024 Betra. All rights reserved.
