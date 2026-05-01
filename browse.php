<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page = 'browse';
$pageTitle = 'Browse pets — Pet Adoption Platform';
require_once __DIR__ . '/include/pets_data.php';

$pets = pets_fetch_all_for_browse();
$petsJson = json_encode(
    $pets,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE
);
if ($petsJson === false) {
    $petsJson = '[]';
}

require_once __DIR__ . '/include/browse_csrf.php';
$contactEmail = '';
$contactPhone = '';
$uid = (int) ($_SESSION['user_id'] ?? 0);
if ($uid > 0) {
    $contactSqlCandidates = [
        'SELECT user_email, user_phone FROM users WHERE user_id = ? LIMIT 1',
        'SELECT email, phone FROM users WHERE user_id = ? LIMIT 1',
        'SELECT user_email, user_phone FROM users WHERE id = ? LIMIT 1',
        'SELECT email, phone FROM users WHERE id = ? LIMIT 1',
    ];
    foreach ($contactSqlCandidates as $q) {
        $uStmt = mysqli_prepare($conn, $q);
        if (!$uStmt) {
            continue;
        }
        mysqli_stmt_bind_param($uStmt, 'i', $uid);
        mysqli_stmt_execute($uStmt);
        mysqli_stmt_bind_result($uStmt, $uEmail, $uPhone);
        if (mysqli_stmt_fetch($uStmt)) {
            $contactEmail = $uEmail !== null ? trim((string) $uEmail) : '';
            $contactPhone = $uPhone !== null ? trim((string) $uPhone) : '';
            mysqli_stmt_close($uStmt);
            break;
        }
        mysqli_stmt_close($uStmt);
    }
}

$browseCfgJson = json_encode(
    [
        'csrf' => browse_csrf_token(),
        'userEmail' => $contactEmail,
        'userPhone' => $contactPhone,
    ],
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE
);
if ($browseCfgJson === false) {
    $browseCfgJson = '{"csrf":"","userEmail":"","userPhone":""}';
}

include 'header.php';
?>
<link rel="stylesheet" href="assets/css/pet-browse.css">

<main class="pet-main pet-browse">
    <div class="browse-toolbar">
        <div class="browse-toolbar-inner">
            <p class="browse-welcome">Hello, <strong><?php echo htmlspecialchars((string) ($_SESSION['username'] ?? 'friend'), ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <div class="browse-search-wrap">
                <label class="visually-hidden" for="searchInput">Search pets</label>
                <input type="search" id="searchInput" class="browse-search" placeholder="Search by name, breed, or traits…" autocomplete="off">
            </div>
            <button type="button" class="browse-reset" id="resetFilters">Reset filters</button>
        </div>
    </div>

    <div class="pet-container browse-body">
        <section class="browse-status-split" aria-label="Adoption listing scope">
            <div class="browse-status-split-grid" role="group" aria-label="Filter by adoption outcome">
                <button type="button" class="browse-status-tile" data-status-tile="available" aria-pressed="false">
                    <span class="browse-status-tile-title">Still looking for a home</span>
                    <span class="browse-status-tile-sub">Not yet adopted — pets waiting to meet you</span>
                </button>
                <button type="button" class="browse-status-tile" data-status-tile="adopted" aria-pressed="false">
                    <span class="browse-status-tile-title">Already in a loving home</span>
                    <span class="browse-status-tile-sub">Adopted — celebrate their new chapter</span>
                </button>
            </div>
            <button type="button" class="browse-status-all" id="statusShowAll" aria-pressed="true">Show all pets</button>
        </section>

        <section class="browse-filters" aria-label="Filters">
            <div class="browse-filter-grid">
                <div class="browse-filter-block">
                    <span class="browse-filter-label">Breed</span>
                    <div class="browse-chips browse-chips-scroll" id="breedFilters"></div>
                </div>
                <div class="browse-filter-block">
                    <span class="browse-filter-label">Age range</span>
                    <div class="browse-chips browse-chips-wrap" id="ageFilters">
                        <button type="button" class="browse-chip" data-filter="ageRanges" data-value="unknown">Unknown</button>
                        <button type="button" class="browse-chip" data-filter="ageRanges" data-value="0-2">0–2 yr</button>
                        <button type="button" class="browse-chip" data-filter="ageRanges" data-value="3-5">3–5 yr</button>
                        <button type="button" class="browse-chip" data-filter="ageRanges" data-value="6-8">6–8 yr</button>
                        <button type="button" class="browse-chip" data-filter="ageRanges" data-value="9+">9+ yr</button>
                    </div>
                </div>
                <div class="browse-filter-block">
                    <span class="browse-filter-label">Traits</span>
                    <div class="browse-chips browse-chips-scroll" id="personalityFilters"></div>
                </div>
                <div class="browse-filter-block">
                    <label class="browse-filter-label" for="sortSelect">Sort</label>
                    <select id="sortSelect" class="browse-select">
                        <option value="default">Default</option>
                        <option value="ageAsc">Age: young to old</option>
                        <option value="ageDesc">Age: old to young</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="browse-heading-row">
            <h1 class="browse-title">Pets <span class="browse-count" id="totalCount">0</span></h1>
            <p class="browse-result-info" id="resultInfo"></p>
        </div>

        <div id="petGrid" class="browse-grid"></div>
        <p class="browse-empty" id="emptyState" hidden>No pets match your filters yet. Try resetting or broadening your search.</p>
    </div>
</main>

<div id="petModal" class="browse-modal" hidden role="dialog" aria-modal="true" aria-labelledby="modalName">
    <div class="browse-modal-backdrop" data-close-modal></div>
    <div class="browse-modal-panel">
        <button type="button" class="browse-modal-close" id="modalClose" aria-label="Close">×</button>
        <img id="modalImage" class="browse-modal-img" src="" alt="">
        <div class="browse-modal-body">
            <div class="browse-modal-head">
                <div>
                    <h2 id="modalName" class="browse-modal-name"></h2>
                    <p id="modalBreed" class="browse-modal-breed"></p>
                </div>
                <div id="modalGender" class="browse-modal-gender" aria-hidden="true"></div>
            </div>
            <dl class="browse-modal-dl">
                <div><dt>Age</dt><dd id="modalAge"></dd></div>
                <div><dt>Health</dt><dd id="modalHealth"></dd></div>
                <div class="full"><dt>About</dt><dd id="modalPersonality"></dd></div>
            </dl>
            <div class="browse-verify" id="modalVerify" hidden>
                <p class="browse-verify-title">Verification details (read-only)</p>
                <p class="browse-verify-sub">We will use your registered contact details to verify and follow up.</p>
                <dl class="browse-verify-dl">
                    <div><dt>Email</dt><dd id="modalVerifyEmail"></dd></div>
                    <div><dt>Phone</dt><dd id="modalVerifyPhone"></dd></div>
                </dl>
            </div>
            <p class="browse-modal-note" id="modalApplyNote"></p>
            <button type="button" class="pet-btn browse-modal-cta" id="modalApplyBtn">Request adoption</button>
        </div>
    </div>
</div>

<script type="application/json" id="pets-json"><?php echo $petsJson; ?></script>
<script type="application/json" id="browse-config"><?php echo $browseCfgJson; ?></script>
<script src="assets/javascript/pet-browse.js" defer></script>

<?php include 'footer.php'; ?>
