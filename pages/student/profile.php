<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$uid = current_user_id();
$user = current_user();
$student = db_select_one($conn, "SELECT * FROM students WHERE user_id=?", 'i', [$uid]);

$page_title = 'My Profile';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
  <p class="text-sm text-gray-500">Update your personal and academic information.</p>

  <form action="<?= APP_URL ?>/actions/student_update_profile.php" method="post" class="mt-6 card grid sm:grid-cols-2 gap-4">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="sm:col-span-2"><label class="label">Full Name</label><input type="text" name="full_name" class="input" value="<?= e($user['full_name']) ?>" required></div>
    <div><label class="label">Email</label><input type="email" name="email" class="input" value="<?= e($user['email']) ?>" required></div>
    <div><label class="label">Contact</label><input type="text" name="contact" class="input" value="<?= e($user['contact']) ?>"></div>
    <div class="sm:col-span-2"><label class="label">Address</label><input type="text" name="address" class="input" value="<?= e($user['address']) ?>"></div>
    <div><label class="label">Student ID</label><input type="text" class="input bg-gray-100" value="<?= e($student['student_id'] ?? '') ?>" disabled></div>
    <div><label class="label">Course</label>
      <select name="course" class="input" required>
        <option value="">Select course</option>
        <?php foreach (app_course_groups() as $group => $courses): ?>
          <optgroup label="<?= e($group) ?>">
            <?php foreach ($courses as $courseOption): ?>
              <option value="<?= e($courseOption) ?>" <?= ($student['course'] ?? '') === $courseOption ? 'selected' : '' ?>><?= e($courseOption) ?></option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Year Level</label><input type="text" name="year_level" class="input" value="<?= e($student['year_level'] ?? '') ?>"></div>
    <div><label class="label">Department</label>
      <select name="department" class="input">
        <option value="">Select department</option>
        <?php foreach (app_department_options() as $departmentOption): ?>
          <option value="<?= e($departmentOption) ?>" <?= ($student['department'] ?? '') === $departmentOption ? 'selected' : '' ?>><?= e($departmentOption) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Academic Year</label><input type="text" name="academic_year" class="input" value="<?= e($student['academic_year'] ?? '') ?>"></div>
    <div><label class="label">Expected Graduation</label><input type="date" name="expected_graduation" class="input" value="<?= e($student['expected_graduation'] ?? '') ?>"></div>
    <div class="sm:col-span-2 border-t pt-4">
      <label class="label">New Password (leave blank to keep current)</label>
      <input type="password" name="password" class="input" minlength="6">
    </div>
    <div class="sm:col-span-2"><button class="btn-primary">Save Changes</button></div>
  </form>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
