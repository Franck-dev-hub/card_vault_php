<?php
namespace App\Controller;

use App\Service\FooterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RootController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->redirectToRoute("root", ["name" => "dashboard"]);
    }
}
