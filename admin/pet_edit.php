<?php
define('ADMIN_GUARD_LOGIN_PATH', '../login.php');
require_once __DIR__ . '/../include/admin_guard.php';
require_once __DIR__ . '/../include/admin_csrf.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../include/pet_add_log.php';

$allowedStatus = ['待领养', '已领养', 'pending', 'adopted', 'available'];
$allowedGender = ['male', 'female', 'M', 'F', '雄', '雌', 'Male', 'Female'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$errors = [];
$row = [
    'pet_name' => '',
    'pet_breed' => '',
    'pet_age' => '',
    'pet_gender' => '',
    'pet_health' => '',
    'pet_status' => '待领养',
    'pet_intro' => '',
    'pet_img' => '',
];

if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT pet_name, pet_breed, pet_age, pet_gender, pet_health, pet_status, pet_intro, pet_img FROM pets WHERE pet_id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $a, $b, $c, $d, $e, $f, $g, $h);
        if (mysqli_stmt_fetch($stmt)) {
            $row = [
                'pet_name' => (string) $a,
                'pet_breed' => (string) $b,
                'pet_age' => (string) $c,
                'pet_gender' => (string) $d,
                'pet_health' => (string) $e,
                'pet_status' => (string) $f,
                'pet_intro' => (string) $g,
                'pet_img' => (string) $h,
            ];
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify()) {
        $errors[] = 'Invalid security token.';
    }

    $id = isset($_POST['pet_id']) ? (int) $_POST['pet_id'] : 0;
    $dbPetImg = $row['pet_img'];
    $name = isset($_POST['pet_name']) ? trim((string) $_POST['pet_name']) : '';
    $breed = isset($_POST['pet_breed']) ? trim((string) $_POST['pet_breed']) : '';
    $age = isset($_POST['pet_age']) ? trim((string) $_POST['pet_age']) : '';
    $gender = isset($_POST['pet_gender']) ? trim((string) $_POST['pet_gender']) : '';
    $health = isset($_POST['pet_health']) ? trim((string) $_POST['pet_health']) : '';
    $status = isset($_POST['pet_status']) ? trim((string) $_POST['pet_status']) : '';
    $intro = isset($_POST['pet_intro']) ? trim((string) $_POST['pet_intro']) : '';
    $imgUrl = isset($_POST['pet_img_url']) ? trim((string) $_POST['pet_img_url']) : '';

    if ($name === '' || strlen($name) > 50) {
        $errors[] = 'Name is required (max 50 characters).';
    }
    if (strlen($breed) > 50) {
        $errors[] = 'Breed must be at most 50 characters.';
    }
    if (strlen($age) > 20) {
        $errors[] = 'Age must be at most 20 characters.';
    }
    if ($gender === '' || !in_array($gender, $allowedGender, true)) {
        $errors[] = 'Please choose a valid gender.';
    }
    if (strlen($health) > 50) {
        $errors[] = 'Health notes must be at most 50 characters.';
    }
    if (!in_array($status, $allowedStatus, true)) {
        $errors[] = 'Please choose a valid listing status.';
    }
    if (strlen($intro) > 20000) {
        $errors[] = 'Introduction is too long.';
    }

    $finalImg = $id > 0 ? (string) $dbPetImg : '';
    if ($imgUrl !== '') {
        if (preg_match('#^https?://#i', $imgUrl) || (strlen($imgUrl) > 0 && $imgUrl[0] === '/')) {
            $finalImg = $imgUrl;
        } else {
            $errors[] = 'Image URL must start with http(s):// or /.';
        }
    }

    if (!empty($_FILES['pet_image']['name']) && (int) $_FILES['pet_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ((int) $_FILES['pet_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed (error code ' . (int) $_FILES['pet_image']['error'] . ').';
        } else {
            $maxBytes = 2 * 1024 * 1024;
            if ((int) $_FILES['pet_image']['size'] > $maxBytes) {
                $errors[] = 'Image must be 2 MB or smaller.';
            } else {
                $ext = strtolower(pathinfo((string) $_FILES['pet_image']['name'], PATHINFO_EXTENSION));
                $map = ['jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'gif' => 'gif', 'webp' => 'webp'];
                if (!isset($map[$ext])) {
                    $errors[] = 'Image must be JPG, PNG, GIF, or WebP.';
                } else {
                    $dir = realpath(__DIR__ . '/../assets/img/uploads/pets');
                    if ($dir === false) {
                        $dir = __DIR__ . '/../assets/img/uploads/pets';
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        $dir = realpath($dir) ?: $dir;
                    }
                    $basename = 'pet_' . bin2hex(random_bytes(8)) . '.' . $map[$ext];
                    $target = (is_string($dir) ? $dir : __DIR__ . '/../assets/img/uploads/pets') . '/' . $basename;
                    if (!move_uploaded_file($_FILES['pet_image']['tmp_name'], $target)) {
                        $errors[] = 'Could not save uploaded image.';
                    } else {
                        $finalImg = '/assets/img/uploads/pets/' . $basename;
                    }
                }
            }
        }
    }

    if ($errors === []) {
        if (!pet_add_db_ready()) {
            $errors[] = 'Database is not connected. Please try again later.';
            pet_add_log_add((int) ($_SESSION['user_id'] ?? 0), 0, $name, 'failed', 'db_not_connected');
        }
    }

    if ($errors === []) {
        $adminId = (int) $_SESSION['user_id'];
        if ($id > 0) {
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE pets SET pet_name=?, pet_breed=?, pet_age=?, pet_gender=?, pet_health=?, pet_status=?, pet_intro=?, pet_img=?, admin_id=? WHERE pet_id=? LIMIT 1'
            );
            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    'ssssssssii',
                    $name,
                    $breed,
                    $age,
                    $gender,
                    $health,
                    $status,
                    $intro,
                    $finalImg,
                    $adminId,
                    $id
                );
                mysqli_stmt_execute($stmt);
                $updated = mysqli_stmt_affected_rows($stmt) >= 0;
                mysqli_stmt_close($stmt);
                if (!$updated) {
                    pet_add_log_add($adminId, $id, $name, 'failed', 'update_failed');
                }
            } else {
                pet_add_log_add($adminId, $id, $name, 'failed', 'update_prepare_failed');
            }
        } else {
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO pets (pet_name, pet_breed, pet_age, pet_gender, pet_health, pet_status, pet_intro, pet_img, admin_id) VALUES (?,?,?,?,?,?,?,?,?)'
            );
            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    'ssssssssi',
                    $name,
                    $breed,
                    $age,
                    $gender,
                    $health,
                    $status,
                    $intro,
                    $finalImg,
                    $adminId
                );
                $inserted = mysqli_stmt_execute($stmt);
                $newPetId = $inserted ? (int) mysqli_insert_id($conn) : 0;
                mysqli_stmt_close($stmt);
                if ($inserted) {
                    pet_add_log_add($adminId, $newPetId, $name, 'success', 'pet_created');
                } else {
                    pet_add_log_add($adminId, 0, $name, 'failed', 'insert_failed');
                }
            } else {
                pet_add_log_add($adminId, 0, $name, 'failed', 'insert_prepare_failed');
            }
        }
        $_SESSION['admin_flash_ok'] = $id > 0 ? 'Pet updated.' : 'Pet created.';
        header('Location: pets.php');
        exit;
    }

    $row = [
        'pet_name' => $name,
        'pet_breed' => $breed,
        'pet_age' => $age,
        'pet_gender' => $gender,
        'pet_health' => $health,
        'pet_status' => $status,
        'pet_intro' => $intro,
        'pet_img' => $imgUrl !== '' ? $imgUrl : $dbPetImg,
    ];
}

