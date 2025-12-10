<?php

declare(strict_types=1);

namespace App\Enums\System;

enum ReportReasonEnum: string
{
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';
    case FAKE_PROFILE = 'fake_profile';
    case SCAM = 'scam';
    case OFFENSIVE_LANGUAGE = 'offensive_language';
    case UNPROFESSIONAL_BEHAVIOR = 'unprofessional_behavior';
    case OTHER = 'other';

    /**
     * Get translated label for the reason.
     */
    public function label(): string
    {
        return match ($this) {
            self::SPAM => __('messages.report_reason_spam'),
            self::HARASSMENT => __('messages.report_reason_harassment'),
            self::INAPPROPRIATE_CONTENT => __('messages.report_reason_inappropriate_content'),
            self::FAKE_PROFILE => __('messages.report_reason_fake_profile'),
            self::SCAM => __('messages.report_reason_scam'),
            self::OFFENSIVE_LANGUAGE => __('messages.report_reason_offensive_language'),
            self::UNPROFESSIONAL_BEHAVIOR => __('messages.report_reason_unprofessional_behavior'),
            self::OTHER => __('messages.report_reason_other'),
        };
    }

    /**
     * Get all cases as options for select.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
