<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$uid = current_user_id();
$user = current_user();
$alumni = db_select_one($conn, "SELECT * FROM alumni WHERE user_id=?", 'i', [$uid]);

$page_title = 'Alumni Profile';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">My Alumni Profile</h1>
  <p class="text-sm text-gray-500">Keep your information up to date for tracer monitoring.</p>

  <form action="<?= APP_URL ?>/actions/alumni_update_profile.php" method="post" class="mt-6 card grid sm:grid-cols-2 gap-4">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="sm:col-span-2"><label class="label">Full Name</label><input type="text" name="full_name" class="input" value="<?= e($user['full_name']) ?>" required></div>
    <div><label class="label">Email</label><input type="email" name="email" class="input" value="<?= e($user['email']) ?>" required></div>
    <div><label class="label">Contact</label><input type="text" name="contact" class="input" value="<?= e($user['contact']) ?>"></div>
    <div class="sm:col-span-2"><label class="label">Address</label><input type="text" name="address" class="input" value="<?= e($user['address']) ?>"></div>

    <div class="sm:col-span-2 border-t pt-4"><h3 class="font-bold text-gray-900">Employment Information</h3></div>
    <div><label class="label">Employment Status</label>
      <select name="employment_status" class="input">
        <?php foreach (['Employed','Unemployed','Self-Employed','Further Studies'] as $o): ?>
          <option <?= ($alumni['employment_status']??'')===$o?'selected':'' ?>><?= $o ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Company</label><input type="text" name="company_name" class="input" value="<?= e($alumni['company_name']??'') ?>"></div>
    <div><label class="label">Job Title</label><input type="text" name="job_title" class="input" value="<?= e($alumni['job_title']??'') ?>"></div>
    <div><label class="label">Industry</label><input type="text" name="industry" class="input" value="<?= e($alumni['industry']??'') ?>"></div>
    <div class="sm:col-span-2"><label class="label">Work Address</label><input type="text" name="work_address" class="input" value="<?= e($alumni['work_address']??'') ?>"></div>
    <div><label class="label">Monthly Income (₱)</label><input type="number" step="0.01" name="monthly_income" class="input" value="<?= e($alumni['monthly_income']??'') ?>"></div>
    <div class="sm:col-span-2"><label class="label">Career Achievements</label><textarea name="career_achievements" rows="3" class="input"><?= e($alumni['career_achievements']??'') ?></textarea></div>

    <div class="sm:col-span-2 border-t pt-4">
      <label class="label">New Password (leave blank to keep current)</label>
      <input type="password" name="password" class="input" minlength="6">
    </div>
    <div class="sm:col-span-2"><button class="btn-primary">Save Changes</button></div>
  </form>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
