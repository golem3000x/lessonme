<?php

declare(strict_types=1);

namespace App\Enums;

enum TableNameEnum: string
{
    case USERS = 'users';
    case TOPICS = 'topics';
    case TOPIC_TRANSLATIONS = 'topic_translations';
    case LESSONS = 'lessons';
    case CACHE = 'cache';
    case CACHE_LOCK = 'cache_locks';
    case JOBS = 'jobs';
    case JOB_BATCHES = 'job_batches';
    case FAILED_JOBS = 'failed_jobs';
    case PASSWORD_RESET_TOKENS = 'password_reset_tokens';
    case SESSIONS = 'sessions';
}
