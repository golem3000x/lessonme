<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'ເຂົ້າສູ່ບັນຊີຂອງທ່ານ',
        'or_register' => 'ຫຼື',
        'register_link' => 'ສ້າງບັນຊີ',
        'email_label' => 'ທີ່ຢູ່ອີເມວ',
        'email_placeholder' => 'ທີ່ຢູ່ອີເມວ',
        'password_label' => 'ລະຫັດຜ່ານ',
        'password_placeholder' => 'ລະຫັດຜ່ານ',
        'remember_me' => 'ຈື່ຂ້ອຍ',
        'forgot_password' => 'ລືມລະຫັດຜ່ານ?',
        'submit' => 'ເຂົ້າສູ່ລະບົບ',
        'or_continue_with' => 'ຫຼື ດຳເນີນການດ້ວຍ',
        'sign_in_with_zalo' => 'ເຂົ້າສູ່ລະບົບດ້ວຍ Zalo',
        'sign_in_with_facebook' => 'ເຂົ້າສູ່ລະບົບດ້ວຍ Facebook',
    ],

    // Register page
    'register' => [
        'title' => 'ສ້າງບັນຊີ',
        'or_login' => 'ຫຼື',
        'login_link' => 'ເຂົ້າສູ່ບັນຊີຂອງທ່ານ',
        'first_name_label' => 'ຊື່',
        'first_name_placeholder' => 'ຊື່ຂອງທ່ານ',
        'last_name_label' => 'ນາມສະກຸນ',
        'last_name_placeholder' => 'ນາມສະກຸນຂອງທ່ານ',
        'email_label' => 'ທີ່ຢູ່ອີເມວ',
        'email_placeholder' => 'ທີ່ຢູ່ອີເມວ',
        'date_of_birth_label' => 'ວັນເດືອນປີເກີດ',
        'password_label' => 'ລະຫັດຜ່ານ',
        'password_placeholder' => 'ຢ່າງໜ້ອຍ 8 ຕົວອັກສອນ',
        'password_confirmation_label' => 'ຢືນຢັນລະຫັດຜ່ານ',
        'password_confirmation_placeholder' => 'ໃສ່ລະຫັດຜ່ານຄືນ',
        'submit' => 'ສ້າງບັນຊີ',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'ເລືອກພາສາແມ່ຂອງທ່ານ',
        'description' => 'ກະລຸນາເລືອກພາສາແມ່ຂອງທ່ານເພື່ອປັບແຕ່ງປະສົບການຂອງທ່ານ.',
        'native_lang_label' => 'ພາສາແມ່',
        'select_language' => 'ເລືອກພາສາ...',
        'submit' => 'ດຳເນີນການຕໍ່',
        'info_text' => 'ທ່ານສາມາດປ່ຽນການຕັ້ງຄ່າພາສາໄດ້ທຸກເວລາໃນການຕັ້ງຄ່າໂປຣໄຟລ໌.',
    ],

    // Role selection page
    'role_selection' => [
        'title' => 'ເລືອກບົດບາດຂອງທ່ານ',
        'description' => 'ເລືອກວິທີທີ່ທ່ານຕ້ອງການໃຊ້ແພລດຟອມຂອງພວກເຮົາ. ທ່ານສາມາດເປັນທັງຄູ ແລະ ນັກຮຽນ.',
        'select_roles' => 'ເລືອກບົດບາດຂອງທ່ານ',
        'teacher' => 'ຄູ',
        'teacher_description' => 'ສ້າງບົດຮຽນ ແລະ ມອບໝາຍໃຫ້ນັກຮຽນ',
        'student' => 'ນັກຮຽນ',
        'student_description' => 'ຮຽນພາສາຜ່ານບົດຮຽນໂຕ້ຕອບ',
        'teaching_languages' => 'ພາສາທີ່ທ່ານສອນ',
        'select_languages' => 'ເລືອກພາສາ',
        'learning_languages' => 'ພາສາທີ່ທ່ານຕ້ອງການຮຽນ',
        'submit' => 'ສຳເລັດການລົງທະບຽນ',
        'info_text' => 'ທ່ານສາມາດອັບເດດບົດບາດ ແລະ ການຕັ້ງຄ່າພາສາໄດ້ທຸກເວລາໃນການຕັ້ງຄ່າໂປຣໄຟລ໌.',
        'at_least_one_role' => 'ກະລຸນາເລືອກຢ່າງໜ້ອຍໜຶ່ງບົດບາດ.',
        'teaching_lang_required' => 'ກະລຸນາເລືອກຢ່າງໜ້ອຍໜຶ່ງພາສາທີ່ທ່ານສອນ.',
        'learning_lang_required' => 'ກະລຸນາເລືອກພາສາທີ່ທ່ານຕ້ອງການຮຽນ.',
    ],

    // Password reset
    'forgot_password' => [
        'title' => 'ລືມລະຫັດຜ່ານ?',
        'description' => 'ບໍ່ມີບັນຫາ. ພຽງແຕ່ບອກອີເມວຂອງທ່ານ ແລະ ພວກເຮົາຈະສົ່ງລິ້ງຕັ້ງລະຫັດຜ່ານໃໝ່ໃຫ້ທ່ານ.',
        'email_label' => 'ທີ່ຢູ່ອີເມວ',
        'email_placeholder' => 'ທີ່ຢູ່ອີເມວ',
        'submit' => 'ສົ່ງລິ້ງຕັ້ງລະຫັດຜ່ານໃໝ່',
        'back_to_login' => 'ກັບໄປເຂົ້າສູ່ລະບົບ',
    ],

    'reset_password' => [
        'title' => 'ຕັ້ງລະຫັດຜ່ານໃໝ່',
        'email_label' => 'ທີ່ຢູ່ອີເມວ',
        'email_placeholder' => 'ທີ່ຢູ່ອີເມວ',
        'password_label' => 'ລະຫັດຜ່ານໃໝ່',
        'password_placeholder' => 'ຢ່າງໜ້ອຍ 8 ຕົວອັກສອນ',
        'password_confirmation_label' => 'ຢືນຢັນລະຫັດຜ່ານ',
        'password_confirmation_placeholder' => 'ໃສ່ລະຫັດຜ່ານຄືນ',
        'submit' => 'ຕັ້ງລະຫັດຜ່ານໃໝ່',
    ],

    // Common
    'failed' => 'ຂໍ້ມູນເຫຼົ່ານີ້ບໍ່ກົງກັບບັນທຶກຂອງພວກເຮົາ.',
    'password' => 'ລະຫັດຜ່ານທີ່ໃຫ້ບໍ່ຖືກຕ້ອງ.',
    'throttle' => 'ມີການພະຍາຍາມເຂົ້າສູ່ລະບົບຫຼາຍເກີນໄປ. ກະລຸນາລອງໃໝ່ໃນ :seconds ວິນາທີ.',
    'reset_link_sent' => 'ພວກເຮົາໄດ້ສົ່ງລິ້ງຕັ້ງລະຫັດຜ່ານໃໝ່ໄປທາງອີເມວແລ້ວ!',
    'reset_success' => 'ລະຫັດຜ່ານຂອງທ່ານໄດ້ຖືກຕັ້ງໃໝ່ແລ້ວ!',
    'reset_token_invalid' => 'ໂທເຄັນຕັ້ງລະຫັດຜ່ານໃໝ່ນີ້ບໍ່ຖືກຕ້ອງ.',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'ສະບາຍດີຕອນເຊົ້າ',
        'afternoon' => 'ສະບາຍດີຕອນບ່າຍ',
        'evening' => 'ສະບາຍດີຕອນແລງ',
        'night' => 'ລາຕີສະຫວັດ',
    ],

    // Welcome messages
    'welcome_message' => ':greeting, :name! ຍິນດີຕ້ອນຮັບກັບຄືນ!',
    'registration_message' => ':greeting, :name! ຍິນດີຕ້ອນຮັບສູ່ແພລດຟອມຂອງພວກເຮົາ!',

    // Ban messages
    'banned' => 'ບັນຊີຂອງທ່ານຖືກບລັອກ. ເຫດຜົນ: :reason',
    'no_reason' => 'ບໍ່ໄດ້ໃຫ້ເຫດຜົນ',
];
