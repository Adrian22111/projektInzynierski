<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
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
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $password = $form->get('password')->getData();
            // $user->setPassword(
            //     $userPasswordHasher->hashPassword(
            //         $user,
            //         $password
            // ));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'isEdit' => true,
        ]);
    }
    #[Route('/{id}/changepassword', 'app_user_change_password')]
    public function changePassword(Request $request,User $user, UserRepository $users, UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            
            $password = $form->get('password')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
            ));
            $id = $user->getId();
            $users->save($user,true);
            $this->addFlash('success','poprawnie zmieniono hasło');
            return $this->redirectToRoute('app_user_edit',['id'=>$id],Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('user/_change_password.html.twig',[
            'form' => $form
        ]);


    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
