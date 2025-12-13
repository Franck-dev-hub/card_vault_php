<?php
function getCardsFromSet($setId) {
    $tcgdex = new \TCGdex\TCGdex("fr");

    try {
        $set = $tcgdex->set->get($setId);
        return $set->cards ?? [];
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des cartes: " . $e->getMessage());
        return [];
    }
}
?>
