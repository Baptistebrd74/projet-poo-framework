<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/livres')]
#[IsGranted('ROLE_ADMIN')]
class AdminLivreController extends AbstractController
{
    #[Route('/', name: 'admin_livres')]
    public function index(LivreRepository $livreRepo): Response
    {
        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livreRepo->findBy([], ['titre' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_livre_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();
        $livre->setDatePublication(new \DateTime());
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            $this->addFlash('success', 'Livre ajouté.');
            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('admin/livre/form.html.twig', [
            'form' => $form,
            'titre' => 'Ajouter un livre',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_livre_edit', requirements: ['id' => '\d+'])]
    public function edit(Livre $livre, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Livre modifié.');
            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('admin/livre/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier : '.$livre->getTitre(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_livre_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Livre $livre, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-livre-'.$livre->getId(), $request->request->get('_token'))) {
            $em->remove($livre);
            $em->flush();
            $this->addFlash('success', 'Livre supprimé.');
        }
        return $this->redirectToRoute('admin_livres');
    }
}
