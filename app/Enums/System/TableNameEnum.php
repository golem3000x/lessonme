<?php

declare(strict_types=1);

namespace App\Enums\System;

enum TableNameEnum: string
{
    case USERS = 'users';
    case TOPICS = 'topics';
    case LESSONS = 'lessons';
    case CACHE = 'cache';
    case CACHE_LOCK = 'cache_locks';
    case JOBS = 'jobs';
    case JOB_BATCHES = 'job_batches';
    case FAILED_JOBS = 'failed_jobs';
    case PASSWORD_RESET_TOKENS = 'password_reset_tokens';
    case SESSIONS = 'sessions';
    case TOPIC_TRANSLATIONS = 'topic_translations';
    case KEYWORDS = 'keywords';
    case KEYWORD_TRANSLATIONS = 'keyword_translations';
    case USER_TOPIC_VISIBILITY = 'user_topic_visibility';
    case USER_KEYWORD_VISIBILITY = 'user_keyword_visibility';
    case LESSON_HISTORIES = 'lesson_histories';
    case TEACHER_STUDENT = 'teacher_student';
    case LESSON_ASSIGNMENTS = 'lesson_assignments';
    case LESSON_ATTEMPTS = 'lesson_attempts';
    case LESSON_SCORES = 'lesson_scores';
    case LESSON_AUDIO_FILES = 'lesson_audio_files';
    case CHAT_SESSIONS = 'chat_sessions';
    case MESSAGES = 'messages';
}
