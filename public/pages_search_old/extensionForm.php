<?php
require_once "getCards.php";

// Pokemon
$extensionPokemon = [];
$setDataPokemon = [];
foreach ($tcgdex->set->list() as $set) {
    $extensionPokemon[] = $set->name;
    $setDataPokemon[$set->name] = $set->id;
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

// Fetch cards
$cards = [];
$setSelected = null;
$currentSet = null;
if (isset($_POST["choix"]) && !empty($_POST["choix"])) {
    $setSelected = $_POST["choix"];

    if ($licenceSelected === "pokemon") {
        $setId = $setDataPokemon[$setSelected] ?? null;
        if ($setId) {
            $currentSet = $tcgdex->set->get($setId);
            $cards = getCardsFromSet($setId, $language);
        }
    }
}

?>

<!-- Extension form -->
<?php if($licenceSelected && isset($extensions[$licenceSelected])): ?>
    <form method="post" action="" id="setForm">
        <select name="choix" id="choix" onchange="this.form.submit()">
            <option value="">-- Sélectionnez un set --</option>
            <?php foreach ($extensions[$licenceSelected] as $value): ?>
                <option value="<?= $value ?>"
                    <?= ($setSelected === $value) ? "selected" : '' ?>>
                    <?= $value ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="licence" value="<?= $licenceSelected ?>">
    </form>

    <!-- Display total cards -->
    <?php if ($currentSet && isset($currentSet->cardCount->total)): ?>
        <p>Total de cartes dans ce set : <?= $currentSet->cardCount->total ?></p>
    <?php endif; ?>

<?php endif; ?>

<!-- Display cards -->
<?php if (!empty($cards)): ?>
    <div class="cards-container">
        <?php foreach ($cards as $card): ?>
            <div class="card-item">
                <img src="<?= $card->image . "/low.webp" ?>"
                     alt="<?= $card->name ?>"
                     loading="lazy">
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
