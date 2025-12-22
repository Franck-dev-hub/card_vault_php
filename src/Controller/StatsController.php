<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatsController extends BaseController
{
    #[Route("/stats/{name}", name: "stats")]
    public function stats(string $name): Response
    {
        return $this->renderPage("routes/stats.html.twig", [
            "name" => $name,
            "pageTitle" => ucfirst($name),
            "currentPage" => $name
        ]);
    }
}
