<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthController
{
    private const string PAGE_NAME = "health";

    #[Route("/" . self::PAGE_NAME, name: self::PAGE_NAME)]
    public function health(): Response
    {
        return new Response("OK", Response::HTTP_OK);
    }
}
