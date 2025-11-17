# Language Selection Feature

## Описание
Реализован промежуточный шаг выбора родного языка (`native_lang`) перед созданием пользователя в базе данных.

## Изменения

### 1. Новый Livewire компонент: `LanguageSelection`
**Файлы:**
- `app/Livewire/Auth/LanguageSelection.php`
- `resources/views/livewire/auth/language-selection.blade.php`

**Функциональность:**
- Проверяет наличие данных регистрации в сессии
- Отображает селект с доступными языками (English/Vietnamese)
- Устанавливает язык по умолчанию на основе текущей локали
- Автоматически определяет `learning_lang` (противоположный от `native_lang`)
- Создает пользователя только после выбора языка
- Отправляет приветственное уведомление
- Логинит пользователя и редиректит на дашборд

### 2. Обновлен `AuthController`
**Файл:** `app/Http/Controllers/Auth/AuthController.php`

**Изменения в методе `register()`:**
- Убрано создание пользователя сразу после регистрации
- Данные сохраняются в сессию: `name`, `email`, `password`, `timezone`
- Редирект на страницу выбора языка: `route('auth.language')`

### 3. Обновлен `SocialAuthController` (Facebook)
**Файл:** `app/Http/Controllers/Auth/SocialAuthController.php`

**Изменения в методе `handleFacebookCallback()`:**
- Для новых пользователей: сохранение данных в сессию и редирект на выбор языка
- Для существующих пользователей: прямой логин и отправка welcome-уведомления

### 4. Обновлен `ZaloController`
**Файл:** `app/Http/Controllers/Auth/ZaloController.php`

**Изменения в методе `callback()`:**
- Для новых пользователей: сохранение данных в сессию и редирект на выбор языка
- Для существующих пользователей: прямой логин и отправка welcome-уведомления
- Убрано хардкод-значение `native_lang` при создании пользователя

### 5. Роутинг
**Файл:** `routes/web.php`

**Добавлен роут:**
```php
Route::get('/auth/language', LanguageSelection::class)->name('auth.language');
```

### 6. Переводы
**Файлы:**
- `resources/lang/en/auth.php`
- `resources/lang/vi/auth.php`

**Добавлена секция `language_selection`:**
- `title` - заголовок страницы
- `description` - описание
- `native_lang_label` - метка для селекта
- `select_language` - плейсхолдер
- `submit` - текст кнопки

## Процесс регистрации

### Обычная регистрация:
1. Пользователь заполняет форму на `/register`
2. Данные сохраняются в сессию
3. Редирект на `/auth/language`
4. Пользователь выбирает `native_lang`
5. Создается запись в БД с `native_lang` и автоматическим `learning_lang`
6. Логин и редирект на `/dashboard`

### Социальная авторизация (Facebook/Zalo):
1. Callback от социальной сети
2. Проверка существования пользователя
3. **Новый пользователь:**
   - Сохранение данных в сессию
   - Редирект на `/auth/language`
   - Выбор языка
   - Создание пользователя
   - Логин и редирект
4. **Существующий пользователь:**
   - Прямой логин
   - Welcome-уведомление
   - Редирект на `/dashboard`

## Структура данных в сессии

```php
session()->get('registration_data') = [
    'name' => string,
    'email' => string,
    'password' => string, // plain text, хешируется при создании
    'timezone' => string,
    // для социальной авторизации:
    'facebook_id' => string|null,
    'zalo_user_id' => string|null,
    'avatar' => string|null,
    'email_verified_at' => Carbon|null,
]
```

## Логика определения learning_lang

```php
$learning_lang = $native_lang === 'en' ? 'vi' : 'en';
```

## Безопасность

- Проверка наличия данных в сессии перед созданием пользователя
- Валидация выбранного языка через Enum
- Очистка сессии после успешной регистрации
- Хеширование пароля перед сохранением

## Уведомления

Используется `RegistrationNotification` с персонализированным приветствием:
- Определяется время суток на основе timezone
- Выбирается приветствие на выбранном языке
- Отправляется через систему уведомлений Laravel
