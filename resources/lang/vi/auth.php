<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'Đăng nhập vào tài khoản',
        'or_register' => 'Hoặc',
        'register_link' => 'tạo tài khoản mới',
        'email_label' => 'Địa chỉ email',
        'email_placeholder' => 'Địa chỉ email',
        'password_label' => 'Mật khẩu',
        'password_placeholder' => 'Mật khẩu',
        'remember_me' => 'Ghi nhớ đăng nhập',
        'forgot_password' => 'Quên mật khẩu?',
        'submit' => 'Đăng nhập',
        'or_continue_with' => 'Hoặc tiếp tục với',
        'sign_in_with_zalo' => 'Đăng nhập với Zalo',
        'sign_in_with_facebook' => 'Đăng nhập với Facebook',
    ],

    // Register page
    'register' => [
        'title' => 'Đăng ký tài khoản',
        'or_login' => 'Hoặc',
        'login_link' => 'đăng nhập vào tài khoản',
        'name_label' => 'Họ và tên',
        'name_placeholder' => 'Tên của bạn',
        'email_label' => 'Địa chỉ email',
        'email_placeholder' => 'Địa chỉ email',
        'password_label' => 'Mật khẩu',
        'password_placeholder' => 'Tối thiểu 8 ký tự',
        'password_confirmation_label' => 'Xác nhận mật khẩu',
        'password_confirmation_placeholder' => 'Nhập lại mật khẩu',
        'submit' => 'Tạo tài khoản',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'Chọn Ngôn Ngữ Mẹ Đẻ',
        'description' => 'Vui lòng chọn ngôn ngữ mẹ đẻ của bạn để cá nhân hóa trải nghiệm.',
        'native_lang_label' => 'Ngôn ngữ mẹ đẻ',
        'select_language' => 'Chọn ngôn ngữ...',
        'submit' => 'Hoàn Tất Đăng Ký',
        'info_text' => 'Bạn có thể thay đổi tùy chọn ngôn ngữ bất cứ lúc nào trong cài đặt hồ sơ.',
    ],

    // Password reset
    'forgot_password' => [
        'title' => 'Quên mật khẩu?',
        'description' => 'Không vấn đề gì. Hãy cho chúng tôi biết địa chỉ email của bạn và chúng tôi sẽ gửi liên kết đặt lại mật khẩu.',
        'email_label' => 'Địa chỉ email',
        'email_placeholder' => 'Địa chỉ email',
        'submit' => 'Gửi liên kết đặt lại mật khẩu',
        'back_to_login' => 'Quay lại đăng nhập',
    ],

    'reset_password' => [
        'title' => 'Đặt lại mật khẩu',
        'email_label' => 'Địa chỉ email',
        'email_placeholder' => 'Địa chỉ email',
        'password_label' => 'Mật khẩu mới',
        'password_placeholder' => 'Tối thiểu 8 ký tự',
        'password_confirmation_label' => 'Xác nhận mật khẩu',
        'password_confirmation_placeholder' => 'Nhập lại mật khẩu',
        'submit' => 'Đặt lại mật khẩu',
    ],

    // Common
    'failed' => 'Thông tin đăng nhập không chính xác.',
    'password' => 'Mật khẩu không đúng.',
    'throttle' => 'Quá nhiều lần đăng nhập. Vui lòng thử lại sau :seconds giây.',
    'reset_link_sent' => 'Chúng tôi đã gửi liên kết đặt lại mật khẩu qua email!',
    'reset_success' => 'Mật khẩu của bạn đã được đặt lại!',
    'reset_token_invalid' => 'Liên kết đặt lại mật khẩu không hợp lệ.',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'Chào buổi sáng',
        'afternoon' => 'Chào buổi chiều',
        'evening' => 'Chào buổi tối',
        'night' => 'Chúc ngủ ngon',
    ],

    // Welcome messages
    'welcome_message' => ':greeting, :name! Chào mừng bạn trở lại!',
    'registration_message' => ':greeting, :name! Chào mừng bạn đến với nền tảng của chúng tôi!',
];
