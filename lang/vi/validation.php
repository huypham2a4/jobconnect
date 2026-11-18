<?php

/*
|--------------------------------------------------------------------------
| Thông báo kiểm tra dữ liệu (tiếng Việt)
|--------------------------------------------------------------------------
| Các controller đã có thông báo riêng cho những lỗi hay gặp. File này dùng
| cho những quy tắc còn lại để người dùng không gặp câu báo lỗi tiếng Anh.
| Quy tắc không có trong file sẽ lấy bản tiếng Anh mặc định của Laravel.
*/

return [
    'accepted' => ':Attribute phải được chấp nhận.',
    'after' => ':Attribute phải sau :date.',
    'after_or_equal' => ':Attribute phải từ :date trở đi.',
    'array' => ':Attribute không hợp lệ.',
    'before' => ':Attribute phải trước :date.',
    'before_or_equal' => ':Attribute phải trước hoặc bằng :date.',
    'boolean' => ':Attribute chỉ nhận giá trị đúng hoặc sai.',
    'confirmed' => 'Xác nhận :attribute không trùng khớp.',
    'date' => ':Attribute không phải ngày hợp lệ.',
    'different' => ':Attribute phải khác :other.',
    'distinct' => ':Attribute bị trùng lặp.',
    'email' => ':Attribute phải là địa chỉ email hợp lệ.',
    'exists' => ':Attribute đã chọn không hợp lệ.',
    'file' => ':Attribute phải là một tệp tin.',
    'gt' => [
        'numeric' => ':Attribute phải lớn hơn :value.',
    ],
    'gte' => [
        'numeric' => ':Attribute phải lớn hơn hoặc bằng :value.',
    ],
    'in' => ':Attribute đã chọn không hợp lệ.',
    'integer' => ':Attribute phải là số nguyên.',
    'max' => [
        'array' => ':Attribute không được quá :max mục.',
        'file' => ':Attribute không được lớn hơn :max KB.',
        'numeric' => ':Attribute không được lớn hơn :max.',
        'string' => ':Attribute không được dài quá :max ký tự.',
    ],
    'mimes' => ':Attribute phải có định dạng: :values.',
    'min' => [
        'array' => ':Attribute phải có ít nhất :min mục.',
        'file' => ':Attribute phải từ :min KB trở lên.',
        'numeric' => ':Attribute phải từ :min trở lên.',
        'string' => ':Attribute phải có ít nhất :min ký tự.',
    ],
    'numeric' => ':Attribute phải là số.',
    'regex' => ':Attribute không hợp lệ.',
    'required' => 'Vui lòng nhập :attribute.',
    'required_if' => 'Vui lòng nhập :attribute.',
    'string' => ':Attribute phải là chuỗi ký tự.',
    'unique' => ':Attribute đã tồn tại.',
    'uploaded' => 'Tải lên :attribute không thành công.',
    'url' => ':Attribute không phải đường dẫn hợp lệ.',

    'custom' => [],

    'attributes' => [
        'name' => 'họ và tên',
        'email' => 'email',
        'password' => 'mật khẩu',
        'current_password' => 'mật khẩu hiện tại',
        'phone' => 'số điện thoại',
        'role' => 'vai trò',
        'bio' => 'giới thiệu bản thân',
        'company_name' => 'tên công ty',
        'city' => 'tỉnh/thành phố',
        'address' => 'địa chỉ',
        'website' => 'website',
        'size' => 'quy mô',
        'description' => 'mô tả',
        'title' => 'tiêu đề',
        'category_id' => 'ngành nghề',
        'location_id' => 'địa điểm',
        'type' => 'hình thức làm việc',
        'experience_level' => 'kinh nghiệm',
        'salary_min' => 'lương tối thiểu',
        'salary_max' => 'lương tối đa',
        'salary_negotiable' => 'lương thỏa thuận',
        'deadline' => 'hạn nộp hồ sơ',
        'requirements' => 'yêu cầu công việc',
        'benefits' => 'quyền lợi',
        'skills' => 'kỹ năng',
        'skills.*' => 'kỹ năng',
        'cv_option' => 'cách chọn CV',
        'existing_cv_id' => 'CV có sẵn',
        'cv_file' => 'tệp CV',
        'cover_letter' => 'thư ngỏ',
        'status' => 'trạng thái',
        'interview_date' => 'thời gian phỏng vấn',
        'interview_type' => 'hình thức phỏng vấn',
        'location_or_link' => 'địa điểm hoặc đường dẫn phỏng vấn',
        'note' => 'ghi chú',
        'rejection_note' => 'lý do từ chối',
        'extend_days' => 'số ngày gia hạn',
        'icon' => 'biểu tượng',
        'bg_class' => 'màu nền',
        'permissions' => 'danh sách quyền',
        'permissions.*' => 'quyền',
        'token' => 'mã đặt lại mật khẩu',
        'q' => 'từ khóa',
    ],
];
