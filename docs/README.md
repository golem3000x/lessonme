# 📚 LessonMe Documentation

AI-powered language learning platform with personalized lesson generation.

## Documentation Structure

### 🏗️ Architecture
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Application architecture overview
- [DATABASE.md](./DATABASE.md) - Database schema (ER diagram, tables)
- [API_SERVICES.md](./API_SERVICES.md) - Services API and DTOs
- [CONFIGURATION.md](./CONFIGURATION.md) - Configuration and environment variables

### 📖 Features
- [GRADING_SYSTEM_IMPLEMENTATION.md](./GRADING_SYSTEM_IMPLEMENTATION.md) - Grading and progress tracking system
- [STUDENT_DASHBOARD_SUMMARY.md](./STUDENT_DASHBOARD_SUMMARY.md) - Student dashboard
- [DEMO_GUEST_SYSTEM.md](./DEMO_GUEST_SYSTEM.md) - Demo guest system
- [LESSON_UPDATE_SUMMARY.md](./LESSON_UPDATE_SUMMARY.md) - Lesson updates
- [LANGUAGE_SELECTION_FEATURE.md](./LANGUAGE_SELECTION_FEATURE.md) - Language selection
- [PASSWORD_RESET_SETUP.md](./PASSWORD_RESET_SETUP.md) - Password reset

### 💬 Chat
- [CHAT_README.md](./CHAT_README.md) - User chat
- [ADMIN_CHAT_README.md](./ADMIN_CHAT_README.md) - Admin chat

### 🔒 Security
- [CHAT_SECURITY.md](./CHAT_SECURITY.md) - Chat and WebSocket security

### 🔧 Technical
- [WEBSOCKET_MIGRATION.md](./WEBSOCKET_MIGRATION.md) - WebSocket migration (Reverb)
- [AUDIO_FILES_DELETION.md](./AUDIO_FILES_DELETION.md) - Audio files management
- [TROUBLESHOOTING.md](./TROUBLESHOOTING.md) - Troubleshooting guide

### 🧪 Testing
- [TESTING_DEMO_GUESTS.md](./TESTING_DEMO_GUESTS.md) - Demo guests testing

### 🎨 UI Components
- [MODAL_COMPONENTS.md](./MODAL_COMPONENTS.md) - Modal windows
- [SUPPORT_PAGE_QR_CODE.md](./SUPPORT_PAGE_QR_CODE.md) - Support page QR code

### 📋 Checklists
- [CHECKLIST.md](./CHECKLIST.md) - Lesson structure update checklist

---

## Quick Start

### Requirements
- PHP 8.1+
- Laravel 11
- PostgreSQL with pgvector
- Node.js / NPM
- Docker (optional)

### Installation
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

### Run with Docker
```bash
./vendor/bin/sail up -d
```

---

## Main Modules

| Module | Description | Path |
|--------|-------------|------|
| **Lessons** | Lesson generation and management | `app/Services/Lesson/` |
| **Courses** | Courses and assignments | `app/Models/Course/` |
| **Grading** | Grading and progress | `app/Services/Lesson/LessonGradingService.php` |
| **Audio** | TTS audio generation | `app/Services/Lesson/LessonAudioService.php` |
| **Chat** | WebSocket chat | `app/Livewire/ChatPanel.php` |

---

## Contact

If you encounter any issues, please create an issue or contact the administrator.
