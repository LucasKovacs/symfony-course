<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     *
     * @return void
     */
    public function index()
    {
        // return new JsonResponse([
        //     'action' => 'index',
        //     'time' => time(),
        // ]);

        return $this->render('base.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     *
     * @return void
     */
    public function confirmUser(string $token, UserConfirmationService $userConfirmationService)
    {
        $userConfirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }
}
