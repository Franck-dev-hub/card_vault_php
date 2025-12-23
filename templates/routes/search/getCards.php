<?php
function getCardsFromSet($setId, $language) {
    $tcgdex = new \TCGdex\TCGdex($language);

    try {
        $set = $tcgdex->set->get($setId);
        return $set->cards ?? [];
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des cartes: " . $e->getMessage());
        return [];
    }
}
