<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'เข้าสู่ระบบบัญชีของคุณ',
        'or_register' => 'หรือ',
        'register_link' => 'สร้างบัญชี',
        'email_label' => 'อีเมล',
        'email_placeholder' => 'อีเมล',
        'password_label' => 'รหัสผ่าน',
        'password_placeholder' => 'รหัสผ่าน',
        'remember_me' => 'จดจำฉัน',
        'forgot_password' => 'ลืมรหัสผ่าน?',
        'submit' => 'เข้าสู่ระบบ',
        'or_continue_with' => 'หรือดำเนินการต่อด้วย',
        'sign_in_with_zalo' => 'เข้าสู่ระบบด้วย Zalo',
        'sign_in_with_facebook' => 'เข้าสู่ระบบด้วย Facebook',
    ],

    // Register page
    'register' => [
        'title' => 'สร้างบัญชี',
        'or_login' => 'หรือ',
        'login_link' => 'เข้าสู่ระบบบัญชีของคุณ',
        'name_label' => 'ชื่อ-นามสกุล',
        'name_placeholder' => 'ชื่อ-นามสกุลของคุณ',
        'email_label' => 'อีเมล',
        'email_placeholder' => 'อีเมล',
        'date_of_birth_label' => 'วันเกิด',
        'password_label' => 'รหัสผ่าน',
        'password_placeholder' => 'อย่างน้อย 8 ตัวอักษร',
        'password_confirmation_label' => 'ยืนยันรหัสผ่าน',
        'password_confirmation_placeholder' => 'พิมพ์รหัสผ่านอีกครั้ง',
        'submit' => 'สร้างบัญชี',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'เลือกภาษาแม่ของคุณ',
        'description' => 'กรุณาเลือกภาษาแม่ของคุณเพื่อปรับแต่งประสบการณ์การใช้งาน',
        'native_lang_label' => 'ภาษาแม่',
        'select_language' => 'เลือกภาษา...',
        'submit' => 'ดำเนินการต่อ',
        'info_text' => 'คุณสามารถเปลี่ยนการตั้งค่าภาษาได้ทุกเมื่อในการตั้งค่าโปรไฟล์',
    ],

    // Role selection page
    'role_selection' => [
        'title' => 'เลือกบทบาทของคุณ',
        'description' => 'เลือกว่าคุณต้องการใช้แพลตฟอร์มของเราอย่างไร คุณสามารถเป็นทั้งครูและนักเรียนได้',
        'select_roles' => 'เลือกบทบาทของคุณ',
        'teacher' => 'ครู',
        'teacher_description' => 'สร้างบทเรียนและมอบหมายให้นักเรียน',
        'student' => 'นักเรียน',
        'student_description' => 'เรียนภาษาผ่านบทเรียนแบบโต้ตอบ',
        'teaching_languages' => 'ภาษาที่คุณสอน',
        'select_languages' => 'เลือกภาษา',
        'learning_languages' => 'ภาษาที่คุณต้องการเรียน',
        'submit' => 'ลงทะเบียนเสร็จสิ้น',
        'info_text' => 'คุณสามารถอัปเดตบทบาทและการตั้งค่าภาษาได้ทุกเมื่อในการตั้งค่าโปรไฟล์',
        'at_least_one_role' => 'กรุณาเลือกอย่างน้อยหนึ่งบทบาท',
        'teaching_lang_required' => 'กรุณาเลือกอย่างน้อยหนึ่งภาษาที่คุณสอน',
        'learning_lang_required' => 'กรุณาเลือกภาษาที่คุณต้องการเรียน',
    ],

    // Password reset
    'forgot_password' => [
        'title' => 'ลืมรหัสผ่าน?',
        'description' => 'ไม่มีปัญหา เพียงแจ้งอีเมลของคุณ แล้วเราจะส่งลิงก์รีเซ็ตรหัสผ่านให้คุณ',
        'email_label' => 'อีเมล',
        'email_placeholder' => 'อีเมล',
        'submit' => 'ส่งลิงก์รีเซ็ตรหัสผ่าน',
        'back_to_login' => 'กลับไปเข้าสู่ระบบ',
    ],

    'reset_password' => [
        'title' => 'รีเซ็ตรหัสผ่าน',
        'email_label' => 'อีเมล',
        'email_placeholder' => 'อีเมล',
        'password_label' => 'รหัสผ่านใหม่',
        'password_placeholder' => 'อย่างน้อย 8 ตัวอักษร',
        'password_confirmation_label' => 'ยืนยันรหัสผ่าน',
        'password_confirmation_placeholder' => 'พิมพ์รหัสผ่านอีกครั้ง',
        'submit' => 'รีเซ็ตรหัสผ่าน',
    ],

    // Common
    'failed' => 'ข้อมูลประจำตัวเหล่านี้ไม่ตรงกับบันทึกของเรา',
    'password' => 'รหัสผ่านที่ระบุไม่ถูกต้อง',
    'throttle' => 'พยายามเข้าสู่ระบบมากเกินไป กรุณาลองใหม่อีกครั้งใน :seconds วินาที',
    'reset_link_sent' => 'เราได้ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณแล้ว!',
    'reset_success' => 'รหัสผ่านของคุณถูกรีเซ็ตแล้ว!',
    'reset_token_invalid' => 'โทเค็นรีเซ็ตรหัสผ่านนี้ไม่ถูกต้อง',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'สวัสดีตอนเช้า',
        'afternoon' => 'สวัสดีตอนบ่าย',
        'evening' => 'สวัสดีตอนเย็น',
        'night' => 'ราตรีสวัสดิ์',
    ],

    // Welcome messages
    'welcome_message' => ':greeting, :name! ยินดีต้อนรับกลับ!',
    'registration_message' => ':greeting, :name! ยินดีต้อนรับสู่แพลตฟอร์มของเรา!',
];
