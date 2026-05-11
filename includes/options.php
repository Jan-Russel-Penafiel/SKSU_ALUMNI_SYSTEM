<?php
// =============================================================
// Shared Select Options
// =============================================================

function app_course_groups(): array {
    return [
        'Engineering and Computing' => [
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Computer Science (BSCS)',
            'Bachelor of Science in Information Systems (BSIS)',
            'Bachelor of Science in Computer Engineering (BSCpE)',
            'Bachelor of Science in Civil Engineering (BSCE)',
        ],
        'Education and Technology' => [
            'Bachelor in Technical Teacher Education (BTTE)',
        ],
        'Industrial Technology Programs' => [
            'Bachelor in Industrial Technology major in Architectural Drafting Technology',
            'Bachelor in Industrial Technology major in Automotive Technology',
            'Bachelor in Industrial Technology major in Civil Technology',
            'Bachelor in Industrial Technology major in Electrical Technology',
            'Bachelor in Industrial Technology major in Food Technology',
            'Bachelor in Industrial Technology major in Electronics Technology',
        ],
    ];
}

function app_course_options(): array {
    $courses = [];
    foreach (app_course_groups() as $groupCourses) {
        foreach ($groupCourses as $course) {
            $courses[] = $course;
        }
    }
    return $courses;
}

function app_department_options(): array {
    return ['CCS', 'ESO', 'NABA'];
}

function app_payment_type_options(): array {
    return ['Yearbook Fee', 'Graduation Fee', 'Donation', 'Other'];
}

function app_payment_method_options(): array {
    return ['Cash', 'GCash', 'Bank Transfer'];
}

function app_payment_status_options(): array {
    return ['pending', 'paid', 'rejected', 'refunded'];
}

function app_is_valid_option(string $value, array $options): bool {
    return in_array($value, $options, true);
}
