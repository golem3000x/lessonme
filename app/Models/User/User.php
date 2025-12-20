<?php

declare(strict_types=1);

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\TableNameEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use App\Models\User\Teacher\TeacherReview;
use App\Notifications\ResetPasswordNotification;
use App\Traits\Bannable;
use App\Traits\HasLanguages;
use App\Traits\HasRoles;
use App\Traits\HasStudentsAndTeachers;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
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
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $first_login_completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $zalo_user_id
 * @property string|null $facebook_id
 * @property string|null $avatar
 * @property Carbon $date_of_birth
 * @property string|null $bio
 * @property string $native_lang
 * @property bool $is_looking_for_teacher
 * @property bool $is_accepting_students
 * @property array<array-key, mixed>|null $settings
 * @property string $timezone
 * @property bool $is_temp_guest
 * @property Carbon|null $temp_guest_expires_at
 * @property-read Collection<int, LessonAssignment> $assignedLessons
 * @property-read int|null $assigned_lessons_count
 * @property-read Collection<int, ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read string|null $avatar_url
 * @property-read float|null $average_rating
 * @property-read string $full_name
 * @property-read int $reviews_count
 * @property-read Collection<int, \App\Models\User\Teacher\TeacherReview> $givenReviews
 * @property-read int|null $given_reviews_count
 * @property-read Collection<int, \App\Models\User\UserLearningLanguage> $learningLanguages
 * @property-read int|null $learning_languages_count
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
 * @property-read Collection<int, \App\Models\User\Teacher\TeacherReview> $receivedReviews
 * @property-read int|null $received_reviews_count
 * @property-read Collection<int, \App\Models\User\Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, LessonScore> $scores
 * @property-read int|null $scores_count
 * @property-read Collection<int, User> $students
 * @property-read int|null $students_count
 * @property-read Collection<int, User> $teachers
 * @property-read int|null $teachers_count
 * @property-read Collection<int, \App\Models\User\UserTeachingLanguage> $teachingLanguages
 * @property-read int|null $teaching_languages_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereBio($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDateOfBirth($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereFacebookId($value)
 * @method static Builder<static>|User whereFirstLoginCompletedAt($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsAcceptingStudents($value)
 * @method static Builder<static>|User whereIsLookingForTeacher($value)
 * @method static Builder<static>|User whereIsTempGuest($value)
 * @method static Builder<static>|User whereLastName($value)
 * @method static Builder<static>|User whereNativeLang($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereSettings($value)
 * @method static Builder<static>|User whereTempGuestExpiresAt($value)
 * @method static Builder<static>|User whereTimezone($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUuid($value)
 * @method static Builder<static>|User whereZaloUserId($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasLocalePreference, HasName
{
    /** @use HasFactory<UserFactory> */
    use Bannable;

    use HasFactory;
    use HasLanguages;
    use HasRoles;
    use HasStudentsAndTeachers;
    use Notifiable;

    protected $table = TableNameEnum::USERS->value;

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'zalo_user_id',
        'facebook_id',
        'avatar',
        'date_of_birth',
        'bio',
        'native_lang',
        'is_looking_for_teacher',
        'is_accepting_students',
        'timezone',
        'email_verified_at',
        'is_temp_guest',
        'temp_guest_expires_at',
        'first_login_completed_at',
        'settings',
        'is_banned',
        'banned_at',
        'banned_by',
        'ban_reason',
        'ban_expires_at',
    ];

    /**
     * The attributes that should be guarded from mass assignment.
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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
        'full_name',
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
            'date_of_birth' => 'date',
            'is_looking_for_teacher' => 'boolean',
            'is_accepting_students' => 'boolean',
            'is_temp_guest' => 'boolean',
            'temp_guest_expires_at' => 'datetime',
            'first_login_completed_at' => 'datetime',
            'settings' => 'array',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'ban_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a specific setting value.
     */
    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Check if greeting should be shown.
     */
    public function shouldShowGreeting(): bool
    {
        return $this->getSetting('show_greeting', true);
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the name to display in Filament admin panel.
     */
    public function getFilamentName(): string
    {
        return $this->full_name;
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
     * Courses assigned by this teacher.
     *
     * @return HasMany<\App\Models\Course\CourseAssignment, $this>
     */
    public function assignedCourses(): HasMany
    {
        return $this->hasMany(\App\Models\Course\CourseAssignment::class, 'teacher_id');
    }

    /**
     * Courses assigned to this student.
     *
     * @return HasMany<\App\Models\Course\CourseAssignment, $this>
     */
    public function myCourseAssignments(): HasMany
    {
        return $this->hasMany(\App\Models\Course\CourseAssignment::class, 'student_id');
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
     * Reviews received by this teacher.
     *
     * @return HasMany<TeacherReview, $this>
     */
    public function receivedReviews(): HasMany
    {
        return $this->hasMany(TeacherReview::class, 'teacher_id');
    }

    /**
     * Reviews given by this student.
     *
     * @return HasMany<TeacherReview, $this>
     */
    public function givenReviews(): HasMany
    {
        return $this->hasMany(TeacherReview::class, 'student_id');
    }

    /**
     * Get average rating for this teacher.
     */
    public function getAverageRatingAttribute(): ?float
    {
        if (! $this->isTeacher()) {
            return null;
        }

        $avg = $this->receivedReviews()->avg('rating');

        return $avg ? round((float) $avg, 1) : null;
    }

    /**
     * Get reviews count for this teacher.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->receivedReviews()->count();
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

    /**
     * Send the password reset notification.
     * Uses the application's current locale for email content.
     */
    public function sendPasswordResetNotification($token): void
    {
        // Get user's preferred locale
        $locale = $this->preferredLocale();

        // Set locale for email translation
        app()->setLocale($locale);

        $this->notify(new ResetPasswordNotification($token));
    }
}
