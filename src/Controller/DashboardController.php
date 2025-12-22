<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends BaseController
{
    #[Route("/dashboard/{name}", name: "dashboard")]
    public function dashboard(string $name): Response
    {
        return $this->renderPage("routes/dashboard.html.twig", [
            "name" => $name,
            "currentPage" => $name
        ]);
    }
}
