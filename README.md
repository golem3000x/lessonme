# 🎓 LessonMe

AI-powered language learning platform with personalized lesson generation.

## ✨ Features

- **AI Lesson Generation** — GPT-4o-mini creates personalized lessons by CEFR level (A1-C2)
- **Interactive Exercises** — gap-fill, matching, true/false, sentence ordering
- **TTS Audio** — vocabulary and exercise audio via FPT.AI
- **Grading System** — automatic checking with progress tracking
- **Courses** — combine lessons into courses and assign to students
- **Teacher-Student** — invitation system and collaborative work
- **Real-time Notifications** — WebSocket via Laravel Reverb
- **Demo Mode** — temporary guests can try without registration

## 🛠 Tech Stack

| Category | Technology |
|----------|------------|
| Backend | Laravel 11, PHP 8.1+ |
| Frontend | Livewire 3.5+, Alpine.js, TailwindCSS, daisyUI |
| Database | PostgreSQL 17 + pgvector |
| AI | OpenAI GPT-4o-mini, text-embedding-3-small |
| TTS | FPT.AI Text-to-Speech |
| WebSocket | Laravel Reverb |
| Queue | Laravel Queue (database driver) |

## 🚀 Installation

### Requirements
- PHP 8.1+
- Composer
- Node.js / NPM
- PostgreSQL with pgvector extension
- Docker (recommended)

### Docker (Laravel Sail)

```bash
# Clone repository
git clone <repo-url>
cd ai-demo

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env

# Start containers
./vendor/bin/sail up -d

# Run migrations and seeders
./vendor/bin/sail artisan migrate --seed

# Build assets
./vendor/bin/sail npm run build
```

### Environment Variables

```env
# OpenAI
OPENAI_API_KEY=sk-...

# FPT.AI TTS
FPT_TTS_API_KEY=...

# WebSocket
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
```

## 📚 Documentation

Full documentation in [`docs/`](./docs/README.md):

- [Architecture](./docs/ARCHITECTURE.md)
- [Database Schema](./docs/DATABASE.md)
- [Services API](./docs/API_SERVICES.md)
- [Configuration](./docs/CONFIGURATION.md)

## 🔧 Commands

```bash
# Start services
./vendor/bin/sail up -d

# WebSocket server
./vendor/bin/sail artisan reverb:start

# Queue worker
./vendor/bin/sail artisan queue:work

# Cleanup temp guests
./vendor/bin/sail artisan app:cleanup-temp-guests

# Run tests
./vendor/bin/sail artisan test
```

## 📁 Project Structure

```
app/
├── Services/Lesson/     # Lesson generation and grading services
├── Models/              # Eloquent models
├── Livewire/            # Livewire components
├── Jobs/                # Background jobs
└── Notifications/       # WebSocket notifications

docs/                    # Documentation
resources/views/         # Blade templates
```

## 📄 License

Proprietary software. All rights reserved.
