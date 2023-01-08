<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findSixLatest();

        return $this->render('main_page/index.html.twig', [
            'posts' => $posts
        ]);
    }
    #[Route('/sidebar', name: 'app_side_bar')]
    public function sidebartesting(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findSixLatest();

        return $this->render('main_page/sidebar.html.twig', [
            'posts' => $posts
        ]);
    }
}
