<?php
// Define variables
$pageTitle = t("stats.title");
$profileText = t("common.profile");
$contentFile = __FILE__;

if (!isset($isTemplate)):
    $isTemplate = true; include "public/pages/template.php";
else:
    // Init DOM
    $dom = new DOMDocument;
    $dom->formatOutput = true;

    // Add demo text
    $demoText = $dom->createElement("div", "Coucou depuis " . $pageTitle);

    $dom->appendChild($demoText);
    echo $dom->saveHTML($demoText);
endif; ?>