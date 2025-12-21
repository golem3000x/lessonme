<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'သင့်အကောင့်သို့ဝင်ရောက်ပါ',
        'or_register' => 'သို့မဟုတ်',
        'register_link' => 'အကောင့်အသစ်ဖန်တီးပါ',
        'email_label' => 'အီးမေးလ်လိပ်စာ',
        'email_placeholder' => 'အီးမေးလ်လိပ်စာ',
        'password_label' => 'စကားဝှက်',
        'password_placeholder' => 'စကားဝှက်',
        'remember_me' => 'ကျွန်ုပ်ကိုမှတ်ထားပါ',
        'forgot_password' => 'စကားဝှက်မေ့သွားပြီလား?',
        'submit' => 'ဝင်ရောက်ရန်',
        'or_continue_with' => 'သို့မဟုတ် ဆက်လက်ဝင်ရောက်ပါ',
        'sign_in_with_zalo' => 'Zalo ဖြင့်ဝင်ရောက်ပါ',
        'sign_in_with_facebook' => 'Facebook ဖြင့်ဝင်ရောက်ပါ',
    ],

    // Register page
    'register' => [
        'title' => 'အကောင့်အသစ်ဖန်တီးပါ',
        'or_login' => 'သို့မဟုတ်',
        'login_link' => 'သင့်အကောင့်သို့ဝင်ရောက်ပါ',
        'name_label' => 'နာမည်အပြည့်အစုံ',
        'name_placeholder' => 'သင့်နာမည်အပြည့်အစုံ',
        'email_label' => 'အီးမေးလ်လိပ်စာ',
        'email_placeholder' => 'အီးမေးလ်လိပ်စာ',
        'date_of_birth_label' => 'မွေးသက္ကရာဇ်',
        'password_label' => 'စကားဝှက်',
        'password_placeholder' => 'အနည်းဆုံး စာလုံး ၈ လုံး',
        'password_confirmation_label' => 'စကားဝှက်အတည်ပြုရန်',
        'password_confirmation_placeholder' => 'စကားဝှက်ထပ်ရိုက်ပါ',
        'submit' => 'အကောင့်ဖန်တီးရန်',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'သင့်မိခင်ဘာသာစကားကိုရွေးချယ်ပါ',
        'description' => 'သင့်အတွေ့အကြုံကို ပုဂ္ဂိုလ်ရေးအရ ချိန်ညှိရန် သင့်မိခင်ဘာသာစကားကို ရွေးချယ်ပါ။',
        'native_lang_label' => 'မိခင်ဘာသာစကား',
        'select_language' => 'ဘာသာစကားရွေးချယ်ပါ...',
        'submit' => 'ဆက်လက်ရန်',
        'info_text' => 'သင်၏ပရိုဖိုင်ဆက်တင်များတွင် ဘာသာစကားရွေးချယ်မှုကို အချိန်မရွေးပြောင်းလဲနိုင်သည်။',
    ],

    // Role selection page
    'role_selection' => [
        'title' => 'သင့်အခန်းကဏ္ဍကိုရွေးချယ်ပါ',
        'description' => 'ကျွန်ုပ်တို့၏ပလက်ဖောင်းကို သင်မည်သို့အသုံးပြုလိုသည်ကို ရွေးချယ်ပါ။ ဆရာနှင့် ကျောင်းသားနှစ်မျိုးလုံးဖြစ်နိုင်သည်။',
        'select_roles' => 'သင့်အခန်းကဏ္ဍ(များ)ကိုရွေးချယ်ပါ',
        'teacher' => 'ဆရာ/ဆရာမ',
        'teacher_description' => 'သင်ခန်းစာများဖန်တီးပြီး ကျောင်းသားများကို တာဝန်ပေးပါ',
        'student' => 'ကျောင်းသား/ကျောင်းသူ',
        'student_description' => 'အပြန်အလှန်သင်ခန်းစာများဖြင့် ဘာသာစကားများလေ့လာပါ',
        'teaching_languages' => 'သင်သင်ကြားသောဘာသာစကားများ',
        'select_languages' => 'ဘာသာစကားများရွေးချယ်ပါ',
        'learning_languages' => 'သင်လေ့လာလိုသောဘာသာစကားများ',
        'submit' => 'စာရင်းသွင်းမှုပြီးဆုံးရန်',
        'info_text' => 'သင်၏ပရိုဖိုင်ဆက်တင်များတွင် အခန်းကဏ္ဍနှင့် ဘာသာစကားရွေးချယ်မှုများကို အချိန်မရွေးပြင်ဆင်နိုင်သည်။',
        'at_least_one_role' => 'အနည်းဆုံး အခန်းကဏ္ဍတစ်ခုရွေးချယ်ပါ။',
        'teaching_lang_required' => 'သင်သင်ကြားသော ဘာသာစကားအနည်းဆုံးတစ်ခုရွေးချယ်ပါ။',
        'learning_lang_required' => 'သင်လေ့လာလိုသော ဘာသာစကားတစ်ခုရွေးချယ်ပါ။',
    ],

    // Password reset
    'forgot_password' => [
        'title' => 'စကားဝှက်မေ့သွားပြီလား?',
        'description' => 'ပြဿနာမရှိပါ။ သင့်အီးမေးလ်လိပ်စာကို ပြောပြပါ၊ စကားဝှက်ပြန်လည်သတ်မှတ်ရန် လင့်ခ်ကို အီးမေးလ်ပို့ပေးပါမည်။',
        'email_label' => 'အီးမေးလ်လိပ်စာ',
        'email_placeholder' => 'အီးမေးလ်လိပ်စာ',
        'submit' => 'စကားဝှက်ပြန်လည်သတ်မှတ်ရန်လင့်ခ်ပို့ပါ',
        'back_to_login' => 'ဝင်ရောက်ရန်သို့ပြန်သွားပါ',
    ],

    'reset_password' => [
        'title' => 'စကားဝှက်ပြန်လည်သတ်မှတ်ရန်',
        'email_label' => 'အီးမေးလ်လိပ်စာ',
        'email_placeholder' => 'အီးမေးလ်လိပ်စာ',
        'password_label' => 'စကားဝှက်အသစ်',
        'password_placeholder' => 'အနည်းဆုံး စာလုံး ၈ လုံး',
        'password_confirmation_label' => 'စကားဝှက်အတည်ပြုရန်',
        'password_confirmation_placeholder' => 'စကားဝှက်ထပ်ရိုက်ပါ',
        'submit' => 'စကားဝှက်ပြန်လည်သတ်မှတ်ရန်',
    ],

    // Common
    'failed' => 'ဤအထောက်အထားများသည် ကျွန်ုပ်တို့၏မှတ်တမ်းနှင့် မကိုက်ညီပါ။',
    'password' => 'ပေးထားသောစကားဝှက်မှားယွင်းပါသည်။',
    'throttle' => 'ဝင်ရောက်ရန် အကြိမ်ရေများလွန်းသည်။ :seconds စက္ကန့်အတွင်း ထပ်မံကြိုးစားပါ။',
    'reset_link_sent' => 'သင့်စကားဝှက်ပြန်လည်သတ်မှတ်ရန်လင့်ခ်ကို အီးမေးလ်ပို့ပြီးပါပြီ!',
    'reset_success' => 'သင့်စကားဝှက်ကို ပြန်လည်သတ်မှတ်ပြီးပါပြီ!',
    'reset_token_invalid' => 'ဤစကားဝှက်ပြန်လည်သတ်မှတ်ရန် တိုကင်သည် မမှန်ကန်ပါ။',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'မင်္ဂလာနံနက်ခင်းပါ',
        'afternoon' => 'မင်္ဂလာနေ့လည်ခင်းပါ',
        'evening' => 'မင်္ဂလာညနေခင်းပါ',
        'night' => 'မင်္ဂလာညချမ်းပါ',
    ],

    // Welcome messages
    'welcome_message' => ':greeting၊ :name! ပြန်လည်ကြိုဆိုပါသည်!',
    'registration_message' => ':greeting၊ :name! ကျွန်ုပ်တို့၏ပလက်ဖောင်းသို့ ကြိုဆိုပါသည်!',
];
