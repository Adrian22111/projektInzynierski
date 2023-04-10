<?php

namespace App\Controller;

use PDOException;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Repository\AdoptionCaseRepository;
use App\Repository\DogRepository;
use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
// use Symfony\Component\Config\Definition\Exception\Exception;
use Exception;
use Laminas\Code\Generator\EnumGenerator\Name;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/user')]
class UserController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy(['archived'=>false]),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            //tutaj upload zdjęcia 
            $file = $form->get('profileImage')->getData();
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
                        $this->getParameter('profileimages_directory'),
                        $newFileName,
                    );

                }
                catch(FileException $e)
                {

                }
                $user->setProfileImage($newFileName);
            }
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'isEdit' => false,
        ]);
    }
    #[IsGranted(User::VIEW,'user')]
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, DogRepository $dogRepository): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'dogs' => $dogRepository->findBy(['archived'=>false]),
        ]);
    }
    #[IsGranted(User::EDIT,'user')]
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('profileImage')->getData() != null)
            {
                if($user->getProfileImage()!= null )
                {
                    try
                    {
                        $user->setProfileImage(
                            new File($this->getParameter('profileimages_directory').'/'.$user->getProfileImage())
                        );
                        $filesystem = new Filesystem();
                        $filesystem->remove($user->getProfileImage());
                    }
                    catch(Exception $e)
                    {
                        
                    }
                }
                $file = $form->get('profileImage')->getData();
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
                            $this->getParameter('profileimages_directory'),
                            $newFileName,
                        );

                    }
                    catch(FileException $e)
                    {

                    }
                    $user->setProfileImage($newFileName);
                }
            }
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'isEdit' => true,
        ]);
    }

    #[IsGranted(User::CHANGE_PASSWORD,'user')]
    #[Route('/{id}/changepassword', 'app_user_change_password')]
    public function changePassword(Request $request,User $user, UserRepository $users, UserPasswordHasherInterface $userPasswordHasher, Security $security ): Response
    {

        $form = $this->createForm(ChangePasswordType::class,$user, array(
            'editedUserId' => $user->getId()));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $id = $user->getId();
            if($form->get('oldPassword')->getData())
            {
                $oldPassword = $form->get('oldPassword')->getData();
                $password = $form->get('password')->getData();
                if($userPasswordHasher->isPasswordValid($user, $oldPassword))
                {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $password
                    ));
                    $users->save($user,true);
                    $this->addFlash('success','poprawnie zmieniono hasło');
                }
                else
                {
                    $this->addFlash('failure','nie udało sie zmienić hasła');
                }
            }
            elseif($security->isGranted('ROLE_ADMIN') && $user->getId() != $security->getUser()->getId())
            {
                $password = $form->get('password')->getData();
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password
                ));
                $users->save($user,true);
                $this->addFlash('success','poprawnie zmieniono hasło');
            }
            else
            {
                $this->addFlash('failure','nie udało sie zmienić hasła');
            }
            
            // $oldPasswordHashed = $userPasswordHasher->hashPassword($user,$oldPassword);
            return $this->redirectToRoute('app_user_show',['id'=>$id],Response::HTTP_SEE_OTHER);
            
        }
        return $this->renderForm('user/_change_password.html.twig',[
            'form' => $form,
            'user' => $user,
        ]);


    }
    #[IsGranted(User::EDIT,'user')]
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if($user->getProfileImage()!= null)
            {
                $user->setProfileImage(
                    new File($this->getParameter('profileimages_directory')."/".$user->getProfileImage())
                );
                $filesystem = new Filesystem();
                $filesystem->remove($user->getProfileImage());
            }
            
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted(User::EDIT,'user')]
    #[Route('/{id}/archive', name: 'app_user_archive', methods: ['GET', 'POST'])]
    public function archive(User $user, UserRepository $userRepository, AdoptionCaseRepository $adoptionCaseRepository, DogRepository $dogRepository): Response
    {   
        $userRoles = $user->getRoles();
        // dd($userRoles);
        if(in_array('ROLE_PRACOWNIK',$userRoles) ||in_array('ROLE_ADMIN',$userRoles))
        {
           
            $letArchive = true; 
            $casesToCorrect = []; 
            $adoptionCases = $adoptionCaseRepository->findEmployeeCases($user->getId());
            $dogs = $dogRepository->findDogsByGuardian($user->getId());
            $posts = $user->getPosts();
            // dd($dogs);
            //sprawdzam czy wszystkie aktywne sprawy mają drugiego pracownika, który może sie dalej nimi zajmować
            foreach($adoptionCases as $adoptionCase)
            {
                if(count($userRepository->findEmployeeByCaseId($adoptionCase->getId())) < 2 ) //poprawic metode zeby zwracala tylko niezarchiwizowanych
                {
                    $letArchive = false;
                    $casesToCorrect[] = $adoptionCase;
                }
            }
            foreach($dogs as $dog)
            {
                // dd($dogRepository->getGuardians($dog->getId()));
                if(count($dogRepository->getGuardians($dog->getId())) < 2 )
                {
                    $letArchive = false;
                    $dogsToCorrect[] = $dog;
                }
            }
            //sprawdzam czy wszystkie psy mają drugiego opiekuna 
            if($letArchive == true)
            {
                // archiwizacja usera
                // $user->setarchived(true);
                // foreach($posts as $post)
                // {
                //     $post->setPostOwner(NULL);
                // }
            }
            else
            {
                // dd($dogsToCorrect);
            }

        }
        else
        {
            dd('klient');
        }
        // if()
        // $user->setarchived(true);
        // $userRepository->save($user,true);
        // return $this->redirectToRoute('app_user_index', ['dogsToCorrect' => $dogsToCorrect,'casesToCorrect'=>$casesToCorrect,'letArchive'=>$letArchive], Response::HTTP_SEE_OTHER);
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy(['archived'=>false]),
            'dogsToCorrect' => $dogsToCorrect,
            'casesToCorrect'=>$casesToCorrect,
            'letArchive'=>$letArchive
        ]);
    }
}
