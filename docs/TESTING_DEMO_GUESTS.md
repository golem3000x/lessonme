# Тестирование системы временных гостей

## Как протестировать:

### 1. Откройте демо-страницу в нескольких браузерах/вкладках

```
http://localhost/welcome
```

**Важно**: Используйте разные браузеры или режим инкогнито для имитации разных пользователей.

### 2. Создайте уроки одновременно

В каждом браузере:
- Выберите тему
- Нажмите "Create Demo Lesson"
- Дождитесь создания урока

**Ожидаемый результат**: 
- Каждый гость видит только свой урок
- Уроки не пересекаются между сессиями

### 3. Проверьте базу данных

```bash
# Войдите в контейнер
docker compose exec laravel.test bash

# Подключитесь к MySQL
mysql -u sail -p'password' ai_demo

# Проверьте временных гостей
SELECT id, email, is_temp_guest, temp_guest_expires_at 
FROM users 
WHERE is_temp_guest = true;

# Проверьте их уроки
SELECT l.id, l.lesson_name, u.email 
FROM lessons l 
JOIN users u ON l.user_id = u.id 
WHERE u.is_temp_guest = true;
```

### 4. Тестирование очистки

```bash
# Создайте тестового истекшего гостя
docker compose exec laravel.test php artisan tinker

>>> $user = \App\Models\User::create([
...     'name' => 'Test Expired Guest',
...     'email' => 'test_expired@temp.local',
...     'password' => bcrypt('test'),
...     'email_verified_at' => now(),
...     'native_lang' => 'vi',
...     'learning_lang' => 'en',
...     'role' => 'student',
...     'is_temp_guest' => true,
...     'temp_guest_expires_at' => now()->subHours(1),
... ]);
>>> exit

# Запустите очистку
docker compose exec laravel.test php artisan guests:cleanup --force

# Проверьте что пользователь удален
docker compose exec laravel.test php artisan tinker
>>> \App\Models\User::where('email', 'test_expired@temp.local')->count()
=> 0
```

### 5. Тестирование timeout (2 минуты)

1. Создайте урок как гость
2. Подождите 2 минуты
3. Должна появиться ошибка с кнопкой "Try Again"
4. Нажмите "Try Again" - должно создать новый урок

### 6. Проверьте scheduler

```bash
# Проверьте зарегистрированные задачи
docker compose exec laravel.test php artisan schedule:list

# Должна быть задача:
# guests:cleanup --force  Daily at 03:00  Clean up expired temporary guest users
```

## Что проверять:

✅ Каждая сессия создает своего временного пользователя  
✅ Уроки изолированы между сессиями  
✅ Истекшие пользователи удаляются командой cleanup  
✅ Timeout работает (2 минуты)  
✅ Кнопка retry создает новый урок  
✅ Scheduler настроен на автоматическую очистку  

## Troubleshooting:

### Ошибка при создании урока:
```bash
# Проверьте логи
docker compose exec laravel.test tail -f storage/logs/laravel.log
```

### Пользователи не удаляются:
```bash
# Проверьте is_temp_guest и expires_at
SELECT * FROM users WHERE is_temp_guest = true;

# Запустите cleanup с выводом
docker compose exec laravel.test php artisan guests:cleanup
```

### Queue не обрабатывается:
```bash
# Проверьте queue worker
docker compose exec laravel.test php artisan queue:work --once
```
