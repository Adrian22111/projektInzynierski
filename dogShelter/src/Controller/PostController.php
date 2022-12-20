<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/post')]
class PostController extends AbstractController
{
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            return $this->render('post/index.html.twig', [
                'posts' => $postRepository->findAll(),
            ]);
        }
        elseif($this->isGranted('ROLE_PRACOWNIK'))
        {
            /** @var User $User */
            $User = $this->getUser();
            $id = $User->getId();
            return $this->render('post/index.html.twig', [
                'posts' => $postRepository->findUserPosts($id),
            ]);
        }

    }
    #[Route('/all', name: 'app_post_all', methods: ['GET'])]
    public function showAllPosts(PostRepository $postRepository): Response
    {
        return $this->render('post/all_posts.html.twig', [
            'posts' => $postRepository->findAllByNewest(),
        ]);
    }
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new DateTime());
            $post->setPostOwner($this->getUser());
            $file = $form->get('image')->getData();
            if($file)
            {
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME,
                );
                $safeName = $slugger->slug($originalName);
                $newName = $safeName."_".uniqid().".".$file->guessExtension();
                try
                {
                    $file->move(
                        $this->getParameter('posts_directory'),
                        $newName
                    );
                }
                catch(FileException $e)
                {

                }
                $post->setImage($newName);
            }
            $postRepository->save($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
    #[IsGranted(POST::EDIT,'post')]
    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if($file)
            {
                if($post->getImage()!=null)
                {
                    $post->setImage(
                        new File($this->getParameter('posts_directory').'/'.$post->getImage())
                    );
                    $filesystem = new Filesystem();
                    $filesystem->remove($post->getImage());
                }
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME,
                );
                $safeName = $slugger->slug($originalName);
                $newName = $safeName."_".uniqid().".".$file->guessExtension();
                try
                {
                    $file->move(
                        $this->getParameter('posts_directory'),
                        $newName
                    );
                }
                catch(FileException $e)
                {

                }
                $post->setImage($newName);
            }

            
            $postRepository->save($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
    #[IsGranted(POST::DELETE,'post')]
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {

            if($post->getImage()!=null)
            {
                $post->setImage(
                    new File($this->getParameter('posts_directory').'/'.$post->getImage())
                );
                $filesystem = new Filesystem();
                $filesystem->remove($post->getImage());
            }
        }
        $postRepository->remove($post, true);
        

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
