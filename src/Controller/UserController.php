<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/mes-emprunts', name: 'app_mes_emprunts')]
    public function mesEmprunts(): Response
    {
        return $this->render('user/emprunts.html.twig', [
            'emprunts' => $this->getUser()->getEmprunts(),
        ]);
    }
}
