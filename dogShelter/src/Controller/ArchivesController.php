<?php

namespace App\Controller;

use App\Entity\AdoptionCase;
use App\Entity\Status;
use App\Repository\AdoptionCaseRepository;
use App\Repository\DocumentsRepository;
use App\Repository\DogRepository;
use App\Repository\PostRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchivesController extends AbstractController
{
    #[Route('/archives', name: 'app_archives')]
    public function index(): Response
    {
        return $this->render('archives/index.html.twig', [
            
        ]);
    }
    #[Route('/archives/adoption/cases', name: 'app_archives_adoption_cases')]
    public function archivedAdoptionCases(AdoptionCaseRepository $adoptionCases): Response
    {   
        
        return $this->render('archives/adoption_cases.html.twig', [
            'adoption_cases' => $adoptionCases->findBy( ['archived'=> true]),
        ]);
    }

    #[Route('/archives/dogs', name: 'app_archives_dogs')]
    public function archivedDogs(DogRepository $dogs): Response
    {   
        
        return $this->render('archives/dogs.html.twig', [
            'dogs' => $dogs->findBy( ['archived'=> true]),
        ]);
    }

    #[Route('/archives/users', name: 'app_archives_users')]
    public function archivedUsers(UserRepository $users): Response
    {   
        
        return $this->render('archives/users.html.twig', [
            'users' => $users->findBy( ['archived'=> true]),
        ]);
    }
    #[Route('/archives/statuses', name: 'app_archives_statuses')]
    public function archivedStatuses(StatusRepository $statuses): Response
    {   
        
        return $this->render('archives/statuses.html.twig', [
            'statuses' => $statuses->findBy( ['archived'=> true]),
        ]);
    }
    #[Route('/archives/documents', name: 'app_archives_documents')]
    public function archivedDocuments(DocumentsRepository $documents): Response
    {   
        
        return $this->render('archives/documents.html.twig', [
            'documents' => $documents->findBy( ['archived'=> true]),
        ]);
    }
    #[Route('/archives/posts', name: 'app_archives_posts')]
    public function archivedPosts(PostRepository $posts): Response
    {   
        return $this->render('archives/posts.html.twig', [
            'posts' => $posts->findBy( ['archived'=> true]),
        ]);
    }
    

}
