<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TCGdex\TCGdex;
use Symfony\Component\HttpClient\Psr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;

class SearchController extends BaseController
{
    #[Route("/search", name: "search")]
    public function search(): Response
    {
        // Create PSR-17 factories
        $psr17Factory = new Psr17Factory();

        // Set up the factories and client
        TCGdex::$requestFactory = $psr17Factory;
        TCGdex::$responseFactory = $psr17Factory;
        TCGdex::$client = new Psr18Client();
        // Set cache TTL (in milliseconds)
        TCGdex::$ttl = 3600 * 1000; // 1 hour

        // Initialize the SDK with the language
        $tcgdex = new TCGdex("en");

        // Fetch a card by ID
        $card = $tcgdex->card->get('swsh3-136');
        $imageUrl = "https://assets.tcgdex.net/en/swsh/swsh3/136/";

        return $this->renderPage("routes/search.html.twig", [
            "card" => $card,
            "imageUrl" => $imageUrl,
            "currentPage" => "search"
        ]);
    }
}
