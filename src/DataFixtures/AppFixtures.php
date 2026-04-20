<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Catégories ---
        $categoriesData = [
            ['Roman', 'Œuvres de fiction narrative.'],
            ['Science-fiction', 'Univers futuristes, technologies imaginaires.'],
            ['Policier', 'Enquêtes et mystères.'],
            ['Biographie', 'Vies de personnes réelles.'],
            ['Informatique', 'Programmation et technologies.'],
        ];

        $categories = [];
        foreach ($categoriesData as [$nom, $desc]) {
            $cat = new Categorie();
            $cat->setNom($nom)->setDescription($desc);
            $manager->persist($cat);
            $categories[] = $cat;
        }

        // --- Utilisateurs ---
        $admin = new User();
        $admin->setEmail('admin@biblio.fr')
            ->setPrenom('Admin')
            ->setNom('Principal')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('user@biblio.fr')
            ->setPrenom('Jean')
            ->setNom('Dupont')
            ->setPassword($this->hasher->hashPassword($user1, 'user123'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('marie@biblio.fr')
            ->setPrenom('Marie')
            ->setNom('Martin')
            ->setPassword($this->hasher->hashPassword($user2, 'user123'));
        $manager->persist($user2);

        // --- Livres ---
        $livresData = [
            ['Les Misérables', 'Victor Hugo', 'Un roman épique sur la rédemption dans la France du XIXe siècle.', '1862-01-01', 0],
            ['1984', 'George Orwell', 'Une dystopie sur une société totalitaire où Big Brother surveille tout.', '1949-06-08', 1],
            ['Dune', 'Frank Herbert', 'Épopée science-fiction sur la planète désertique Arrakis.', '1965-08-01', 1],
            ['Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un conte poétique et philosophique.', '1943-04-06', 0],
            ['Sherlock Holmes', 'Arthur Conan Doyle', 'Les enquêtes du célèbre détective britannique.', '1892-10-14', 2],
            ['Harry Potter à l\'école des sorciers', 'J.K. Rowling', 'Le premier tome des aventures du jeune sorcier.', '1997-06-26', 0],
            ['Clean Code', 'Robert C. Martin', 'Guide de bonnes pratiques pour écrire du code propre.', '2008-08-01', 4],
            ['Steve Jobs', 'Walter Isaacson', 'Biographie officielle du cofondateur d\'Apple.', '2011-10-24', 3],
            ['Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'La quête pour détruire l\'Anneau Unique en Terre du Milieu.', '1954-07-29', 0],
            ['Fondation', 'Isaac Asimov', 'Le déclin d\'un empire galactique et la naissance d\'une Fondation.', '1951-05-01', 1],
        ];

        $livres = [];
        foreach ($livresData as [$titre, $auteur, $resume, $date, $catIndex]) {
            $livre = new Livre();
            $livre->setTitre($titre)
                ->setAuteur($auteur)
                ->setResume($resume)
                ->setDatePublication(new \DateTime($date))
                ->setDisponible(true)
                ->setCategorie($categories[$catIndex]);
            $manager->persist($livre);
            $livres[] = $livre;
        }

        // --- Emprunts ---
        $emprunt1 = new Emprunt();
        $emprunt1->setUtilisateur($user1)
            ->setLivre($livres[1]) // 1984
            ->setDateEmprunt(new \DateTime('-5 days'))
            ->setDateRetourPrevue(new \DateTime('+9 days'))
            ->setStatut(Emprunt::STATUT_EN_COURS);
        $livres[1]->setDisponible(false);
        $manager->persist($emprunt1);

        $emprunt2 = new Emprunt();
        $emprunt2->setUtilisateur($user2)
            ->setLivre($livres[2]) // Dune
            ->setDateEmprunt(new \DateTime('-20 days'))
            ->setDateRetourPrevue(new \DateTime('-6 days'))
            ->setDateRetourReelle(new \DateTime('-3 days'))
            ->setStatut(Emprunt::STATUT_RENDU);
        $manager->persist($emprunt2);

        $manager->flush();
    }
}
