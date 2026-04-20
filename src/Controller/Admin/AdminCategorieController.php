<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class AdminCategorieController extends AbstractController
{
    #[Route('/', name: 'admin_categories')]
    public function index(CategorieRepository $repo): Response
    {
        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $repo->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cat = new Categorie();
        $form = $this->createForm(CategorieType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cat);
            $em->flush();
            $this->addFlash('success', 'Catégorie ajoutée.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categorie/form.html.twig', [
            'form' => $form,
            'titre' => 'Ajouter une catégorie',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_edit', requirements: ['id' => '\d+'])]
    public function edit(Categorie $cat, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categorie/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier : '.$cat->getNom(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_categorie_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Categorie $cat, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-cat-'.$cat->getId(), $request->request->get('_token'))) {
            $em->remove($cat);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }
        return $this->redirectToRoute('admin_categories');
    }
}
