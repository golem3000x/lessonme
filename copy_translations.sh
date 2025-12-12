#!/bin/bash

# Скрипт для копирования файлов перевода из en/ в km/ и ms/
# Script to copy translation files from en/ to km/ and ms/

# Базовая директория с переводами
LANG_DIR="resources/lang"

# Исходная директория (английский)
SOURCE_DIR="$LANG_DIR/en"

# Целевые директории
TARGET_DIRS=("km" "ms")

# Проверяем существование исходной директории
if [ ! -d "$SOURCE_DIR" ]; then
    echo "❌ Error: Source directory $SOURCE_DIR does not exist!"
    exit 1
fi

# Копируем файлы для каждой целевой директории
for target in "${TARGET_DIRS[@]}"; do
    TARGET_PATH="$LANG_DIR/$target"
    
    echo "📁 Processing $target..."
    
    # Создаём директорию если не существует
    mkdir -p "$TARGET_PATH"
    
    # Копируем все .php файлы
    for file in "$SOURCE_DIR"/*.php; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            cp "$file" "$TARGET_PATH/$filename"
            echo "   ✓ Copied $filename"
        fi
    done
    
    echo "✅ Done with $target"
    echo ""
done

echo "🎉 All files copied successfully!"
echo ""
echo "Files in km/:"
ls -la "$LANG_DIR/km/"
echo ""
echo "Files in ms/:"
ls -la "$LANG_DIR/ms/"
