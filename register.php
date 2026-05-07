<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (is_logged_in()) redirect('pages/' . current_role() . '/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name  = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contact    = trim($_POST['contact'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $course     = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $expected   = $_POST['expected_graduation'] ?? null;
    $password   = $_POST['password'] ?? '';
    $password2  = $_POST['password2'] ?? '';

    $errors = [];
    if (!$full_name || !$email || !$student_id || !$course || !$year_level || !$password) {
        $errors[] = 'All required fields must be filled.';
    }
    if ($password !== $password2) $errors[] = 'Passwords do not match.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    $exists = db_select_one($conn, "SELECT id FROM users WHERE email=?", 's', [$email]);
    if ($exists) $errors[] = 'Email is already registered.';
    $sid_exists = db_select_one($conn, "SELECT id FROM students WHERE student_id=?", 's', [$student_id]);
    if ($sid_exists) $errors[] = 'Student ID is already registered.';

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $uid = db_execute($conn,
            "INSERT INTO users (full_name,email,password,role,contact,address) VALUES (?,?,?,?,?,?)",
            'ssssss', [$full_name, $email, $hashed, 'student', $contact, $address]);
        if ($uid) {
            db_execute($conn,
                "INSERT INTO students (user_id,student_id,course,year_level,department,academic_year,expected_graduation) VALUES (?,?,?,?,?,?,?)",
                'issssss', [$uid, $student_id, $course, $year_level, $department, $academic_year, $expected]);
            flash('success', 'Account created. You may now log in.');
            redirect('login.php');
        }
        $errors[] = 'Failed to create account.';
    }
    foreach ($errors as $e) flash('error', $e);
}

$page_title = 'Register';
include __DIR__ . '/templates/header.php';
?>
<main class="flex-1 flex items-center justify-center px-4 py-10">
  <div class="w-full max-w-2xl">
    <div class="bg-white rounded-2xl shadow-card border border-ink-200 p-8 relative overflow-hidden">
      <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-crimson-600 via-crimson-700 to-crimson-900"></div>
      <div class="flex items-center gap-3 mb-1">
        <div class="bg-gradient-to-br from-crimson-600 to-crimson-800 text-white rounded-xl w-11 h-11 flex items-center justify-center shadow-soft">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        <div>
          <h2 class="text-xl font-bold text-ink-900 tracking-tight">Student Registration</h2>
          <p class="text-xs text-ink-500">For graduating students of SKSU Isulan Campus</p>
        </div>
      </div>
      <div class="my-5 h-px bg-ink-200"></div>
      <form method="post" class="grid sm:grid-cols-2 gap-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="sm:col-span-2"><label class="label">Full name *</label><input type="text" name="full_name" class="input" required></div>
        <div><label class="label">Email *</label><input type="email" name="email" class="input" required></div>
        <div><label class="label">Contact No.</label><input type="text" name="contact" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Address</label><input type="text" name="address" class="input"></div>
        <div><label class="label">Student ID *</label><input type="text" name="student_id" class="input" required placeholder="e.g. 2021-12345"></div>
        <div><label class="label">Course *</label>
          <select name="course" class="input" required>
            <option value="">Select course</option>
            <option>BS Information Technology</option>
            <option>BS Computer Science</option>
            <option>BS Business Administration</option>
            <option>BS Education</option>
            <option>BS Agriculture</option>
            <option>BS Criminology</option>
            <option>BS Nursing</option>
            <option>BS Engineering</option>
          </select>
        </div>
        <div><label class="label">Year Level *</label>
          <select name="year_level" class="input" required>
            <option value="">Select</option><option>1st</option><option>2nd</option><option>3rd</option><option>4th</option><option>5th</option>
          </select>
        </div>
        <div><label class="label">Department</label><input type="text" name="department" class="input" placeholder="e.g. CCS"></div>
        <div><label class="label">Academic Year</label><input type="text" name="academic_year" class="input" placeholder="2025-2026"></div>
        <div><label class="label">Expected Graduation</label><input type="date" name="expected_graduation" class="input"></div>
        <div><label class="label">Password *</label><input type="password" name="password" class="input" required minlength="6"></div>
        <div><label class="label">Confirm Password *</label><input type="password" name="password2" class="input" required minlength="6"></div>
        <div class="sm:col-span-2"><button class="btn-primary w-full">Create Account</button></div>
      </form>
      <p class="mt-4 text-sm text-center text-ink-600">Already have an account? <a href="<?= APP_URL ?>/login.php" class="text-crimson-700 font-semibold hover:underline">Sign in</a></p>
    </div>
  </div>
</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
