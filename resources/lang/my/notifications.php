<?php

declare(strict_types=1);

return [
    'topic_approved_subject' => 'သင့်ခေါင်းစဉ်ကို အတည်ပြုပြီးပါပြီ!',
    'topic_approved_body' => '":topic" ခေါင်းစဉ်သည် စစ်ဆေးမှုအောင်မြင်ပြီး ယခုရရှိနိုင်ပါပြီ။',
    'topic_approved_db' => 'ခေါင်းစဉ်သည် စစ်ဆေးမှုအောင်မြင်ပြီး စာရင်းတွင် မြင်နိုင်ပါပြီ။',
    'view_topics' => 'ခေါင်းစဉ်များကြည့်ရှုပါ',

    'topic_rejected_subject' => 'သင့်ခေါင်းစဉ်ကို ပယ်ချခဲ့သည်',
    'topic_rejected_body' => '":topic" ခေါင်းစဉ်သည် စစ်ဆေးမှုမအောင်မြင်ပါ။',
    'topic_rejected_reason' => 'ခေါင်းစဉ်ကို ပြင်ဆင်ပြီး ထပ်မံကြိုးစားပါ။',
    'topic_rejected_db' => 'ခေါင်းစဉ်ကို စစ်ဆေးမှုတွင် ပယ်ချခဲ့သည်။',
    'thanks' => 'နားလည်မှုအတွက် ကျေးဇူးတင်ပါသည်!',

    // Creation notifications
    'topic_creation_pending' => 'သင့်ခေါင်းစဉ် ":slug" ကို စစ်ဆေးမှုအတွက် တင်သွင်းပြီးပါပြီ',
    'keyword_creation_pending' => 'သင့်သော့ချက်စကားလုံး ":slug" ကို စစ်ဆေးမှုအတွက် တင်သွင်းပြီးပါပြီ',

    // Moderation notifications for topics
    'topic_moderation_approved' => 'သင့်ခေါင်းစဉ် ":slug" ကို အတည်ပြုပြီးပါပြီ!',
    'topic_moderation_rejected' => 'သင့်ခေါင်းစဉ် ":slug" ကို စစ်ဆေးမှုတွင် ပယ်ချခဲ့သည်',
    'topic_moderation_invalid' => 'သင့်ခေါင်းစဉ် ":slug" ကို စီမံဆောင်ရွက်ရာတွင် အမှားဖြစ်ခဲ့သည်',

    // Moderation notifications for keywords
    'keyword_moderation_approved' => 'သင့်သော့ချက်စကားလုံး ":slug" ကို အတည်ပြုပြီးပါပြီ!',
    'keyword_moderation_rejected' => 'သင့်သော့ချက်စကားလုံး ":slug" ကို စစ်ဆေးမှုတွင် ပယ်ချခဲ့သည်',
    'keyword_moderation_invalid' => 'သင့်သော့ချက်စကားလုံး ":slug" ကို စီမံဆောင်ရွက်ရာတွင် အမှားဖြစ်ခဲ့သည်',

    // Translation notifications for topics
    'topic_translate_successfully' => 'သင့်ခေါင်းစဉ် ":slug" ကို အောင်မြင်စွာဘာသာပြန်ပြီး ယခုမြင်နိုင်ပါပြီ!',
    'topic_translate_invalid' => 'သင့်ခေါင်းစဉ် ":slug" အတွက် ဘာသာပြန်မှုအမှား',

    // Translation notifications for keywords
    'keyword_translate_successfully' => 'သင့်သော့ချက်စကားလုံး ":slug" ကို အောင်မြင်စွာဘာသာပြန်ပြီး ယခုမြင်နိုင်ပါပြီ!',
    'keyword_translate_invalid' => 'သင့်သော့ချက်စကားလုံး ":slug" အတွက် ဘာသာပြန်မှုအမှား',

    // Auth notifications
    'user_auth_welcome' => 'ပြန်လည်ကြိုဆိုပါသည်၊ :name!',
    'user_auth_registered' => 'ကျွန်ုပ်တို့၏ပလက်ဖောင်းသို့ ကြိုဆိုပါသည်၊ :name!',

    // Lesson creation notifications
    'lesson_creation_processing' => 'သင်ခန်းစာဖန်တီးမှု လုပ်ဆောင်နေသည်...',
    'lesson_creation_successfully' => '":name" သင်ခန်းစာကို အောင်မြင်စွာဖန်တီးပြီးပါပြီ!',
    'lesson_creation_error' => 'သင်ခန်းစာဖန်တီးမှု မအောင်မြင်ပါ။ ထပ်မံကြိုးစားပါ။',
    'lesson_creation_exists' => 'အလားတူသင်ခန်းစာ ":name" ရှိပြီးသားဖြစ်သည်။',

    // Lesson regeneration notifications
    'lesson_regeneration_processing' => 'သင်ခန်းစာပြန်လည်ဖန်တီးမှု လုပ်ဆောင်နေသည်...',
    'lesson_regeneration_successfully' => '":name" သင်ခန်းစာကို အောင်မြင်စွာပြန်လည်ဖန်တီးပြီးပါပြီ!',
    'lesson_regeneration_error' => 'သင်ခန်းစာပြန်လည်ဖန်တီးမှု မအောင်မြင်ပါ။ ထပ်မံကြိုးစားပါ။',
    'lesson_regeneration_exists' => '":name" သင်ခန်းစာ ရှိပြီးသားဖြစ်သည်။',

    // Lesson assignment notifications
    'lesson_assigned' => ':teacher က သင့်ကို သင်ခန်းစာတာဝန်ပေးခဲ့သည်: ":lesson"',
    'lesson_assignment_sent' => '":lesson" သင်ခန်းစာကို ကျောင်းသား :count ဦးသို့ တာဝန်ပေးခဲ့သည်',
    'lesson_unassigned' => ':teacher က သင့်ကို သင်ခန်းစာမှ ဖယ်ရှားခဲ့သည်: ":lesson"',
    'lesson_assignment_removed' => 'ကျောင်းသား :student ကို ":lesson" သင်ခန်းစာမှ ဖယ်ရှားခဲ့သည်',

    // Course assignment notifications
    'course_assigned' => ':teacher က သင့်ကို သင်တန်းတာဝန်ပေးခဲ့သည်: ":course"',
    'course_assignment_sent' => '":course" သင်တန်းကို ကျောင်းသား :count ဦးသို့ တာဝန်ပေးခဲ့သည်',
    'course_unassigned' => ':teacher က သင့်ကို သင်တန်းမှ ဖယ်ရှားခဲ့သည်: ":course"',
    'course_assignment_removed' => 'ကျောင်းသား :student ကို ":course" သင်တန်းမှ ဖယ်ရှားခဲ့သည်',

    // Notification Bell
    'title' => 'အသိပေးချက်များ',
    'notifications' => 'အသိပေးချက်များ',
    'mark_all_read' => 'အားလုံးဖတ်ပြီးအဖြစ် မှတ်သားပါ',
    'mark_read' => 'ဖတ်ပြီးအဖြစ် မှတ်သားပါ',
    'delete' => 'ဖျက်ရန်',
    'no_notifications' => 'အသိပေးချက်များမရှိသေးပါ',
    'all_caught_up' => 'အားလုံးဖတ်ပြီးပါပြီ!',
    'previous' => 'ယခင်',
    'next' => 'နောက်',
    'page' => 'စာမျက်နှာ',
];
