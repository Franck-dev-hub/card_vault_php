<?php
namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    private const string PAGE_NAME = "login";

    #[Route("/" . self::PAGE_NAME, name: self::PAGE_NAME)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect user if logged in
        if ($this->getUser()) {
            return $this->redirectToRoute("root");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $this->addFlash('error', $error->getMessageKey());
        }

        // Create form from template
        $loginForm = $this->createForm(LoginFormType::class);

        return $this->renderPage(self::PAGE_NAME . ".html.twig", [
            "dir" => "security",
            "currentPage" => self::PAGE_NAME,
            "loginForm" => $loginForm->createView(),
            "last_username" => $lastUsername,
            "error" => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
