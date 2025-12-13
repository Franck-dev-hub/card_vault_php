<?php
// Pokemon
$extensionPokemon = [];
foreach ($tcgdex->set->list() as $set) {
    $extensionPokemon[] = $set->name;
}

// Magic
$extensionMagic = ["ext magic 1", "ext magic 2", "ext magic 3"];

// Yu-Gi-Oh
$extensionYugioh = ["ext yugi 1", "ext yugi 2", "ext yugi 3"];

$extensions = [
    "pokemon" => $extensionPokemon,
    "magic" => $extensionMagic,
    "yugioh" => $extensionYugioh
];

?>
    <!-- Extension form -->
<?php if($licenceSelected && isset($extensions[$licenceSelected])): ?>
    <form method="post" action="">
        <select name="choix" id="choix">
            <option value="">-- Sélectionnez un set --</option>
            <?php foreach ($extensions[$licenceSelected] as $value): ?>
                <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="licence" value="<?= htmlspecialchars($licenceSelected) ?>">
    </form>
<?php endif; ?>