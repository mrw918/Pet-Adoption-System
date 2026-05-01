<?php
$page = 'index';
$pageTitle = 'Home — Pet Adoption Platform';
include 'header.php';

$pets = [
    [
        'name' => 'Lucky',
        'breed' => 'Labrador mix',
        'age' => '11 years',
        'gender' => 'Female',
        'health' => 'Healthy, vaccinated',
        'personality' => 'Calm, deeply loyal',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-lucky.svg',
    ],
    [
        'name' => 'Bailey',
        'breed' => 'Golden Retriever',
        'age' => '10 years',
        'gender' => 'Female',
        'health' => 'Spayed, healthy',
        'personality' => 'Gentle soul, loves walks',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-bailey.svg',
    ],
    [
        'name' => 'Mochi',
        'breed' => 'Corgi',
        'age' => '6 years',
        'gender' => 'Male',
        'health' => 'Minor tartar, dewormed',
        'personality' => 'Easygoing, playful',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-mochi.svg',
    ],
    [
        'name' => 'Oliver',
        'breed' => 'British Shorthair',
        'age' => '6 years',
        'gender' => 'Male',
        'health' => 'Healthy, vaccinated',
        'personality' => 'Quiet, clever',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-oliver.svg',
    ],
    [
        'name' => 'Coco',
        'breed' => 'Poodle',
        'age' => '4 years',
        'gender' => 'Female',
        'health' => 'Healthy, vaccinated',
        'personality' => 'Smart, affectionate',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-coco.svg',
    ],
    [
        'name' => 'Milo',
        'breed' => 'Tabby Cat',
        'age' => '3 years',
        'gender' => 'Male',
        'health' => 'Neutered, healthy',
        'personality' => 'Curious, playful',
        'status' => 'Adopted',
        'imgurl' => 'assets/img/home-milo.svg',
    ],
];
?>

<main class="pet-main">
    <section class="pet-home-hero">
        <div class="pet-home-hero-inner">
            <h1>Give them a home worth waiting for</h1>
            <p class="sub">Adopt, don’t shop—meet companions who are ready for kindness, routine vet care, and a lifetime by your side.</p>
            <div class="pet-tags-row">
                <span class="pet-chip">Rescue-first</span>
                <span class="pet-chip">Vetted profiles</span>
                <span class="pet-chip">Lifelong care</span>
            </div>
            <div class="pet-hero-actions">
                <a class="pet-btn" href="#pets">Browse success stories</a>
                <a class="pet-btn pet-btn-outline" href="guide.php">Read the adoption guide</a>
            </div>
        </div>
    </section>

    <section class="pet-container" id="pets">
        <div class="pet-section-head">
            <h2>Recently homed friends</h2>
            <p>Every profile reflects real care journeys—health notes, temperament, and the joy of a fresh start.</p>
        </div>
        <div class="pet-card-grid">
            <?php foreach ($pets as $pet) : ?>
                <article class="pet-card">
                    <img src="<?php echo htmlspecialchars($pet['imgurl'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($pet['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async">
                    <div class="pet-card-body">
                        <div class="pet-card-name"><?php echo htmlspecialchars($pet['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="pet-card-meta">
                            <?php echo htmlspecialchars($pet['breed'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($pet['age'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($pet['gender'], ENT_QUOTES, 'UTF-8'); ?>
                            <br>
                            <?php echo htmlspecialchars($pet['health'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($pet['personality'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <span class="pet-status"><?php echo htmlspecialchars($pet['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="pet-quote-band">
        <p>Every animal deserves patience, dignity, and a family that chooses them on purpose.<br>Adoption is not charity—it is mutual rescue.</p>
        <div class="pet-quote-actions">
            <a class="pet-btn pet-btn-light" href="about.php">Go to About Us</a>
            <a class="pet-btn pet-btn-light" href="guide.php">Go to Adoption Guide</a>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
