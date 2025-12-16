<?php

declare(strict_types=1);

return [
    // Navigation
    'chats' => 'ການສົນທະນາ',
    'chat' => 'ສົນທະນາ',

    // Table columns
    'user' => 'ຜູ້ໃຊ້',
    'last_message' => 'ຂໍ້ຄວາມລ່າສຸດ',
    'unread' => 'ຍັງບໍ່ອ່ານ',
    'status' => 'ສະຖານະ',
    'created' => 'ສ້າງເມື່ອ',

    // Status values
    'status_open' => 'ເປີດ',
    'status_closed' => 'ປິດ',

    // Filters
    'filter_status' => 'ສະຖານະ',
    'filter_unread' => 'ມີຍັງບໍ່ອ່ານ',

    // Actions
    'action_view' => 'ເບິ່ງ',
    'action_close_chat' => 'ປິດການສົນທະນາ',
    'action_reopen_chat' => 'ເປີດການສົນທະນາຄືນ',

    // Chat view
    'chat_with' => 'ສົນທະນາກັບ :name',
    'user_info' => 'ຂໍ້ມູນຜູ້ໃຊ້',
    'name' => 'ຊື່',
    'email' => 'ອີເມວ',
    'chat_status' => 'ສະຖານະການສົນທະນາ',
    'created_at' => 'ສ້າງເມື່ອ',
    'last_message_at' => 'ຂໍ້ຄວາມລ່າສຸດ',
    'no_messages' => 'ບໍ່ມີຂໍ້ຄວາມ',

    // Messages
    'message_history' => 'ປະຫວັດຂໍ້ຄວາມ',
    'send_message' => 'ສົ່ງຂໍ້ຄວາມ',
    'enter_message' => 'ປ້ອນຂໍ້ຄວາມ...',
    'send_button' => 'ສົ່ງ',
    'sending' => 'ກຳລັງສົ່ງ...',

    // Validation
    'validation_required' => 'ຊ່ອງນີ້ຈຳເປັນຕ້ອງຕື່ມ',
    'validation_max' => 'ສູງສຸດ :max ຕົວອັກສອນ',

    // Lesson Feedbacks
    'lesson_feedbacks' => 'ຄຳຄິດເຫັນບົດຮຽນ',
    'lesson_feedback' => 'ຄຳຄິດເຫັນບົດຮຽນ',
    'lesson' => 'ບົດຮຽນ',
    'rating' => 'ຄະແນນ',
    'comment' => 'ຄຳເຫັນ',
    'submitted_at' => 'ສົ່ງເມື່ອ',
    'like' => 'ມັກ',
    'dislike' => 'ບໍ່ມັກ',

    // Feedback History
    'feedback_history' => 'ປະຫວັດຄຳຄິດເຫັນ',
    'feedback_history_item' => 'ລາຍການປະຫວັດຄຳຄິດເຫັນ',
    'history_version' => 'ເວີຊັນປະຫວັດ',
    'no_history_link' => 'ບໍ່ມີລິ້ງປະຫວັດ',
    'original_date' => 'ວັນທີເດີມ',
    'archived_at' => 'ເກັບເມື່ອ',

    // User Reports
    'reports' => 'ລາຍງານ',
    'report' => 'ລາຍງານ',
    'reporter' => 'ຜູ້ລາຍງານ',
    'reported_user' => 'ຜູ້ຖືກລາຍງານ',
    'reason' => 'ເຫດຜົນ',
    'description' => 'ລາຍລະອຽດ',
    'resolved' => 'ແກ້ໄຂແລ້ວ',
    'is_resolved' => 'ແກ້ໄຂແລ້ວບໍ່',
    'resolved_at' => 'ແກ້ໄຂເມື່ອ',
    'resolved_by' => 'ແກ້ໄຂໂດຍ',
    'report_details' => 'ລາຍລະອຽດລາຍງານ',
    'resolution' => 'ການແກ້ໄຂ',

    // Report Filters
    'filter_resolved' => 'ສະຖານະການແກ້ໄຂ',
    'filter_all' => 'ທັງໝົດ',
    'filter_resolved_yes' => 'ແກ້ໄຂແລ້ວ',
    'filter_resolved_no' => 'ຍັງບໍ່ແກ້ໄຂ',

    // Report Actions
    'action_resolve' => 'ແກ້ໄຂ',
    'action_resolve_selected' => 'ແກ້ໄຂທີ່ເລືອກ',
    'resolve_report_title' => 'ແກ້ໄຂລາຍງານ',
    'resolve_report_description' => 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການໝາຍລາຍງານນີ້ວ່າແກ້ໄຂແລ້ວ?',
    'report_resolved' => 'ແກ້ໄຂລາຍງານແລ້ວ',
    'reports_resolved' => 'ແກ້ໄຂລາຍງານທີ່ເລືອກແລ້ວ',

    // Report Notifications
    'new_report_notification' => 'ລາຍງານຜູ້ໃຊ້ໃໝ່',
    'new_report_body' => ':reporter ລາຍງານ :reported ເນື່ອງຈາກ :reason',

    // Ban Actions
    'action_ban_user' => 'ບລັອກຜູ້ໃຊ້',
    'ban_user_title' => 'ບລັອກຜູ້ໃຊ້',
    'ban_user_description' => 'ນີ້ຈະບລັອກຜູ້ໃຊ້ຈາກການເຂົ້າເຖິງລະບົບ.',
    'ban_reason' => 'ເຫດຜົນການບລັອກ',
    'ban_expires_at' => 'ການບລັອກໝົດອາຍຸ',
    'ban_expires_hint' => 'ປ່ອຍວ່າງສຳລັບການບລັອກຖາວອນ',
    'user_banned' => 'ບລັອກຜູ້ໃຊ້ແລ້ວ',
];
