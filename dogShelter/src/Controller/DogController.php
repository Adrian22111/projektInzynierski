<?php

namespace App\Controller;

use Exception;
use App\Entity\Dog;
use App\Form\DogType;
use App\Entity\AdoptionCase;
use Doctrine\ORM\Mapping\Id;
use App\Repository\DogRepository;
use App\Repository\UserRepository;
use App\Repository\AdoptionCaseRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/dog')]
class DogController extends AbstractController
{
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/', name: 'app_dog_index', methods: ['GET'])]
    public function index(DogRepository $dogRepository): Response
    {
        return $this->render('dog/index.html.twig', [
            'dogs' => $dogRepository->findBy(['archived'=>false]),
        ]);
    }

    #[Route('/all', name: 'app_dog_all', methods: ['GET'])]
    public function showAllDogs(DogRepository $dogRepository): Response
    {
        return $this->render('dog/all_dogs.html.twig', [
            'dogs' => $dogRepository->findBy(['archived'=> false, 'inAdoption' => false,]),
            // 'guardians' => $dogRepository->getGuardians();
        ]);
    }
    #[IsGranted('ROLE_PRACOWNIK')]
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
    public function show(Dog $dog, UserRepository $userRepository, $id): Response
    {
        return $this->render('dog/show.html.twig', [
            'dog' => $dog,
            'guardians' => $userRepository->findActiveGuardians($id),
        ]);
    }
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/{id}/edit', name: 'app_dog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dog $dog, DogRepository $dogRepository, SluggerInterface $slugger, UserRepository $users): Response
    {
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('image')->getData() != null)
            {
                if($dog->getImage() != null)
                {
                    try
                    {
                        $dog->setImage(
                            new File($this->getParameter('dogimages_directory').'/'.$dog->getImage())
                        );
                        $filesystem = new Filesystem();
                        $filesystem->remove($dog->getImage());
                    }
                    catch(Exception $e)
                    {

                    }

                }
                 $file = $form->get('image')->getData();
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
            $dogId = $dog->getId();
            $currentGuardiansId = $dogRepository->getGuardians($dogId);
            foreach($currentGuardiansId as $currentGuardian)
            {
                $currentGuardian->removeGuardianOf($dog);
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
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/{id}', name: 'app_dog_delete', methods: ['POST'])]
    public function delete(Request $request, Dog $dog, DogRepository $dogRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dog->getId(), $request->request->get('_token'))) {
            if($dog->getAdoptionCase()!= null)
            {
                $this->addFlash('failure','rozstrzygnij najpierw sprawę adopcji');
                return $this->redirectToRoute('app_adoption_case_show',['id'=>$dog->getAdoptionCase()->getId()]);
            }
            else
            {
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

        }

        return $this->redirectToRoute('app_dog_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/{id}/archive', name: 'app_dog_archive', methods: ['GET', 'POST'])]
    public function archieve(Dog $dog, DogRepository $dogRepository): Response
    {
        if($adoptionCase = $dog->getAdoptionCase())
        {
            
            $client = $adoptionCase->getClient();
            $adoptionCase->setarchived(true);
            $client->setAvailable(true);
            if($documents = $adoptionCase->getDocuments())
            {
                foreach($documents as $document)
                {
                    $document->setarchived(true);
                }
            }
        }
        $dog->setarchived(true);
        $dogRepository->save($dog,true);
        return $this->redirectToRoute('app_dog_index', [], Response::HTTP_SEE_OTHER);
    }
}