$pageTitle = $id > 0 ? 'Edit pet' : 'Add pet';
$adminNav = 'pets';
require_once __DIR__ . '/../include/admin_header.php';
?>

<h1 class="admin-page-title"><?php echo $id > 0 ? 'Edit pet' : 'Add pet'; ?></h1>
<p class="admin-lead">Fields are validated against database limits. Images: JPG/PNG/GIF/WebP, max 2 MB.</p>

<?php if ($errors !== []) : ?>
    <div class="admin-flash err">
        <?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <form method="post" enctype="multipart/form-data" class="admin-form-grid">
        <?php echo admin_csrf_field(); ?>
        <?php if ($id > 0) : ?>
            <input type="hidden" name="pet_id" value="<?php echo (int) $id; ?>">
        <?php endif; ?>

        <div class="admin-field">
            <label for="pet_name">Name *</label>
            <input id="pet_name" name="pet_name" type="text" required maxlength="50" value="<?php echo htmlspecialchars($row['pet_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="admin-field">
            <label for="pet_breed">Breed</label>
            <input id="pet_breed" name="pet_breed" type="text" maxlength="50" value="<?php echo htmlspecialchars($row['pet_breed'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="admin-field">
            <label for="pet_age">Age (display text)</label>
            <input id="pet_age" name="pet_age" type="text" maxlength="20" placeholder="e.g. 2 years" value="<?php echo htmlspecialchars($row['pet_age'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="admin-field">
            <label for="pet_gender">Gender *</label>
            <select id="pet_gender" name="pet_gender" required>
                <?php
                foreach ($allowedGender as $val) {
                    $sel = (strcasecmp((string) $row['pet_gender'], $val) === 0) ? ' selected' : '';
                    echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="pet_health">Health</label>
            <input id="pet_health" name="pet_health" type="text" maxlength="50" value="<?php echo htmlspecialchars($row['pet_health'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="admin-field">
            <label for="pet_status">Listing status *</label>
            <select id="pet_status" name="pet_status" required>
                <?php
                $sopts = ['待领养' => 'Listed (待领养)', '已领养' => 'Adopted (已领养)', 'pending' => 'pending (legacy)', 'adopted' => 'adopted', 'available' => 'available'];
                foreach ($sopts as $val => $lab) {
                    $sel = ($row['pet_status'] === $val) ? ' selected' : '';
                    echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($lab, ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="pet_intro">Introduction</label>
            <textarea id="pet_intro" name="pet_intro" maxlength="20000"><?php echo htmlspecialchars($row['pet_intro'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="admin-field">
            <label for="pet_img_url">Image URL (optional if you upload a file)</label>
            <input id="pet_img_url" name="pet_img_url" type="text" placeholder="https://... or /assets/img/..." value="<?php echo htmlspecialchars($row['pet_img'], ENT_QUOTES, 'UTF-8'); ?>">
            <p class="hint">If you upload a file below, it replaces this URL.</p>
        </div>
        <div class="admin-field">
            <label for="pet_image">Upload image</label>
            <input id="pet_image" name="pet_image" type="file" accept="image/jpeg,image/png,image/gif,image/webp,.jpg,.jpeg,.png,.gif,.webp">
        </div>

        <div class="admin-actions">
            <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            <a class="admin-btn admin-btn-ghost" href="pets.php">Cancel</a>
        </div>
    </form>
</div>

    </div>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>
