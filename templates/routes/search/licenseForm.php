<?php
$licenceSelected = $_POST['licence'] ?? null;

?>

<form method="post" action="">
    <select name="licence" id="licence" onchange="this.form.submit()">
        <option value="">-- Sélectionnez une license --</option>
        <option value="pokemon" <?= $licenceSelected === "pokemon" ? "selected" : '' ?>>Pokémon</option>
        <option value="magic" <?= $licenceSelected === "magic" ? "selected" : '' ?>>Magic</option>
        <option value="yugioh" <?= $licenceSelected === "yugioh" ? "selected" : '' ?>>Yu-Gi-Oh</option>
    </select>
</form>
