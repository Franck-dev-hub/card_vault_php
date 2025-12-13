<footer>

<?php

/**
 * @throws DOMException
 */
function readFooter(): void
{
	// Init DOM
    $dom = new DOMDocument;
    $dom->formatOutput = true;

    // Get the current page
    $currentPage = basename($_SERVER["PHP_SELF"], ".php");

	// Button directory
    $iconDir = "/public/assets/icons/footer/";
    // [route, label, icon]
    $buttons = [
            ["dashboard", t("dashboard.title"), $iconDir . "icon1.svg"],
            ["stats", t("stats.title"), $iconDir . "icon2.svg"],
            ["scan", t("scan.title"), $iconDir . "icon3.svg"],
            ["vault", t("vault.title"), $iconDir . "icon4.svg"],
            ["search", t("search.title"), $iconDir . "icon5.svg"]
    ];

	// Main content (Flex box)
    $flexContainer = $dom->createElement("div");
    $flexContainer->setAttribute("class", "footer-box");

    // Generate buttons
    foreach ($buttons as [$page, $buttonName, $buttonIcon]) {
        // Check if svg file exist
        if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $buttonIcon)) {
            echo "Can't load file :" . $buttonIcon . "<br>";
            continue;
        }

        // Link to the route
        $link = $dom->createElement("a");
        $link->setAttribute("href", "/" . $page);
        $link->setAttribute("class", "footer-link");

        // Footer button
        $button = $dom->createElement("button", "");
        $class = "footer-button";

        // Mark button to active is current page
        if ($currentPage === $page) {
            $class .= " active";
        }
        $button->setAttribute("class", $class);

        // Button icon
        $icon = $dom->createElement("img");
        $icon->setAttribute("src", $buttonIcon);
        $icon->setAttribute("alt", $buttonName);

        // Build HTML
        $button->appendChild($icon);
        $link->appendChild($button);
        $flexContainer->appendChild($link);
	}

    // Add footer to the DOM
    $dom->appendChild($flexContainer);

    // Final HTML
    echo $dom->saveHTML($flexContainer);
}

try {
    readFooter();
} catch (DOMException $e) {
    echo "Can't read footer php file";
}

?>
</footer>
