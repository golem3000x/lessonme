<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\TableNameEnum;
use App\Enums\System\UserRoleEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property UserRoleEnum $role
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $zalo_user_id
 * @property string|null $avatar
 * @property string $native_lang
 * @property string $learning_lang
 * @property string|null $facebook_id
 * @property string $timezone
 * @property bool $is_temp_guest
 * @property Carbon|null $temp_guest_expires_at
 * @property Carbon|null $first_login_completed_at
 * @property-read Collection<int, LessonAssignment> $assignedLessons
 * @property-read int|null $assigned_lessons_count
 * @property-read Collection<int, ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read string|null $avatar_url
 * @property-read Collection<int, LessonAttempt> $lessonAttempts
 * @property-read int|null $lesson_attempts_count
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 * @property-read Collection<int, LessonAssignment> $myAssignments
 * @property-read int|null $my_assignments_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, LessonScore> $scores
 * @property-read int|null $scores_count
 * @property-read Collection<int, User> $students
 * @property-read int|null $students_count
 * @property-read Collection<int, User> $teachers
 * @property-read int|null $teachers_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereFacebookId($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsTempGuest($value)
 * @method static Builder<static>|User whereLearningLang($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User whereNativeLang($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereTempGuestExpiresAt($value)
 * @method static Builder<static>|User whereTimezone($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereZaloUserId($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasLocalePreference
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = TableNameEnum::USERS->value;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'zalo_user_id',
        'facebook_id',
        'avatar',
        'native_lang',
        'learning_lang',
        'timezone',
        'email_verified_at',
        'role',
        'is_temp_guest',
        'temp_guest_expires_at',
        'first_login_completed_at',
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     * Защита критичных полей от случайной перезаписи.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
            'is_temp_guest' => 'boolean',
            'temp_guest_expires_at' => 'datetime',
            'first_login_completed_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the user's preferred locale for notifications.
     */
    public function preferredLocale(): string
    {
        return $this->native_lang ?? SystemLanguageEnum::VIETNAMESE->value;
    }

    /**
     * @return HasMany<ChatSession, $this>
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Students of this teacher.
     *
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TableNameEnum::TEACHER_STUDENT->value,
            'teacher_id',
            'student_id'
        )->withTimestamps();
    }

    /**
     * Teachers of this student.
     *
     * @return BelongsToMany<User, $this>
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TableNameEnum::TEACHER_STUDENT->value,
            'student_id',
            'teacher_id'
        )->withTimestamps();
    }

    /**
     * Lessons assigned by this teacher.
     *
     * @return HasMany<LessonAssignment, $this>
     */
    public function assignedLessons(): HasMany
    {
        return $this->hasMany(LessonAssignment::class, 'teacher_id');
    }

    /**
     * Lessons assigned to this student.
     *
     * @return HasMany<LessonAssignment, $this>
     */
    public function myAssignments(): HasMany
    {
        return $this->hasMany(LessonAssignment::class, 'student_id');
    }

    /**
     * All lesson attempts by this user.
     *
     * @return HasMany<LessonAttempt, $this>
     */
    public function lessonAttempts(): HasMany
    {
        return $this->hasMany(LessonAttempt::class);
    }

    /**
     * All scores earned by this user.
     *
     * @return HasMany<LessonScore, $this>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(LessonScore::class);
    }

    /**
     * Проверить, является ли пользователь администратором.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRoleEnum::ADMIN;
    }

    /**
     * Проверить, является ли пользователь учителем.
     */
    public function isTeacher(): bool
    {
        return $this->role === UserRoleEnum::TEACHER;
    }

    /**
     * Проверить, является ли пользователь учеником.
     */
    public function isStudent(): bool
    {
        return $this->role === UserRoleEnum::STUDENT;
    }

    /**
     * Назначить роль пользователю.
     * Только для явных административных операций.
     *
     * @param  UserRoleEnum  $role  Новая роль
     * @param  User|null  $actor  Пользователь, выполняющий операцию (для аудита)
     */
    public function assignRole(UserRoleEnum $role, ?User $actor = null): void
    {
        $old_role = $this->role;

        // Опционально: логирование для аудита
        if ($actor) {
            Log::info('User role changed', [
                'user_id' => $this->id,
                'user_email' => $this->email,
                'old_role' => $old_role->value,
                'new_role' => $role->value,
                'changed_by' => $actor->id,
                'changed_by_email' => $actor->email,
            ]);
        }

        $this->role = $role;
        $this->save();
    }

    /**
     * Назначить пользователя администратором.
     * Алиас для assignRole(UserRoleEnum::ADMIN).
     *
     * @param  User|null  $actor  Пользователь, выполняющий операцию (для аудита)
     */
    public function makeAdmin(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::ADMIN, $actor);
    }

    /**
     * Назначить пользователя учителем.
     *
     * @param  User|null  $actor  Пользователь, выполняющий операцию (для аудита)
     */
    public function makeTeacher(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::TEACHER, $actor);
    }

    /**
     * Назначить пользователя учеником.
     *
     * @param  User|null  $actor  Пользователь, выполняющий операцию (для аудита)
     */
    public function makeStudent(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::STUDENT, $actor);
    }

    /**
     * Get the full URL for the user's avatar.
     * Handles both local storage paths and external URLs (Facebook, Zalo, etc.)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        // Check if avatar is already a full URL (starts with http:// or https://)
        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        // Otherwise, it's a local storage path
        return asset('storage/'.$this->avatar);
    }
}
