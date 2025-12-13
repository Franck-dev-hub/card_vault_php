<?php
require_once "/app/vendor/autoload.php";

// Quick example
use TCGdex\TCGdex;

// Initialize the SDK
$tcgdex = new TCGdex("en");

// Fetch a card by ID
$card = $tcgdex->card->get('swsh3-136');

echo "Found: {$card->name} ({$card->localId}/{$card->set->cardCount->total})";
?>