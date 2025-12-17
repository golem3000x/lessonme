<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'ប្រធានបទរបស់អ្នកត្រូវបានអនុម័ត!',
    'topic_approved_body' => 'ប្រធានបទ ":topic" បានឆ្លងកាត់ការត្រួតពិនិត្យ ហើយឥឡូវនេះអាចប្រើបានហើយ។',
    'topic_approved_db' => 'ប្រធានបទបានឆ្លងកាត់ការត្រួតពិនិត្យ ហើយឥឡូវនេះអាចមើលឃើញក្នុងបញ្ជី។',
    'view_topics' => 'មើលប្រធានបទ',

    'topic_rejected_subject' => 'ប្រធានបទរបស់អ្នកត្រូវបានបដិសេធ',
    'topic_rejected_body' => 'ប្រធានបទ ":topic" មិនបានឆ្លងកាត់ការត្រួតពិនិត្យទេ។',
    'topic_rejected_reason' => 'សូមកែប្រែចំណងជើង ហើយព្យាយាមម្តងទៀត។',
    'topic_rejected_db' => 'ប្រធានបទត្រូវបានបដិសេធដោយការត្រួតពិនិត្យ។',
    'thanks' => 'សូមអរគុណសម្រាប់ការយល់ដឹងរបស់អ្នក!',

    // Creation notifications
    'topic_creation_pending' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានដាក់ស្នើសម្រាប់ការត្រួតពិនិត្យ',
    'keyword_creation_pending' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានដាក់ស្នើសម្រាប់ការត្រួតពិនិត្យ',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានអនុម័ត!',
    'topic_moderation_rejected' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានបដិសេធក្នុងការត្រួតពិនិត្យ',
    'topic_moderation_invalid' => 'មានកំហុសក្នុងការដំណើរការប្រធានបទ ":slug" របស់អ្នក',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានអនុម័ត!',
    'keyword_moderation_rejected' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានបដិសេធក្នុងការត្រួតពិនិត្យ',
    'keyword_moderation_invalid' => 'មានកំហុសក្នុងការដំណើរការពាក្យគន្លឹះ ":slug" របស់អ្នក',

    // Translation notifications for topics
    'topic_translate_successfully' => 'ប្រធានបទ ":slug" របស់អ្នកត្រូវបានបកប្រែដោយជោគជ័យ ហើយឥឡូវនេះអាចមើលឃើញហើយ!',
    'topic_translate_invalid' => 'មានកំហុសក្នុងការបកប្រែប្រធានបទ ":slug" របស់អ្នក',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'ពាក្យគន្លឹះ ":slug" របស់អ្នកត្រូវបានបកប្រែដោយជោគជ័យ ហើយឥឡូវនេះអាចមើលឃើញហើយ!',
    'keyword_translate_invalid' => 'មានកំហុសក្នុងការបកប្រែពាក្យគន្លឹះ ":slug" របស់អ្នក',

    // Auth notifications
    'user_auth_welcome' => 'សូមស្វាគមន៍មកវិញ :name!',
    'user_auth_registered' => 'សូមស្វាគមន៍មកកាន់វេទិការបស់យើង :name!',

    // Lesson creation notifications
    'lesson_creation_creation' => 'កំពុងបង្កើតមេរៀន...',
    'lesson_creation_created' => 'មេរៀន ":name" ត្រូវបានបង្កើតដោយជោគជ័យ!',
    'lesson_creation_regeneration' => 'កំពុងបង្កើតមេរៀនឡើងវិញ...',
    'lesson_creation_error' => 'ការបង្កើតមេរៀនបានបរាជ័យ។ សូមព្យាយាមម្តងទៀត។',
    'lesson_creation_exists' => 'មេរៀនស្រដៀងគ្នា ":name" មានរួចហើយ។',

    // Lesson assignment notifications
    'lesson_assigned' => ':teacher បានផ្តល់មេរៀនមួយដល់អ្នក: ":lesson"',
    'lesson_assignment_sent' => 'មេរៀន ":lesson" ត្រូវបានផ្តល់ទៅ :count សិស្ស',
    'lesson_unassigned' => ':teacher បានដកអ្នកចេញពីមេរៀន: ":lesson"',
    'lesson_assignment_removed' => 'សិស្ស :student ត្រូវបានដកចេញពីមេរៀន ":lesson"',

    // Course assignment notifications
    'course_assigned' => ':teacher បានផ្តល់វគ្គសិក្សាមួយដល់អ្នក: ":course"',
    'course_assignment_sent' => 'វគ្គសិក្សា ":course" ត្រូវបានផ្តល់ទៅ :count សិស្ស',
    'course_unassigned' => ':teacher បានដកអ្នកចេញពីវគ្គសិក្សា: ":course"',
    'course_assignment_removed' => 'សិស្ស :student ត្រូវបានដកចេញពីវគ្គសិក្សា ":course"',

    // Notification Bell
    'title' => 'ការជូនដំណឹង',
    'notifications' => 'ការជូនដំណឹង',
    'mark_all_read' => 'សម្គាល់ទាំងអស់ថាបានអាន',
    'mark_read' => 'សម្គាល់ថាបានអាន',
    'delete' => 'លុប',
    'no_notifications' => 'មិនទាន់មានការជូនដំណឹងទេ',
    'all_caught_up' => 'អ្នកបានអានអ្វីៗទាំងអស់ហើយ!',
    'previous' => 'មុន',
    'next' => 'បន្ទាប់',
    'page' => 'ទំព័រ',
];
