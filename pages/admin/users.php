<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$role_filter = $_GET['role'] ?? 'all';
$q = trim($_GET['q'] ?? '');

$conds = ['1'];
$types = ''; $params = [];
if ($role_filter !== 'all') { $conds[] = 'role=?'; $types .= 's'; $params[] = $role_filter; }
if ($q) { $conds[] = '(full_name LIKE ? OR email LIKE ?)'; $types .= 'ss'; $params[] = "%$q%"; $params[] = "%$q%"; }
$where = implode(' AND ', $conds);

$rows = db_select($conn, "SELECT * FROM users WHERE $where ORDER BY id DESC", $types, $params);

$page_title = 'User Management';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>User Management</h1>
      <p class="subtitle">Manage system accounts across all roles.</p>
    </div>
    <button type="button"
            class="btn-primary"
            data-modal-open="userModal"
            data-fill='<?= e(json_encode([
              "__title" => "Create new user",
              "id" => 0,
              "full_name" => "",
              "email" => "",
              "role" => "student",
              "contact" => "",
              "address" => "",
              "status" => "active",
              "password" => ""
            ])) ?>'>
      <?= icon('plus','w-4 h-4') ?>
      New user
    </button>
  </div>

  <form class="card flex gap-3 flex-wrap items-end">
    <div class="min-w-[160px]">
      <label class="label">Role</label>
      <select name="role" class="input">
        <?php foreach (['all','student','registrar','alumni','admin'] as $r): ?>
          <option <?= $role_filter===$r?'selected':'' ?>><?= $r ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="flex-1 min-w-[220px]">
      <label class="label">Search</label>
      <input name="q" value="<?= e($q) ?>" class="input" placeholder="Search by name or email">
    </div>
    <div class="flex gap-2">
      <button class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        Apply filters
      </button>
      <a href="?role=all&q=" class="btn-ghost">Reset</a>
    </div>
  </form>

  <div class="mt-6 table-wrap overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Created</th>
          <th class="pr-6 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $u):
          $roleColors = [
            'admin'     => 'badge-crimson',
            'registrar' => 'badge-info',
            'alumni'    => 'badge-success',
            'student'   => 'badge-neutral',
          ];
          $rb = $roleColors[$u['role']] ?? 'badge-neutral';
          $editFill = json_encode([
            '__title'   => 'Edit user',
            'id'        => (int)$u['id'],
            'full_name' => $u['full_name'],
            'email'     => $u['email'],
            'role'      => $u['role'],
            'contact'   => $u['contact'] ?? '',
            'address'   => $u['address'] ?? '',
            'status'    => $u['status'],
            'password'  => '',
          ]);
        ?>
          <tr>
            <td class="pl-6 font-mono text-xs text-ink-500"><?= (int)$u['id'] ?></td>
            <td class="font-semibold text-ink-900"><?= e($u['full_name']) ?></td>
            <td class="text-ink-600"><?= e($u['email']) ?></td>
            <td><span class="badge <?= $rb ?> capitalize"><?= e($u['role']) ?></span></td>
            <td><?= status_badge($u['status']) ?></td>
            <td class="text-xs text-ink-500"><?= fmt_date($u['created_at']) ?></td>
            <td class="pr-6 text-right">
              <div class="inline-flex items-center gap-1">
                <button type="button"
                        class="text-xs font-semibold text-sky-700 hover:bg-sky-50 px-2 py-1 rounded-md"
                        data-modal-open="userModal"
                        data-fill='<?= e($editFill) ?>'>Edit</button>
                <a href="<?= APP_URL ?>/actions/admin_toggle_user.php?id=<?= (int)$u['id'] ?>" class="text-xs font-semibold text-amber-700 hover:bg-amber-50 px-2 py-1 rounded-md" data-confirm="Toggle status?">Toggle</a>
                <?php if ((int)$u['id'] !== current_user_id()): ?>
                  <a href="<?= APP_URL ?>/actions/admin_delete_user.php?id=<?= (int)$u['id'] ?>" class="text-xs font-semibold text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-md" data-confirm="Delete this user permanently?">Delete</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('users','w-5 h-5') ?></div>
            <div>No users match your filters.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- User Create / Edit Modal -->
<div id="userModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="userModalTitle">
  <div class="modal-panel modal-lg" role="document">
    <div class="modal-head">
      <div>
        <h3 id="userModalTitle" data-modal-title>Create new user</h3>
        <div class="modal-sub">Account details &mdash; assign role and access status.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/admin_save_user.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="0">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="label">Full name</label><input type="text" name="full_name" class="input" required></div>
        <div><label class="label">Email</label><input type="email" name="email" class="input" required></div>
        <div><label class="label">Role</label>
          <select name="role" class="input" required>
            <?php foreach (['student','registrar','alumni','admin'] as $r): ?>
              <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="label">Contact</label><input type="text" name="contact" class="input"></div>
        <div><label class="label">Status</label>
          <select name="status" class="input">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="sm:col-span-2"><label class="label">Address</label><input type="text" name="address" class="input"></div>
        <div class="sm:col-span-2">
          <label class="label">Password <span class="text-ink-400 font-normal">(leave blank to keep current when editing)</span></label>
          <input type="password" name="password" class="input" minlength="6" autocomplete="new-password">
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('check','w-4 h-4') ?>
          Save user
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
