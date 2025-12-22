<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends BaseController
{
    #[Route("/search/{name}", name: "search")]
    public function search(string $name): Response
    {
        return $this->renderPage("routes/search.html.twig", [
            "name" => $name,
            "pageTitle" => ucfirst($name),
            "currentPage" => $name
        ]);
    }
}
