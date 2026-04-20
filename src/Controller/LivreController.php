<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LivreController extends AbstractController
{
    #[Route('/livres', name: 'app_livres')]
    public function list(Request $request, LivreRepository $livreRepo, CategorieRepository $catRepo): Response
    {
        $categorieId = $request->query->get('categorie');
        $disponibleOnly = $request->query->getBoolean('disponible');

        $qb = $livreRepo->createQueryBuilder('l')
            ->leftJoin('l.categorie', 'c')->addSelect('c')
            ->orderBy('l.titre', 'ASC');

        if ($categorieId) {
            $qb->andWhere('l.categorie = :cat')->setParameter('cat', $categorieId);
        }
        if ($disponibleOnly) {
            $qb->andWhere('l.disponible = true');
        }

        return $this->render('livre/list.html.twig', [
            'livres' => $qb->getQuery()->getResult(),
            'categories' => $catRepo->findAll(),
            'categorieActive' => $categorieId,
            'disponibleOnly' => $disponibleOnly,
        ]);
    }

    #[Route('/livres/{id}', name: 'app_livre_detail', requirements: ['id' => '\d+'])]
    public function detail(Livre $livre): Response
    {
        return $this->render('livre/detail.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/emprunter/{id}', name: 'app_emprunter', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function emprunter(Livre $livre, EntityManagerInterface $em): Response
    {
        if (!$livre->isDisponible()) {
            $this->addFlash('error', 'Ce livre n\'est pas disponible.');
            return $this->redirectToRoute('app_livre_detail', ['id' => $livre->getId()]);
        }

        $emprunt = new Emprunt();
        $emprunt->setUtilisateur($this->getUser())
            ->setLivre($livre)
            ->setDateEmprunt(new \DateTime())
            ->setDateRetourPrevue(new \DateTime('+14 days'))
            ->setStatut(Emprunt::STATUT_EN_COURS);

        $livre->setDisponible(false);

        $em->persist($emprunt);
        $em->flush();

        $this->addFlash('success', 'Livre emprunté avec succès. Retour prévu dans 14 jours.');
        return $this->redirectToRoute('app_mes_emprunts');
    }
}
