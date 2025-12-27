<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RootController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->redirectToRoute("root", ["name" => "dashboard"]);
    }
}
