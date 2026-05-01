<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'My applications — Pet Adoption Platform';
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/include/pet_image.php';

$rows = [];
$uid = (int) $_SESSION['user_id'];

$sql = 'SELECT a.app_id, a.app_status, a.create_time,
               p.pet_id, p.pet_name, p.pet_breed, p.pet_age, p.pet_gender, p.pet_img
        FROM adoption_application a
        LEFT JOIN pets p ON p.pet_id = a.pet_id
        WHERE a.user_id = ?
        ORDER BY a.app_id DESC';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $uid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $appId, $appStatus, $ctime, $petId, $petName, $petBreed, $petAge, $petGender, $petImg);
    while (mysqli_stmt_fetch($stmt)) {
        $status = strtolower(trim((string) $appStatus));
        $isHistory = in_array($status, ['approved', 'rejected', 'cancelled'], true);
        $progress = 45;
        $statusText = 'Submitted';
        $nextStep = 'Your application is in queue for manual review.';

        if ($status === 'pending') {
            $progress = 45;
            $statusText = 'Pending review';
            $nextStep = 'Our team usually reviews applications within 1-2 business days.';
        } elseif ($status === 'review' || $status === 'in_review') {
            $progress = 70;
            $statusText = 'In review';
            $nextStep = 'We are currently reviewing your profile and home readiness details.';
            $isHistory = false;
        } elseif ($status === 'approved') {
            $progress = 100;
            $statusText = 'Approved';
            $nextStep = 'Great news! Please wait for staff follow-up to complete the adoption process.';
        } elseif ($status === 'rejected') {
            $progress = 100;
            $statusText = 'Rejected';
            $nextStep = 'This request was not approved. You can browse and apply for another pet.';
        } elseif ($status === 'cancelled') {
            $progress = 100;
            $statusText = 'Cancelled';
            $nextStep = 'This application was cancelled.';
        }

        $rows[] = [
            'app_id' => (int) $appId,
            'app_status' => $status,
            'status_text' => $statusText,
            'progress' => $progress,
            'is_history' => $isHistory ? 1 : 0,
            'next_step' => $nextStep,
            'create_time' => $ctime !== null ? (string) $ctime : '',
            'pet_id' => (int) $petId,
            'pet_name' => $petName !== null ? (string) $petName : '',
            'pet_breed' => $petBreed !== null ? (string) $petBreed : '',
            'pet_age' => $petAge !== null ? (string) $petAge : '',
            'pet_gender' => $petGender !== null ? (string) $petGender : '',
            'pet_img' => pet_image_url(
                $petName !== null ? (string) $petName : '',
                $petImg !== null ? (string) $petImg : ''
            ),
        ];
    }
    mysqli_stmt_close($stmt);
}

$appsJson = json_encode($rows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
if ($appsJson === false) {
    $appsJson = '[]';
}

include 'header.php';
?>
<link rel="stylesheet" href="assets/css/my-applications.css">

<main class="pet-main my-apps-page">
    <div class="pet-container">
        <section class="pet-page-hero my-apps-hero">
            <p class="eyebrow">My adoption journey</p>
            <h1>My applications</h1>
            <p class="lead">Track every request status in one place and stay ready for the next step.</p>
        </section>

        <div class="my-app-tabs" role="tablist" aria-label="Application groups">
            <button type="button" class="my-app-tab is-active" id="tab-ongoing" data-tab="ongoing" aria-selected="true">Ongoing applications</button>
            <button type="button" class="my-app-tab" id="tab-history" data-tab="history" aria-selected="false">Application history</button>
        </div>

        <div id="apps-list" class="my-app-grid"></div>

        <section id="apps-empty" class="my-app-empty" hidden>
            <p id="apps-empty-text">No applications found.</p>
            <a href="browse.php" class="pet-btn">Browse adoptable pets</a>
        </section>
    </div>
</main>

<script type="application/json" id="my-apps-data"><?php echo $appsJson; ?></script>
<script>
(function () {
    'use strict';

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    var raw = document.getElementById('my-apps-data');
    var apps = [];
    try {
        apps = raw ? JSON.parse(raw.textContent || '[]') : [];
    } catch (e) {
        apps = [];
    }

    var tab = 'ongoing';

    function statusCls(s) {
        if (s === 'approved') return 'is-approved';
        if (s === 'rejected') return 'is-rejected';
        if (s === 'cancelled') return 'is-cancelled';
        if (s === 'review' || s === 'in_review') return 'is-review';
        return 'is-pending';
    }

    function render() {
        var list = document.getElementById('apps-list');
        var empty = document.getElementById('apps-empty');
        var emptyText = document.getElementById('apps-empty-text');
        if (!list || !empty || !emptyText) return;

        var filtered = apps.filter(function (a) {
            return tab === 'ongoing' ? Number(a.is_history) !== 1 : Number(a.is_history) === 1;
        });

        if (filtered.length === 0) {
            list.innerHTML = '';
            empty.hidden = false;
            emptyText.textContent = tab === 'ongoing'
                ? 'You have no ongoing applications yet.'
                : 'No historical applications yet.';
            return;
        }

        empty.hidden = true;
        list.innerHTML = filtered.map(function (a) {
            var meta = [a.pet_breed, a.pet_age, a.pet_gender].filter(Boolean).join(' · ');
            var createdAt = a.create_time || '';
            var pct = Math.max(0, Math.min(100, Number(a.progress) || 0));
            return '' +
                '<article class="my-app-card">' +
                    '<img class="my-app-img" src="' + esc(a.pet_img) + '" alt="' + esc(a.pet_name || 'Pet') + '">' +
                    '<div class="my-app-body">' +
                        '<h2 class="my-app-name">' + esc(a.pet_name || ('Pet #' + a.pet_id)) + '</h2>' +
                        '<p class="my-app-meta">' + esc(meta) + '</p>' +
                        '<span class="my-app-status ' + esc(statusCls(a.app_status)) + '">' + esc(a.status_text || a.app_status) + '</span>' +
                        '<div class="my-app-progress"><span style="width:' + pct + '%"></span></div>' +
                        '<p class="my-app-step">' + esc(a.next_step || '') + '</p>' +
                        '<p class="my-app-date">Submitted: ' + esc(createdAt) + '</p>' +
                        '<div class="my-app-actions">' +
                            '<a class="my-app-btn" href="browse.php">View pet listings</a>' +
                        '</div>' +
                    '</div>' +
                '</article>';
        }).join('');
    }

    document.querySelectorAll('.my-app-tab').forEach(function (b) {
        b.addEventListener('click', function () {
            tab = b.getAttribute('data-tab') || 'ongoing';
            document.querySelectorAll('.my-app-tab').forEach(function (x) {
                var on = x === b;
                x.classList.toggle('is-active', on);
                x.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            render();
        });
    });

    render();
})();
</script>

<?php include 'footer.php'; ?>
