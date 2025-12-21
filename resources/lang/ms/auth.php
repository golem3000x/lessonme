<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'Log masuk ke akaun anda',
        'or_register' => 'Atau',
        'register_link' => 'cipta akaun',
        'email_label' => 'Alamat e-mel',
        'email_placeholder' => 'Alamat e-mel',
        'password_label' => 'Kata laluan',
        'password_placeholder' => 'Kata laluan',
        'remember_me' => 'Ingat saya',
        'forgot_password' => 'Lupa kata laluan?',
        'submit' => 'Log masuk',
        'or_continue_with' => 'Atau teruskan dengan',
        'sign_in_with_zalo' => 'Log masuk dengan Zalo',
        'sign_in_with_facebook' => 'Log masuk dengan Facebook',
    ],

    // Register page
    'register' => [
        'title' => 'Cipta akaun',
        'or_login' => 'Atau',
        'login_link' => 'log masuk ke akaun anda',
        'name_label' => 'Nama Penuh',
        'name_placeholder' => 'Nama penuh anda',
        'email_label' => 'Alamat e-mel',
        'email_placeholder' => 'Alamat e-mel',
        'date_of_birth_label' => 'Tarikh Lahir',
        'password_label' => 'Kata laluan',
        'password_placeholder' => 'Minimum 8 aksara',
        'password_confirmation_label' => 'Sahkan kata laluan',
        'password_confirmation_placeholder' => 'Ulang kata laluan',
        'submit' => 'Cipta akaun',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'Pilih Bahasa Ibunda Anda',
        'description' => 'Sila pilih bahasa ibunda anda untuk memperibadikan pengalaman anda.',
        'native_lang_label' => 'Bahasa Ibunda',
        'select_language' => 'Pilih bahasa...',
        'submit' => 'Teruskan',
        'info_text' => 'Anda boleh menukar pilihan bahasa pada bila-bila masa dalam tetapan profil anda.',
    ],

    // Role selection page
    'role_selection' => [
        'title' => 'Pilih Peranan Anda',
        'description' => 'Pilih cara anda mahu menggunakan platform kami. Anda boleh menjadi guru dan pelajar.',
        'select_roles' => 'Pilih Peranan Anda',
        'teacher' => 'Guru',
        'teacher_description' => 'Cipta pelajaran dan tugaskan kepada pelajar',
        'student' => 'Pelajar',
        'student_description' => 'Belajar bahasa melalui pelajaran interaktif',
        'teaching_languages' => 'Bahasa Yang Anda Ajar',
        'select_languages' => 'Pilih bahasa',
        'learning_languages' => 'Bahasa Yang Anda Mahu Pelajari',
        'submit' => 'Selesaikan Pendaftaran',
        'info_text' => 'Anda boleh mengemaskini peranan dan pilihan bahasa anda pada bila-bila masa dalam tetapan profil anda.',
        'at_least_one_role' => 'Sila pilih sekurang-kurangnya satu peranan.',
        'teaching_lang_required' => 'Sila pilih sekurang-kurangnya satu bahasa yang anda ajar.',
        'learning_lang_required' => 'Sila pilih bahasa yang anda mahu pelajari.',
    ],

    // Password reset
    'forgot_password' => [
        'title' => 'Lupa kata laluan anda?',
        'description' => 'Tiada masalah. Beritahu kami alamat e-mel anda dan kami akan menghantar pautan tetapan semula kata laluan.',
        'email_label' => 'Alamat e-mel',
        'email_placeholder' => 'Alamat e-mel',
        'submit' => 'E-mel pautan tetapan semula kata laluan',
        'back_to_login' => 'Kembali ke log masuk',
    ],

    'reset_password' => [
        'title' => 'Tetapkan semula kata laluan',
        'email_label' => 'Alamat e-mel',
        'email_placeholder' => 'Alamat e-mel',
        'password_label' => 'Kata laluan baharu',
        'password_placeholder' => 'Minimum 8 aksara',
        'password_confirmation_label' => 'Sahkan kata laluan',
        'password_confirmation_placeholder' => 'Ulang kata laluan',
        'submit' => 'Tetapkan semula kata laluan',
    ],

    // Common
    'failed' => 'Bukti kelayakan ini tidak sepadan dengan rekod kami.',
    'password' => 'Kata laluan yang diberikan tidak betul.',
    'throttle' => 'Terlalu banyak percubaan log masuk. Sila cuba lagi dalam :seconds saat.',
    'reset_link_sent' => 'Kami telah menghantar pautan tetapan semula kata laluan ke e-mel anda!',
    'reset_success' => 'Kata laluan anda telah ditetapkan semula!',
    'reset_token_invalid' => 'Token tetapan semula kata laluan ini tidak sah.',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'Selamat pagi',
        'afternoon' => 'Selamat tengah hari',
        'evening' => 'Selamat petang',
        'night' => 'Selamat malam',
    ],

    // Welcome messages
    'welcome_message' => ':greeting, :name! Selamat kembali!',
    'registration_message' => ':greeting, :name! Selamat datang ke platform kami!',
];
