<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'หัวข้อของคุณได้รับการอนุมัติแล้ว!',
    'topic_approved_body' => 'หัวข้อ ":topic" ผ่านการตรวจสอบและพร้อมใช้งานแล้ว',
    'topic_approved_db' => 'หัวข้อผ่านการตรวจสอบและแสดงในรายการแล้ว',
    'view_topics' => 'ดูหัวข้อ',

    'topic_rejected_subject' => 'หัวข้อของคุณถูกปฏิเสธ',
    'topic_rejected_body' => 'หัวข้อ ":topic" ไม่ผ่านการตรวจสอบ',
    'topic_rejected_reason' => 'กรุณาแก้ไขชื่อและลองใหม่อีกครั้ง',
    'topic_rejected_db' => 'หัวข้อถูกปฏิเสธโดยการตรวจสอบ',
    'thanks' => 'ขอบคุณสำหรับความเข้าใจ!',

    // Creation notifications
    'topic_creation_pending' => 'หัวข้อ ":slug" ของคุณถูกส่งเพื่อตรวจสอบ',
    'keyword_creation_pending' => 'คำสำคัญ ":slug" ของคุณถูกส่งเพื่อตรวจสอบ',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'หัวข้อ ":slug" ของคุณได้รับการอนุมัติแล้ว!',
    'topic_moderation_rejected' => 'หัวข้อ ":slug" ของคุณถูกปฏิเสธระหว่างการตรวจสอบ',
    'topic_moderation_invalid' => 'เกิดข้อผิดพลาดในการประมวลผลหัวข้อ ":slug" ของคุณ',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'คำสำคัญ ":slug" ของคุณได้รับการอนุมัติแล้ว!',
    'keyword_moderation_rejected' => 'คำสำคัญ ":slug" ของคุณถูกปฏิเสธระหว่างการตรวจสอบ',
    'keyword_moderation_invalid' => 'เกิดข้อผิดพลาดในการประมวลผลคำสำคัญ ":slug" ของคุณ',

    // Translation notifications for topics
    'topic_translate_successfully' => 'หัวข้อ ":slug" ของคุณได้รับการแปลสำเร็จและพร้อมแสดงแล้ว!',
    'topic_translate_invalid' => 'เกิดข้อผิดพลาดในการแปลหัวข้อ ":slug" ของคุณ',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'คำสำคัญ ":slug" ของคุณได้รับการแปลสำเร็จและพร้อมแสดงแล้ว!',
    'keyword_translate_invalid' => 'เกิดข้อผิดพลาดในการแปลคำสำคัญ ":slug" ของคุณ',

    // Auth notifications
    'user_auth_welcome' => 'ยินดีต้อนรับกลับ :name!',
    'user_auth_registered' => 'ยินดีต้อนรับสู่แพลตฟอร์มของเรา :name!',

    // Lesson creation notifications
    'lesson_creation_processing' => 'กำลังสร้างบทเรียน...',
    'lesson_creation_successfully' => 'บทเรียน ":name" ถูกสร้างสำเร็จ!',
    'lesson_creation_error' => 'การสร้างบทเรียนล้มเหลว กรุณาลองใหม่',
    'lesson_creation_exists' => 'บทเรียนที่คล้ายกัน ":name" มีอยู่แล้ว',

    // Lesson regeneration notifications
    'lesson_regeneration_processing' => 'กำลังสร้างบทเรียนใหม่...',
    'lesson_regeneration_successfully' => 'บทเรียน ":name" ถูกสร้างใหม่สำเร็จ!',
    'lesson_regeneration_error' => 'การสร้างบทเรียนใหม่ล้มเหลว กรุณาลองใหม่',
    'lesson_regeneration_exists' => 'บทเรียน ":name" มีอยู่แล้ว',

    // Lesson assignment notifications
    'lesson_assigned' => ':teacher มอบหมายบทเรียนให้คุณ: ":lesson"',
    'lesson_assignment_sent' => 'มอบหมายบทเรียน ":lesson" ให้นักเรียน :count คน',
    'lesson_unassigned' => ':teacher ลบคุณออกจากบทเรียน: ":lesson"',
    'lesson_assignment_removed' => 'นักเรียน :student ถูกลบออกจากบทเรียน ":lesson"',

    // Notification Bell
    'title' => 'การแจ้งเตือน',
    'notifications' => 'การแจ้งเตือน',
    'mark_all_read' => 'ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว',
    'mark_read' => 'ทำเครื่องหมายว่าอ่านแล้ว',
    'delete' => 'ลบ',
    'no_notifications' => 'ยังไม่มีการแจ้งเตือน',
    'all_caught_up' => 'คุณอ่านทั้งหมดแล้ว!',
    'previous' => 'ก่อนหน้า',
    'next' => 'ถัดไป',
    'page' => 'หน้า',
];
