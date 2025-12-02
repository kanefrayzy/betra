# WestWallet Integration - Quick Start Commands

## 📋 Checklist установки

### 1. Применить миграции
```bash
php artisan migrate
```

### 2. Запустить сидер
```bash
php artisan db:seed --class=WestWalletSeeder
```

### 3. Добавить в .env
```bash
# Добавьте эти строки в конец файла .env
cat >> .env << 'EOF'

# WestWallet Configuration
WESTWALLET_API_URL=https://api.westwallet.io
WESTWALLET_PUBLIC_KEY=
WESTWALLET_PRIVATE_KEY=
WESTWALLET_TRUSTED_IPS=5.188.51.47
WESTWALLET_SKIP_IP_CHECK=false
EOF
```

### 4. Очистить кэш
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 5. Проверка установки
```bash
# Проверить что миграция применена
php artisan migrate:status | grep user_crypto_wallets

# Проверить что payment handlers созданы
php artisan tinker
>>> \App\Models\PaymentSystem::where('name', 'WestWallet')->with('handlers')->first()
>>> exit
```

---

## 🔧 Настройка WestWallet

1. **Зарегистрируйтесь:** https://westwallet.io
2. **Получите API ключи:**
   - Перейдите в Profile → Settings
   - Скопируйте Public Key и Private Key
   - Вставьте в `.env`
3. **Настройте IPN URL:**
   - В настройках WestWallet укажите: `https://ваш-домен.com/pay/westwallet`

---

## 🧪 Тестирование

### Проверка генерации адреса (через Postman или curl)
```bash
curl -X POST https://ваш-домен.com/crypto/get-address \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"currency":"BTC"}'
```

### Проверка IPN endpoint
```bash
# Из разрешенного IP (5.188.51.47) или с отключенной проверкой
curl -X POST https://ваш-домен.com/pay/westwallet \
  -d "label=user_1_BTC_123456" \
  -d "status=completed" \
  -d "amount=0.001" \
  -d "currency=BTC"
```

### Просмотр логов в реальном времени
```bash
tail -f storage/logs/laravel.log | grep -i westwallet
```

---

## 📊 SQL Queries для проверки

### Посмотреть все крипто-кошельки
```sql
SELECT 
    u.username,
    ucw.currency,
    ucw.address,
    ucw.total_received,
    ucw.transactions_count,
    ucw.created_at
FROM user_crypto_wallets ucw
JOIN users u ON ucw.user_id = u.id
ORDER BY ucw.created_at DESC;
```

### Посмотреть крипто-депозиты
```sql
SELECT 
    u.username,
    t.amount,
    t.currency_id,
    t.status,
    JSON_EXTRACT(t.context, '$.crypto_currency') as crypto,
    JSON_EXTRACT(t.context, '$.crypto_amount') as crypto_amount,
    t.created_at
FROM transactions t
JOIN users u ON t.user_id = u.id
WHERE JSON_EXTRACT(t.context, '$.payment_system') = 'WestWallet'
ORDER BY t.created_at DESC
LIMIT 20;
```

### Посмотреть WestWallet платёжные обработчики
```sql
SELECT 
    ps.name as system_name,
    ph.name as handler_name,
    ph.currency,
    ph.min_deposit_limit,
    ph.active
FROM payment_handlers ph
JOIN payment_systems ps ON ph.payment_system_id = ps.id
WHERE ps.name = 'WestWallet';
```

---

## 🐛 Debug режим

### Включить подробное логирование
Добавьте в `.env`:
```env
LOG_LEVEL=debug
WESTWALLET_SKIP_IP_CHECK=true  # только для локальной разработки!
```

### Проверить конфигурацию
```bash
php artisan tinker
>>> config('payment.westwallet')
>>> exit
```

---

## 🔄 Откат изменений (если нужно)

### Откатить миграцию
```bash
php artisan migrate:rollback --step=1
```

### Удалить WestWallet handlers
```bash
php artisan tinker
>>> \App\Models\PaymentSystem::where('name', 'WestWallet')->delete();
>>> exit
```

---

## ✅ Готово!

После выполнения всех шагов:
1. Откройте сайт
2. Войдите в аккаунт
3. Откройте модальное окно пополнения
4. Выберите любую криптовалюту (BTC, ETH, USDT и т.д.)
5. Получите QR-код и адрес для пополнения!

---

## 📞 Поддержка

Если что-то не работает:
1. Проверьте логи: `tail -f storage/logs/laravel.log`
2. Проверьте .env файл
3. Убедитесь что все миграции применены
4. Проверьте что WestWallet API ключи корректны
