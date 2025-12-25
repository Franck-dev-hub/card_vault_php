<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoadmapController extends BaseController
{
    #[Route("/roadmap/{name}", name: "roadmap")]
    public function roadmap(string $name): Response
    {
        return $this->renderPage("routes/roadmap.js", [
            "name" => $name,
            "currentPage" => $name
        ]);
    }
}
