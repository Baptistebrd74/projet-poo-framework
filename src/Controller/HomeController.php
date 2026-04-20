<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(LivreRepository $livreRepo, CategorieRepository $catRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'livres' => $livreRepo->findBy([], ['titre' => 'ASC'], 6),
            'categories' => $catRepo->findAll(),
        ]);
    }
}
