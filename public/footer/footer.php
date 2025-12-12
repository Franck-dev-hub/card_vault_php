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

    // Get current page
    $currentPage = basename($_SERVER["PHP_SELF"], ".php");

	// Button variables
    $iconDir = "/public/assets/icons/footer/";
    $buttons = [
            ["dashboard", "Dashboard", $iconDir . "icon1.svg"],
            ["stats", "Stats", $iconDir . "icon2.svg"],
            ["scan", "Scan", $iconDir . "icon3.svg"],
            ["vault", "Vault", $iconDir . "icon4.svg"],
            ["search", "Search", $iconDir . "icon5.svg"]
    ];

	// Flex box
	$flexContainer = $dom->createElement("div");
    $flexContainer->setAttribute("class", "footer-box");

	foreach ($buttons as [$page, $buttonName, $buttonIcon]) {
        // Check if svg exist
        if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $buttonIcon)) {
            echo "Can't load file :" . $buttonIcon . "<br>";
            continue;
        }
		$button = $dom->createElement("button", "");
        $class = "footer-button";

        if ($currentPage === $page) {
            $class .= " active";
        }
        $button->setAttribute("class", $class);

        $icon = $dom->createElement("img");
        $icon->setAttribute("src", $buttonIcon);
        $icon->setAttribute("alt", $buttonName);
        $button->appendChild($icon);

		$flexContainer->appendChild($button);
	}

	$dom->appendChild($flexContainer);
	echo $dom->saveHTML($flexContainer);
}

try {
    readFooter();
} catch (DOMException $e) {
    echo "Can't read footer php file";
}

?>
</footer>
