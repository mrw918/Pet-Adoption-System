<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($pageTitle)) {
    $pageTitle = 'Pet Adoption Platform';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="assets/css/pet-theme.css">
</head>
<body class="site-pet">
<div class="pet-bg-layer" aria-hidden="true"></div>
<div class="pet-bg-paws" aria-hidden="true"></div>
<?php
$navBase = '';
require __DIR__ . '/include/site_top_nav.php';
?>
