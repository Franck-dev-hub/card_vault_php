<?php

/**
 * @throws DOMException
 */
function readHeader(string $pageTitle = "Page Title", string $profileText = "Profile"): void
{
    // Init DOM
    $dom = new DOMDocument;
    $dom->formatOutput = true;

    // Flex box
    $flexContainer = $dom->createElement("div");
    $flexContainer->setAttribute("class", "header-box");

    // Add title
    $mainTitle = $dom->createElement("div", $pageTitle);
    $mainTitle->setAttribute("class", "header-title");
    $flexContainer->appendChild($mainTitle);

    // Add profile
    $profil = $dom->createElement("div", $profileText);
    $profil->setAttribute("class", "header-profile");
    $flexContainer->appendChild($profil);

    $dom->appendChild($flexContainer);
    echo $dom->saveHTML($flexContainer);
}

try {
    readHeader($pageTitle, $profileText);
} catch (DOMException $e) {
    echo "Can't read header php file";
}

?>