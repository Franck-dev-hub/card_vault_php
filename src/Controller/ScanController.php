<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ScanController extends BaseController
{
    #[Route("/scan/{name}", name: "scan")]
    public function scan(string $name): Response
    {
        return $this->renderPage("routes/scan.html.twig", [
            "name" => $name,
            "pageTitle" => ucfirst($name),
            "currentPage" => $name
        ]);
    }
}
