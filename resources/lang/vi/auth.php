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
        'first_name_label' => 'Tên',
        'first_name_placeholder' => 'Tên của bạn',
        'last_name_label' => 'Họ',
        'last_name_placeholder' => 'Họ của bạn',
        'email_label' => 'Địa chỉ email',
        'email_placeholder' => 'Địa chỉ email',
        'date_of_birth_label' => 'Ngày sinh',
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
        'submit' => 'Tiếp Tục',
        'info_text' => 'Bạn có thể thay đổi tùy chọn ngôn ngữ bất cứ lúc nào trong cài đặt hồ sơ.',
    ],

    // Role selection page
    'role_selection' => [
        'title' => 'Chọn Vai Trò Của Bạn',
        'description' => 'Chọn cách bạn muốn sử dụng nền tảng của chúng tôi. Bạn có thể vừa là giáo viên vừa là học sinh.',
        'select_roles' => 'Chọn Vai Trò',
        'teacher' => 'Giáo viên',
        'teacher_description' => 'Tạo bài học và giao cho học sinh',
        'student' => 'Học sinh',
        'student_description' => 'Học ngôn ngữ qua các bài học tương tác',
        'teaching_languages' => 'Ngôn Ngữ Bạn Dạy',
        'select_languages' => 'Chọn ngôn ngữ',
        'learning_languages' => 'Ngôn Ngữ Bạn Muốn Học',
        'submit' => 'Hoàn Tất Đăng Ký',
        'info_text' => 'Bạn có thể cập nhật vai trò và tùy chọn ngôn ngữ bất cứ lúc nào trong cài đặt hồ sơ.',
        'at_least_one_role' => 'Vui lòng chọn ít nhất một vai trò.',
        'teaching_lang_required' => 'Vui lòng chọn ít nhất một ngôn ngữ bạn dạy.',
        'learning_lang_required' => 'Vui lòng chọn ngôn ngữ bạn muốn học.',
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
