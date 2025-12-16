<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'ຫົວຂໍ້ຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ!',
    'topic_approved_body' => 'ຫົວຂໍ້ ":topic" ຜ່ານການກວດສອບແລ້ວ ແລະ ພ້ອມໃຊ້ງານ.',
    'topic_approved_db' => 'ຫົວຂໍ້ຜ່ານການກວດສອບແລ້ວ ແລະ ເຫັນໄດ້ໃນລາຍການ.',
    'view_topics' => 'ເບິ່ງຫົວຂໍ້',

    'topic_rejected_subject' => 'ຫົວຂໍ້ຂອງທ່ານຖືກປະຕິເສດ',
    'topic_rejected_body' => 'ຫົວຂໍ້ ":topic" ບໍ່ຜ່ານການກວດສອບ.',
    'topic_rejected_reason' => 'ກະລຸນາແກ້ໄຂຊື່ ແລະ ລອງໃໝ່.',
    'topic_rejected_db' => 'ຫົວຂໍ້ຖືກປະຕິເສດໂດຍການກວດສອບ.',
    'thanks' => 'ຂອບໃຈສຳລັບຄວາມເຂົ້າໃຈ!',

    // Creation notifications
    'topic_creation_pending' => 'ຫົວຂໍ້ ":slug" ຂອງທ່ານໄດ້ຖືກສົ່ງເພື່ອກວດສອບ',
    'keyword_creation_pending' => 'ຄຳສຳຄັນ ":slug" ຂອງທ່ານໄດ້ຖືກສົ່ງເພື່ອກວດສອບ',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'ຫົວຂໍ້ ":slug" ຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ!',
    'topic_moderation_rejected' => 'ຫົວຂໍ້ ":slug" ຂອງທ່ານຖືກປະຕິເສດລະຫວ່າງການກວດສອບ',
    'topic_moderation_invalid' => 'ເກີດຂໍ້ຜິດພາດໃນການດຳເນີນການຫົວຂໍ້ ":slug" ຂອງທ່ານ',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'ຄຳສຳຄັນ ":slug" ຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ!',
    'keyword_moderation_rejected' => 'ຄຳສຳຄັນ ":slug" ຂອງທ່ານຖືກປະຕິເສດລະຫວ່າງການກວດສອບ',
    'keyword_moderation_invalid' => 'ເກີດຂໍ້ຜິດພາດໃນການດຳເນີນການຄຳສຳຄັນ ":slug" ຂອງທ່ານ',

    // Translation notifications for topics
    'topic_translate_successfully' => 'ຫົວຂໍ້ ":slug" ຂອງທ່ານໄດ້ຖືກແປສຳເລັດ ແລະ ເຫັນໄດ້ແລ້ວ!',
    'topic_translate_invalid' => 'ເກີດຂໍ້ຜິດພາດໃນການແປຫົວຂໍ້ ":slug" ຂອງທ່ານ',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'ຄຳສຳຄັນ ":slug" ຂອງທ່ານໄດ້ຖືກແປສຳເລັດ ແລະ ເຫັນໄດ້ແລ້ວ!',
    'keyword_translate_invalid' => 'ເກີດຂໍ້ຜິດພາດໃນການແປຄຳສຳຄັນ ":slug" ຂອງທ່ານ',

    // Auth notifications
    'user_auth_welcome' => 'ຍິນດີຕ້ອນຮັບກັບຄືນ, :name!',
    'user_auth_registered' => 'ຍິນດີຕ້ອນຮັບສູ່ແພລດຟອມຂອງພວກເຮົາ, :name!',

    // Lesson creation notifications
    'lesson_creation_creation' => 'ກຳລັງສ້າງບົດຮຽນ...',
    'lesson_creation_created' => 'ບົດຮຽນ ":name" ໄດ້ຖືກສ້າງສຳເລັດແລ້ວ!',
    'lesson_creation_regeneration' => 'ກຳລັງສ້າງບົດຮຽນໃໝ່...',
    'lesson_creation_error' => 'ການສ້າງບົດຮຽນລົ້ມເຫຼວ. ກະລຸນາລອງໃໝ່.',
    'lesson_creation_exists' => 'ບົດຮຽນຄ້າຍຄືກັນ ":name" ມີຢູ່ແລ້ວ.',

    // Lesson assignment notifications
    'lesson_assigned' => ':teacher ມອບໝາຍບົດຮຽນໃຫ້ທ່ານ: ":lesson"',
    'lesson_assignment_sent' => 'ບົດຮຽນ ":lesson" ໄດ້ມອບໝາຍໃຫ້ :count ນັກຮຽນ',
    'lesson_unassigned' => ':teacher ລຶບທ່ານອອກຈາກບົດຮຽນ: ":lesson"',
    'lesson_assignment_removed' => 'ນັກຮຽນ :student ຖືກລຶບອອກຈາກບົດຮຽນ ":lesson"',

    // Notification Bell
    'title' => 'ການແຈ້ງເຕືອນ',
    'notifications' => 'ການແຈ້ງເຕືອນ',
    'mark_all_read' => 'ໝາຍທັງໝົດວ່າອ່ານແລ້ວ',
    'mark_read' => 'ໝາຍວ່າອ່ານແລ້ວ',
    'delete' => 'ລຶບ',
    'no_notifications' => 'ຍັງບໍ່ມີການແຈ້ງເຕືອນ',
    'all_caught_up' => 'ທ່ານອ່ານໝົດແລ້ວ!',
    'previous' => 'ກ່ອນໜ້າ',
    'next' => 'ຕໍ່ໄປ',
    'page' => 'ໜ້າ',
];
