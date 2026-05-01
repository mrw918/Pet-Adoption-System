<?php
$pageTitle = 'Manage pets';
$adminNav = 'pets';
require_once __DIR__ . '/../include/admin_header.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../include/pet_image.php';

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$total = 0;
$tr = mysqli_query($conn, 'SELECT COUNT(*) AS c FROM pets');
if ($tr) {
    $rw = mysqli_fetch_assoc($tr);
    $total = (int) ($rw['c'] ?? 0);
    mysqli_free_result($tr);
}
$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$rows = [];
$sql = 'SELECT pet_id, pet_name, pet_breed, pet_status, pet_img, create_time FROM pets ORDER BY pet_id DESC LIMIT ? OFFSET ?';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $pname, $pbreed, $pstatus, $pimg, $ctime);
    while (mysqli_stmt_fetch($stmt)) {
        $name = (string) $pname;
        $rawImg = (string) $pimg;
        $rows[] = [
            'pet_id' => (int) $pid,
            'pet_name' => $name,
            'pet_breed' => (string) $pbreed,
            'pet_status' => (string) $pstatus,
            'pet_img' => pet_image_url($name, $rawImg),
            'create_time' => $ctime !== null ? (string) $ctime : '',
        ];
    }
    mysqli_stmt_close($stmt);
}

if (!empty($_SESSION['admin_flash_ok'])) {
    echo '<div class="admin-flash ok">' . htmlspecialchars((string) $_SESSION['admin_flash_ok'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_ok']);
}
if (!empty($_SESSION['admin_flash_err'])) {
    echo '<div class="admin-flash err">' . htmlspecialchars((string) $_SESSION['admin_flash_err'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['admin_flash_err']);
}
?>

<h1 class="admin-page-title">Pets</h1>
<p class="admin-lead"><?php echo (int) $total; ?> total · page <?php echo (int) $page; ?> of <?php echo (int) $totalPages; ?></p>

<div class="admin-panel">
    <div class="admin-actions" style="margin-bottom:1rem;">
        <a class="admin-btn admin-btn-primary" href="pet_edit.php">Add pet</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Breed</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []) : ?>
                    <tr><td colspan="7">No pets yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $r) : ?>
                        <tr>
                            <td><?php echo (int) $r['pet_id']; ?></td>
                            <td>
                                <?php if ($r['pet_img'] !== '') : ?>
                                    <img class="thumb" src="<?php echo htmlspecialchars($r['pet_img'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($r['pet_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['pet_breed'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['pet_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($r['create_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a class="admin-btn admin-btn-ghost" href="pet_edit.php?id=<?php echo (int) $r['pet_id']; ?>">Edit</a>
                                <form class="inline-form" method="post" action="pet_delete.php" onsubmit="return confirm('Delete this pet and its applications?');">
                                    <?php echo admin_csrf_field(); ?>
                                    <input type="hidden" name="pet_id" value="<?php echo (int) $r['pet_id']; ?>">
                                    <button type="submit" class="admin-btn admin-btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1) : ?>
        <div class="admin-pagination">
            <?php if ($page > 1) : ?>
                <a href="pets.php?page=<?php echo $page - 1; ?>">Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <?php if ($i === $page) : ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="pets.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages) : ?>
                <a href="pets.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

    </div>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>
