<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'ប្រធានបទរបស់អ្នកត្រូវបានអនុម័ត!',
    'topic_approved_body' => 'ប្រធានបទ ":topic" បានឆ្លងកាត់ការត្រួតពិនិត្យ ហើយឥឡូវនេះអាចប្រើប្រាស់បាន។',
    'topic_approved_db' => 'ប្រធានបទបានឆ្លងកាត់ការត្រួតពិនិត្យ ហើយឥឡូវនេះអាចមើលហើញក្នុងបញ្ជី។',
    'view_topics' => 'មើលប្រធានបទ',

    'topic_rejected_subject' => 'ប្រធានបទរបស់អ្នកត្រូវបានបដិសេធ',
    'topic_rejected_body' => 'ប្រធានបទ ":topic" មិនបានឆ្លងកាត់ការត្រួតពិនិត្យទេ។',
    'topic_rejected_reason' => 'សូមកែសម្រួលចំណងជើង ហើយព្យាយាមម្តងទៀត។',
    'topic_rejected_db' => 'ប្រធានបទត្រូវបានបដិសេធដោយការត្រួតពិនិត្យ។',
    'thanks' => 'សូមអរគុណចំពោះការយល់ដឹងរបស់អ្នក!',

    // Creation notifications
    'topic_creation_pending' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានបញ្ជូនសម្រាប់ការត្រួតពិនិត្យ',
    'keyword_creation_pending' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានបញ្ជូនសម្រាប់ការត្រួតពិនិត្យ',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានអនុម័ត!',
    'topic_moderation_rejected' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានបដិសេធក្នុងពេលត្រួតពិនិត្យ',
    'topic_moderation_invalid' => 'កំហុសក្នុងការដំណើរការប្រធានបទ ":slug" របស់អ្នក',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានអនុម័ត!',
    'keyword_moderation_rejected' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានបដិសេធក្នុងពេលត្រួតពិនិត្យ',
    'keyword_moderation_invalid' => 'កំហុសក្នុងការដំណើរការពាក្យគន្លឹះ ":slug" របស់អ្នក',

    // Translation notifications for topics
    'topic_translate_successfully' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានបកប្រែដោយជោគជ័យ ហើយឥឡូវនេះអាចមើលហើញ!',
    'topic_translate_invalid' => 'កំហុសបកប្រែសម្រាប់ប្រធានបទ ":slug" របស់អ្នក',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានបកប្រែដោយជោគជ័យ ហើយឥឡូវនេះអាចមើលហើញ!',
    'keyword_translate_invalid' => 'កំហុសបកប្រែសម្រាប់ពាក្យគន្លឹះ ":slug" របស់អ្នក',

    // Auth notifications
    'user_auth_welcome' => 'សូមស្វាគមន៍មកវិញ :name!',
    'user_auth_registered' => 'សូមស្វាគមន៍មកកាន់វេទិការបស់យើង :name!',

    // Lesson creation notifications
    'lesson_creation_processing' => 'កំពុងបង្កើតមេរៀន...',
    'lesson_creation_successfully' => 'មេរៀន ":name" ត្រូវបានបង្កើតដោយជោគជ័យ!',
    'lesson_creation_error' => 'ការបង្កើតមេរៀនបានបរាជ័យ។ សូមព្យាយាមម្តងទៀត។',
    'lesson_creation_exists' => 'មេរៀនស្រដៀងគ្នា ":name" មានរួចហើយ។',

    // Lesson regeneration notifications
    'lesson_regeneration_processing' => 'កំពុងបង្កើតមេរៀនឡើងវិញ...',
    'lesson_regeneration_successfully' => 'មេរៀន ":name" ត្រូវបានបង្កើតឡើងវិញដោយជោគជ័យ!',
    'lesson_regeneration_error' => 'ការបង្កើតមេរៀនឡើងវិញបានបរាជ័យ។ សូមព្យាយាមម្តងទៀត។',
    'lesson_regeneration_exists' => 'មេរៀន ":name" មានរួចហើយ។',

    // Lesson assignment notifications
    'lesson_assigned' => ':teacher បានចាត់តាំងមេរៀនទៅអ្នក: ":lesson"',
    'lesson_assignment_sent' => 'មេរៀន ":lesson" ត្រូវបានចាត់តាំងទៅសិស្ស :count នាក់',
    'lesson_unassigned' => ':teacher បានដកអ្នកចេញពីមេរៀន: ":lesson"',
    'lesson_assignment_removed' => 'សិស្ស :student ត្រូវបានដកចេញពីមេរៀន ":lesson"',

    // Notification Bell
    'title' => 'ការជូនដំណឹង',
    'notifications' => 'ការជូនដំណឹង',
    'mark_all_read' => 'សម្គាល់ទាំងអស់ជាបានអាន',
    'mark_read' => 'សម្គាល់ជាបានអាន',
    'delete' => 'លុប',
    'no_notifications' => 'មិនមានការជូនដំណឹងនៅឡើយទេ',
    'all_caught_up' => 'អ្នកបានអានហើយទាំងអស់!',
    'previous' => 'មុន',
    'next' => 'បន្ទាប់',
    'page' => 'ទំព័រ',
];
