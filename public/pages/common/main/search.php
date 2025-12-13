<?php
// Define variables
$pageTitle = t("search.title");
$profileText = t("common.profile");
$contentFile = __FILE__;

if (!isset($isTemplate)):
    $isTemplate = true; include "public/pages/common/template.php";
else:
    // HTML content
endif; ?>