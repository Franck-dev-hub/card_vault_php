<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VaultController extends BaseController
{
    #[Route("/vault/{name}", name: "vault")]
    public function vault(string $name): Response
    {
        return $this->renderPage("routes/vault.html.twig", [
            "name" => $name,
            "pageTitle" => ucfirst($name),
            "currentPage" => $name
        ]);
    }
}
