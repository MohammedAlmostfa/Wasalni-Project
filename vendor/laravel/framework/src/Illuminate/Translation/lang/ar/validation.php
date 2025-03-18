<?php

return [
    /*
|--------------------------------------------------------------------------
| Authentication Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used during authentication for various
| messages that we need to display to the user. You are free to modify
| these language lines according to your application's requirements.
|
*/


    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other يساوي :value.',
    'active_url' => ':attribute غير رابط صحيح.',
    'after' => ':attribute يجب أن يكون بعد :date.',
    'after_or_equal' => ':attribute يجب أن يكون بعد أو يساوي :date.',
    'alpha' => ':attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => ':attribute يجب أن يحتوي على أحرف، أرقام، وشرطات فقط.',
    'alpha_num' => ':attribute يجب أن يحتوي على أحرف وأرقام فقط.',
    'array' => ':attribute يجب أن يكون مصفوفة.',
    'ascii' => ':attribute يجب أن يحتوي على أحرف ورموز أحادية البايت فقط.',
    'before' => ':attribute يجب أن يكون قبل :date.',
    'before_or_equal' => ':attribute يجب أن يكون قبل أو يساوي :date.',
    'between' => [
        'array' => ':attribute يجب أن يحتوي على بين :min و :max عناصر.',
        'file' => ':attribute يجب أن يكون حجمه بين :min و :max كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون بين :min و :max.',
        'string' => ':attribute يجب أن يكون بين :min و :max أحرف.',
    ],
    'boolean' => ':attribute يجب أن يكون صح أو خطأ.',
    'can' => ':attribute يحتوي على قيمة غير مسموح بها.',
    'confirmed' => 'التأكيد لـ :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => ':attribute يجب أن يكون تاريخ صالح.',
    'date_equals' => ':attribute يجب أن يكون نفس تاريخ :date.',
    'date_format' => ':attribute يجب أن يتطابق مع الصيغة :format.',
    'decimal' => ':attribute يجب أن يحتوي على :decimal منازل عشرية.',
    'declined' => ':attribute يجب رفضه.',
    'declined_if' => ':attribute يجب رفضه عندما يكون :other يساوي :value.',
    'different' => ':attribute و :other يجب أن يكونا مختلفين.',
    'digits' => ':attribute يجب أن يحتوي على :digits أرقام.',
    'digits_between' => ':attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions' => ':attribute يحتوي على أبعاد صورة غير صحيحة.',
    'distinct' => ':attribute يحتوي على قيمة مكررة.',
    'doesnt_end_with' => ':attribute يجب ألا ينتهي بأحد القيم التالية: :values.',
    'doesnt_start_with' => ':attribute يجب ألا يبدأ بأحد القيم التالية: :values.',
    'email' => 'البريد الإلكتروني غير صحيح.',
    'ends_with' => ':attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'exists' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'extensions' => ':attribute يجب أن يحتوي على أحد الامتدادات التالية: :values.',
    'file' => ':attribute يجب أن يكون ملف.',
    'filled' => ':attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => ':attribute يجب أن يحتوي على أكثر من :value عناصر.',
        'file' => ':attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون أكبر من :value.',
        'string' => ':attribute يجب أن يكون أطول من :value أحرف.',
    ],
    'gte' => [
        'array' => ':attribute يجب أن يحتوي على :value عناصر أو أكثر.',
        'file' => ':attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون أكبر من أو يساوي :value.',
        'string' => ':attribute يجب أن يكون أطول من أو يساوي :value أحرف.',
    ],
    'hex_color' => ':attribute يجب أن يكون لون سداسي صحيح.',
    'image' => ':attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'in_array' => ':attribute يجب أن يكون موجود في :other.',
    'integer' => ':attribute يجب أن يكون عدد صحيح.',
    'ip' => ':attribute يجب أن يكون عنوان IP صالح.',
    'ipv4' => ':attribute يجب أن يكون عنوان IPv4 صالح.',
    'ipv6' => ':attribute يجب أن يكون عنوان IPv6 صالح.',
    'json' => ':attribute يجب أن يكون نص JSON صالح.',
    'lowercase' => ':attribute يجب أن يكون بأحرف صغيرة.',
    'lt' => [
        'array' => ':attribute يجب أن يحتوي على أقل من :value عناصر.',
        'file' => ':attribute يجب أن يكون أصغر من :value كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون أصغر من :value.',
        'string' => ':attribute يجب أن يكون أقصر من :value أحرف.',
    ],
    'lte' => [
        'array' => ':attribute يجب ألا يحتوي على أكثر من :value عناصر.',
        'file' => ':attribute يجب أن يكون أصغر من أو يساوي :value كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون أصغر من أو يساوي :value.',
        'string' => ':attribute يجب أن يكون أقصر من أو يساوي :value أحرف.',
    ],
    'mac_address' => ':attribute يجب أن يكون عنوان MAC صالح.',
    'max' => [
        'array' => ':attribute يجب ألا يحتوي على أكثر من :max عناصر.',
        'file' => ':attribute يجب ألا يتجاوز :max كيلوبايت.',
        'numeric' => ':attribute يجب ألا يتجاوز :max.',
        'string' => ':attribute يجب ألا يتجاوز :max أحرف.',
    ],
    'max_digits' => ':attribute يجب ألا يحتوي على أكثر من :max أرقام.',
    'mimes' => ':attribute يجب أن يكون من نوع: :values.',
    'mimetypes' => ':attribute يجب أن يكون من نوع: :values.',
    'min' => [
        'array' => ':attribute يجب أن يحتوي على الأقل :min عناصر.',
        'file' => ':attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون على الأقل :min.',
        'string' => ':attribute يجب أن يكون على الأقل :min أحرف.',
    ],
    'min_digits' => ':attribute يجب أن يحتوي على الأقل :min أرقام.',
    'missing' => ':attribute يجب أن يكون غير موجود.',
    'missing_if' => ':attribute يجب أن يكون غير موجود عندما يكون :other يساوي :value.',
    'missing_unless' => ':attribute يجب أن يكون غير موجود إلا إذا كان :other يساوي :value.',
    'missing_with' => ':attribute يجب أن يكون غير موجود عندما تكون :values موجودة.',
    'missing_with_all' => ':attribute يجب أن يكون غير موجود عندما تكون جميع :values موجودة.',
    'multiple_of' => ':attribute يجب أن يكون مضاعف لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'not_regex' => 'صيغة :attribute غير صحيحة.',
    'numeric' => ':attribute يجب أن يكون رقم.',
    'password' => [
        'letters' => ':attribute يجب أن يحتوي على حرف واحد على الأقل.',
        'mixed' => ':attribute يجب أن يحتوي على حرف كبير وصغير على الأقل.',
        'numbers' => ':attribute يجب أن يحتوي على رقم واحد على الأقل.',
        'symbols' => ':attribute يجب أن يحتوي على رمز واحد على الأقل.',
        'uncompromised' => ':attribute تم تسريبه في اختراق بيانات. اختر قيمة أخرى.',
    ],
    'present' => ':attribute يجب أن يكون موجود.',
    'present_if' => ':attribute يجب أن يكون موجود عندما يكون :other يساوي :value.',
    'present_unless' => ':attribute يجب أن يكون موجود إلا إذا كان :other يساوي :value.',
    'present_with' => ':attribute يجب أن يكون موجود عندما تكون :values موجودة.',
    'present_with_all' => ':attribute يجب أن يكون موجود عندما تكون جميع :values موجودة.',
    'prohibited' => ':attribute ممنوع.',
    'prohibited_if' => ':attribute ممنوع عندما يكون :other يساوي :value.',
    'prohibited_unless' => ':attribute ممنوع إلا إذا كان :other موجود في :values.',
    'prohibits' => ':attribute يمنع وجود :other.',
    'regex' => 'صيغة :attribute غير صحيحة.',
    'required' => ':attribute مطلوب.',
    'required_array_keys' => ':attribute يجب أن يحتوي على قيم لـ :values.',
    'required_if' => ':attribute مطلوب عندما يكون :other يساوي :value.',
    'required_if_accepted' => ':attribute مطلوب عندما يتم قبول :other.',
    'required_unless' => ':attribute مطلوب إلا إذا كان :other موجودًا في :values.',
    'required_with' => ':attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all' => ':attribute مطلوب عندما تكون جميع :values موجودة.',
    'required_without' => ':attribute مطلوب عندما لا تكون :values موجودة.',
    'required_without_all' => ':attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'same' => ':attribute يجب أن يتطابق مع :other.',
    'size' => [
        'array' => ':attribute يجب أن يحتوي على :size عناصر.',
        'file' => ':attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => ':attribute يجب أن يكون :size.',
        'string' => ':attribute يجب أن يكون :size أحرف.',
    ],
    'starts_with' => ':attribute يجب أن يبدأ بأحد القيم التالية: :values.',
    'string' => ':attribute يجب أن يكون نص.',
    'timezone' => ':attribute يجب أن يكون منطقة زمنية صحيحة.',
    'unique' => ':attribute تم استخدامه من قبل.',
    'uploaded' => ':attribute فشل في التحميل.',
    'uppercase' => ':attribute يجب أن يكون بأحرف كبيرة.',
    'url' => ':attribute يجب أن يكون رابط صالح.',
    'ulid' => ':attribute يجب أن يكون ULID صحيح.',
    'uuid' => ':attribute يجب أن يكون UUID صحيح.',

    /*
    |----------------------------------------------------------------------
    | Custom Validation Language Lines
    |----------------------------------------------------------------------
    |
    | يمكنك تخصيص الرسائل للسمات باستخدام الاسم "attribute.rule".
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Custom Validation Attributes
    |----------------------------------------------------------------------
    |
    | هذه السطور تستخدم لتبديل اسم السمة بقيمة أكثر وضوحًا للمستخدم.
    |
    */

    'attributes' => [
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'gender' => 'الجنس',
        'birthday' => 'تاريخ الميلاد',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
        'country_id' => 'الدولة',
        'city_name' => 'المدينة',
        'country_name' => 'اسم الدولة',
        'trip_id' => 'الرحلة',
        'status' => 'الحالة',
        'seats_number' => 'عدد المقاعد',
        'user_id' => 'المستخدم',
        'favorite_user_id' => 'المستخدم المفضل',
        'booking_id' => 'الحجز',
        'rate' => 'التقييم',
        'review' => 'المراجعة',
        'description' => 'الوصف',
        'trip_start' => 'بداية الرحلة',
        'from' => 'من',
        'to' => 'إلى',
        'seat_price' => 'سعر المقعد',
        'available_seats' => 'المقاعد المتاحة',
        'code' => 'رمز التحقق',
        'googleToken' => 'رمز الوصول من غوغل'
    ],

];
