<?php

namespace App\Controller;

use App\Repository\DogRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(PostRepository $postRepository,DogRepository $dogRepository): Response
    {
        $posts = $postRepository->findSixLatest();
        $dogs = $dogRepository->findAll();

        return $this->render('main_page/index.html.twig', [
            'posts' => $posts,
            'dogs' => $dogs,
        ]);
    }
    #[Route('/newslider', name: 'app_new_slideer')]
    public function newSlider(DogRepository $dogRepository): Response
    {
        $dogs = $dogRepository->findAll();

        return $this->render('main_page/newslider.html.twig', [
            'dogs'=>$dogs,
        ]);
    }

    #[Route('/about', name: 'app_about_us')]
    public function about(): Response
    {
        return $this->render('main_page/about.html.twig', [
            
        ]);
    }

}
