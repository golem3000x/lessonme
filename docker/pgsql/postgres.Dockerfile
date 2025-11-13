FROM postgres:17

# Устанавливаем pgvector из официального репозитория PostgreSQL
RUN apt-get update && apt-get install -y \
    postgresql-17-pgvector \
 && rm -rf /var/lib/apt/lists/*