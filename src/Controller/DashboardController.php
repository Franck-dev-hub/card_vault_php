<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends BaseController
{
    private const string PAGE_NAME = "dashboard";

    #[Route("/" . self::PAGE_NAME, name: self::PAGE_NAME)]
    public function dashboard(): Response
    {
        return $this->renderPage(self::PAGE_NAME . ".html.twig", [
            "dir" => "routes",
            "currentPage" => self::PAGE_NAME
        ]);
    }
}
