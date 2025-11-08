<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'Your topic has been approved!',
    'topic_approved_body' => 'The topic ":topic" has passed moderation and is now available.',
    'topic_approved_db' => 'The topic has passed moderation and is now visible in the list.',
    'view_topics' => 'View topics',

    'topic_rejected_subject' => 'Your topic was rejected',
    'topic_rejected_body' => 'The topic ":topic" did not pass moderation.',
    'topic_rejected_reason' => 'Please modify the title and try again.',
    'topic_rejected_db' => 'The topic was rejected by moderation.',
    'thanks' => 'Thank you for your understanding!',

    // Processing notifications
    'topic_processing_pending' => 'Your topic ":slug" has been submitted for moderation',
    'keyword_processing_pending' => 'Your keyword ":slug" has been submitted for moderation',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'Your topic ":slug" has been approved!',
    'topic_moderation_rejected' => 'Your topic ":slug" was rejected during moderation',
    'topic_moderation_invalid' => 'Error processing your topic ":slug"',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'Your keyword ":slug" has been approved!',
    'keyword_moderation_rejected' => 'Your keyword ":slug" was rejected during moderation',
    'keyword_moderation_invalid' => 'Error processing your keyword ":slug"',

    // Translation notifications for topics
    'topic_translate_successfully' => 'Your topic ":slug" has been successfully translated and is now visible!',
    'topic_translate_invalid' => 'Translation error for your topic ":slug"',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'Your keyword ":slug" has been successfully translated and is now visible!',
    'keyword_translate_invalid' => 'Translation error for your keyword ":slug"',
];
