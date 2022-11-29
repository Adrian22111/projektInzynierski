<?php

namespace App\Controller;

use App\Entity\Dog;
use App\Form\DogType;
use App\Repository\DogRepository;
use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/dog')]
class DogController extends AbstractController
{
    #[Route('/', name: 'app_dog_index', methods: ['GET'])]
    public function index(DogRepository $dogRepository): Response
    {
        return $this->render('dog/index.html.twig', [
            'dogs' => $dogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DogRepository $dogRepository, SluggerInterface $slugger): Response
    {
        $dog = new Dog();
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if($file)
            {
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeName = $slugger->slug($originalName);
                $newName = $safeName."_".uniqid().".".$file->guessExtension();
                try
                {
                    $file->move(
                        $this->getParameter('dogimages_directory'),
                        $newName
                    );
                }
                catch(FileException $e )
                {

                }
                $dog->setImage($newName);

            }
            $guardians = $form->get('guardian')->getData();
            foreach($guardians as $guardian)
            {
                $guardian->addGuardianOf($dog);
            }
            $dogRepository->save($dog, true);
            $this->addFlash('success','Pomyślnie dodano zdjęcie');
            return $this->redirectToRoute('app_dog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dog/new.html.twig', [
            'dog' => $dog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dog_show', methods: ['GET'])]
    public function show(Dog $dog): Response
    {
        return $this->render('dog/show.html.twig', [
            'dog' => $dog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dog $dog, DogRepository $dogRepository, SluggerInterface $slugger, UserRepository $users): Response
    {
        $guardiansToDelete = $dog->getGuardian();
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($guardiansToDelete as $guardianToDelete)
            {
                $dog->removeGuardian($guardianToDelete);
            }
            if($form->get('image')->getData() != null)
            {
                if($dog->getImage()!= null)
                {
                    $dog->setImage(
                        new File($this->getParameter('dogimages_directory').'/'.$dog->getImage())
                    );
                    $filesystem = new Filesystem();
                    $filesystem->remove($dog->getImage());
                }
                $file = $form->get('image')->getData();
                if($file)
                {
                    $originalFileName = pathinfo(
                        $file->getClientOriginalName(),
                        PATHINFO_FILENAME
                    );
                    $safeFileName = $slugger->slug($originalFileName);
                    $newFileName = $safeFileName."_".uniqid().".".$file->guessExtension();
                    try
                    {
                        $file->move(
                            $this->getParameter('dogimages_directory'),
                            $newFileName,
                        );

                    }
                    catch(FileException $e)
                    {

                    }
                    $dog->setImage($newFileName);
                }
            }
            
            $guardians = $form->get('guardian')->getData();  
            foreach($guardians as $guardian)
            {
                $guardian->addGuardianOf($dog);
            }
            $dogRepository->save($dog, true);

            return $this->redirectToRoute('app_dog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dog/edit.html.twig', [
            'dog' => $dog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dog_delete', methods: ['POST'])]
    public function delete(Request $request, Dog $dog, DogRepository $dogRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dog->getId(), $request->request->get('_token'))) {
            if($dog->getImage()!=null)
            {
                $dog->setImage(
                    new File($this->getParameter('dogimages_directory').'/'.$dog->getImage())
                );
                
                $filesystem = new Filesystem();
                $filesystem->remove($dog->getImage());
            }
            $dogRepository->remove($dog, true);
        }

        return $this->redirectToRoute('app_dog_index', [], Response::HTTP_SEE_OTHER);
    }
}
