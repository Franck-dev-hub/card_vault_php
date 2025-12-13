<?php
require_once "/app/vendor/autoload.php";

use TCGdex\TCGdex;

// Initialize the SDK
$tcgdex = new TCGdex($language);

require_once "licenseForm.php";
require_once "extensionForm.php";

?>
