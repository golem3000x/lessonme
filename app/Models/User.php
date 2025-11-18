<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\SystemLanguageEnum;
use App\Enums\TableNameEnum;
use App\Enums\UserRoleEnum;
use App\Models\Lesson\Lesson;
use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property UserRoleEnum $role
 * @property string|null $zalo_user_id
 * @property string|null $facebook_id
 * @property string|null $avatar
 * @property string $native_lang
 * @property string $learning_lang
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereLearningLangId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User whereNativeLangId($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereZaloUserId($value)
 * @method static Builder<static>|User whereFacebookId($value)
 * @mixin Eloquent
 * @method static Builder<static>|User whereLearningLang($value)
 * @method static Builder<static>|User whereNativeLang($value)
 * @property string $timezone
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereTimezone($value)
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
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     * Защита критичных полей от случайной перезаписи.
     *
     * @var list<string>
     */
    protected $guarded = [
        'role',
    ];

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
     * @param UserRoleEnum $role Новая роль
     * @param User|null $actor Пользователь, выполняющий операцию (для аудита)
     */
    public function assignRole(UserRoleEnum $role, ?User $actor = null): void
    {
        $old_role = $this->role;

        // Опционально: логирование для аудита
        if ($actor) {
            \Log::info('User role changed', [
                'user_id' => $this->id,
                'user_email' => $this->email,
                'old_role' => $old_role?->value,
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
     * @param User|null $actor Пользователь, выполняющий операцию (для аудита)
     */
    public function makeAdmin(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::ADMIN, $actor);
    }

    /**
     * Назначить пользователя учителем.
     *
     * @param User|null $actor Пользователь, выполняющий операцию (для аудита)
     */
    public function makeTeacher(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::TEACHER, $actor);
    }

    /**
     * Назначить пользователя учеником.
     *
     * @param User|null $actor Пользователь, выполняющий операцию (для аудита)
     */
    public function makeStudent(?User $actor = null): void
    {
        $this->assignRole(UserRoleEnum::STUDENT, $actor);
    }
}
