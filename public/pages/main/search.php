<?php
// Define variables
$pageTitle = t("search.title");
$profileText = t("common.profile");
$contentFile = __FILE__;

if (!isset($isTemplate)):
    $isTemplate = true; include "public/pages/template.php";
else:
    include "public/pages/main/search/api.php";
endif; ?>