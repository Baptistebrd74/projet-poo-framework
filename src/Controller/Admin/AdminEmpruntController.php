<?php

namespace App\Controller\Admin;

use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminEmpruntController extends AbstractController
{
    #[Route('/utilisateurs', name: 'admin_users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/emprunts', name: 'admin_emprunts')]
    public function emprunts(EmpruntRepository $empRepo): Response
    {
        return $this->render('admin/emprunts.html.twig', [
            'emprunts' => $empRepo->findBy([], ['dateEmprunt' => 'DESC']),
        ]);
    }

    #[Route('/emprunts/{id}/retour', name: 'admin_emprunt_retour', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function marquerRetour(Emprunt $emprunt, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('retour-emprunt-'.$emprunt->getId(), $request->request->get('_token'))) {
            $emprunt->setStatut(Emprunt::STATUT_RENDU);
            $emprunt->setDateRetourReelle(new \DateTime());
            $emprunt->getLivre()->setDisponible(true);
            $em->flush();
            $this->addFlash('success', 'Emprunt marqué comme rendu.');
        }
        return $this->redirectToRoute('admin_emprunts');
    }
}
