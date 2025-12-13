<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" href="/public/assets/favicon.png" type="image/x-png">
    <link rel="stylesheet" href="/public/css/styles.css">
    <link rel="stylesheet" href="/public/css/header.css">
    <link rel="stylesheet" href="/public/css/footer.css">
</head>
<body>
<?php include __DIR__ . "/header.php"; ?>

<main>
    <?php include $contentFile; ?>
</main>

<?php include __DIR__ . "/footer.php"; ?>
</body>
</html>