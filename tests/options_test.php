<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/options.php';

function assert_true(bool $condition, string $message): void {
    if (!$condition) {
        fwrite(STDERR, "Assertion failed: {$message}\n");
        exit(1);
    }
}

$expectedCourses = [
    'Bachelor of Science in Information Technology (BSIT)',
    'Bachelor of Science in Computer Science (BSCS)',
    'Bachelor of Science in Information Systems (BSIS)',
    'Bachelor of Science in Computer Engineering (BSCpE)',
    'Bachelor of Science in Civil Engineering (BSCE)',
    'Bachelor in Technical Teacher Education (BTTE)',
    'Bachelor in Industrial Technology major in Architectural Drafting Technology',
    'Bachelor in Industrial Technology major in Automotive Technology',
    'Bachelor in Industrial Technology major in Civil Technology',
    'Bachelor in Industrial Technology major in Electrical Technology',
    'Bachelor in Industrial Technology major in Food Technology',
    'Bachelor in Industrial Technology major in Electronics Technology',
];

$groups = app_course_groups();
$courses = app_course_options();
$departments = app_department_options();
$paymentStatuses = app_payment_status_options();

assert_true(array_keys($groups) === [
    'Engineering and Computing',
    'Education and Technology',
    'Industrial Technology Programs',
], 'course groups use the approved headings');

foreach ($expectedCourses as $course) {
    assert_true(in_array($course, $courses, true), "course option exists: {$course}");
}

assert_true(count($courses) === count($expectedCourses), 'only approved course options are returned');
assert_true($departments === ['CCS', 'ESO', 'NABA'], 'department options are fixed to CCS, ESO, NABA');
assert_true($paymentStatuses === ['pending', 'paid', 'rejected', 'refunded'], 'payment statuses support pending review and disapproval');
assert_true(app_is_valid_option($courses[0], $courses), 'valid option helper accepts listed values');
assert_true(!app_is_valid_option('BS Business Administration', $courses), 'valid option helper rejects removed course values');

echo "Option tests passed.\n";
