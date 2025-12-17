<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\System\SystemLanguageEnum;
use App\Models\UserLearningLanguage;
use App\Models\UserTeachingLanguage;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait for managing user teaching and learning languages.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserTeachingLanguage> $teachingLanguages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserLearningLanguage> $learningLanguages
 */
trait HasLanguages
{
    /**
     * Get teaching languages for this teacher.
     *
     * @return HasMany<UserTeachingLanguage, $this>
     */
    public function teachingLanguages(): HasMany
    {
        return $this->hasMany(UserTeachingLanguage::class);
    }

    /**
     * Get learning languages for this student.
     *
     * @return HasMany<UserLearningLanguage, $this>
     */
    public function learningLanguages(): HasMany
    {
        return $this->hasMany(UserLearningLanguage::class);
    }

    /**
     * Add teaching language for this teacher.
     */
    public function addTeachingLanguage(SystemLanguageEnum $language): void
    {
        if (! $this->isTeacher()) {
            return;
        }

        $this->teachingLanguages()->firstOrCreate([
            'language' => $language->value,
        ]);
    }

    /**
     * Remove teaching language.
     */
    public function removeTeachingLanguage(SystemLanguageEnum $language): void
    {
        $this->teachingLanguages()->where('language', '=', $language->value)->delete();
    }

    /**
     * Sync teaching languages.
     *
     * @param  array<string>  $languages  Language values to sync
     */
    public function syncTeachingLanguages(array $languages): void
    {
        $this->teachingLanguages()->delete();

        foreach ($languages as $language) {
            $this->teachingLanguages()->create([
                'language' => $language,
            ]);
        }
    }

    /**
     * Get teaching language values as array.
     *
     * @return array<string>
     */
    public function getTeachingLanguageValues(): array
    {
        return $this->teachingLanguages()->pluck('language')->toArray();
    }

    /**
     * Add learning language for this student.
     */
    public function addLearningLanguage(SystemLanguageEnum $language): void
    {
        if (! $this->isStudent()) {
            return;
        }

        $this->learningLanguages()->firstOrCreate([
            'language' => $language->value,
        ]);
    }

    /**
     * Remove learning language.
     */
    public function removeLearningLanguage(SystemLanguageEnum $language): void
    {
        $this->learningLanguages()->where('language', '=', $language->value)->delete();
    }

    /**
     * Sync learning languages.
     *
     * @param  array<string>  $languages  Language values to sync
     */
    public function syncLearningLanguages(array $languages): void
    {
        $this->learningLanguages()->delete();

        foreach ($languages as $language) {
            $this->learningLanguages()->create([
                'language' => $language,
            ]);
        }
    }

    /**
     * Get learning language values as array.
     *
     * @return array<string>
     */
    public function getLearningLanguageValues(): array
    {
        return $this->learningLanguages()->pluck('language')->toArray();
    }
}
