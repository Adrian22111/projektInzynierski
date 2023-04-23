<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\AdoptionCase;
use App\Form\AdoptionCaseType;
use App\Repository\UserRepository;
use App\Repository\DocumentsRepository;
use App\Repository\AdoptionCaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/adoption/case')]
class AdoptionCaseController extends AbstractController
{


    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/', name: 'app_adoption_case_index', methods: ['GET'])]
    public function index(AdoptionCaseRepository $adoptionCaseRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            return $this->render('adoption_case/index.html.twig', [
                'adoption_cases' => $adoptionCaseRepository->findBy(['archived'=>false]),
            ]);
        }
        elseif($this->isGranted('ROLE_PRACOWNIK'))
        {
            /** @var User $User */
            $User = $this->getUser();
            $id = $User->getId();
            return $this->render('adoption_case/index.html.twig', [
                'adoption_cases' => $adoptionCaseRepository->findEmployeeCases($id),
            ]);
        }

    }

    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/new', name: 'app_adoption_case_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdoptionCaseRepository $adoptionCaseRepository, UserRepository $users): Response
    {
        $adoptionCase = new AdoptionCase();
        $form = $this->createForm(AdoptionCaseType::class, $adoptionCase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $form->get('client')->getData();
                $user->setAvailable(false);
                $dog = $form->get('dog')->getData();
                $dog->setInAdoption(true);
                $adoptionCaseRepository->save($adoptionCase, true);
            


            return $this->redirectToRoute('app_adoption_case_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('adoption_case/new.html.twig', [
            'adoption_case' => $adoptionCase,
            'form' => $form,
        ]);
    }

    #[IsGranted(AdoptionCase::VIEW,'adoptionCase')]
    #[Route('/{id}', name: 'app_adoption_case_show', methods: ['GET'])]
    public function show(AdoptionCase $adoptionCase): Response
    {
        return $this->render('adoption_case/show.html.twig', [
            'adoption_case' => $adoptionCase,
        ]);
    }

    #[IsGranted(AdoptionCase::EDIT,'adoptionCase')]
    #[Route('/{id}/edit', name: 'app_adoption_case_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdoptionCase $adoptionCase, AdoptionCaseRepository $adoptionCaseRepository): Response
    {
        $client = $adoptionCase->getClient();
        // $client->setAvailable(true);
        // $adoptionCase->setClient($client);
        $dog = $adoptionCase->getDog();
        // $dog->setInAdoption(false);
        // $adoptionCase->setDog($dog);
        // $adoptionCaseRepository->save($adoptionCase,true);
        

        $form = $this->createForm(AdoptionCaseType::class, $adoptionCase,array(
            'clientId' => $client->getId(),
            'dogId' =>$dog->getId()));
        $form->handleRequest($request);

             
        if ($form->isSubmitted() && $form->isValid()) {

            $adoptionCaseRepository->save($adoptionCase,true);
            return $this->redirectToRoute('app_adoption_case_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('adoption_case/edit.html.twig', [
            'adoption_case' => $adoptionCase,
            'form' => $form,
        ]);
        

    }
    #[IsGranted(AdoptionCase::DELETE,'adoptionCase')]
    #[Route('/{id}', name: 'app_adoption_case_delete', methods: ['POST'])]
    public function delete(Request $request, AdoptionCase $adoptionCase, AdoptionCaseRepository $adoptionCaseRepository, DocumentsRepository $documentsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adoptionCase->getId(), $request->request->get('_token'))) {
            $client = $adoptionCase->getClient();
            $client->setAvailable(true);
            $documents = $adoptionCase->getDocuments();
            $dog = $adoptionCase->getDog();
            $dog->setInAdoption(false);
            foreach($documents as $document)
            {
                $documentsRepository->remove($document);
            }
            $adoptionCaseRepository->remove($adoptionCase, true);
        }

        return $this->redirectToRoute('app_adoption_case_index', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/archive', name: 'app_adoption_case_archive', methods: ['GET', 'POST'])]
    public function archieve(AdoptionCase $adoptionCase, AdoptionCaseRepository $adoptionCaseRepository,): Response
    {
        $dog = $adoptionCase->getDog();
        $documents = $adoptionCase->getDocuments();
        $dog->setarchived(true);
        $client = $adoptionCase->getClient();
        $client->setAvailable(true);
        foreach($documents as $document)
        {
            $document->setarchived(true);
            
        }
        $adoptionCase->setarchived(true);
        $adoptionCaseRepository->save($adoptionCase,true);

        return $this->redirectToRoute('app_adoption_case_index', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted('ROLE_CLIENT')]
    #[Route('/{id}/client-cases', name: 'app_adoption_case_show_client_cases', methods: ['GET'])]
    public function showClientCases(AdoptionCaseRepository $adoptionCaseRepository, $id): Response
    {
            return $this->render('adoption_case/client_cases_list.html.twig', [
                'adoption_cases' => $adoptionCaseRepository->findBy(['archived'=>false, 'client'=> $id]),
            ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/restore', name: 'app_adoption_case_restore', methods: ['GET', 'POST'])]
    public function restore(AdoptionCase $adoptionCase, AdoptionCaseRepository $adoptionCaseRepository,): Response
    {
        $dog = $adoptionCase->getDog();
        $adoptionCases = $adoptionCaseRepository->findBy(['dog'=>$dog->getId(), 'archived'=>false]);
        $clientNotAvailable = false;
        $allEmployeesArchived = true;
        // dd($adoptionCasesCount);
        //sprawdzam czy pies nie ma już jakiejś aktywnej sprawy
        if(count($adoptionCases) == 0)
        {
            
            $client = $adoptionCase->getClient();
            $employees = $adoptionCase->getEmployee();
            if($client->isarchived() == true || $client->isAvailable() == false)
            {
                $clientNotAvailable = true;
            }
            foreach($employees as $employee)
            {
                if($employee->isarchived() == false and $employee->isAvailable() == true)
                {
                    $allEmployeesArchived = false;
                }
            }
            if($clientNotAvailable == true or $allEmployeesArchived == true)
            {
                
                // przekierowanie do błedu
                return $this->render('archives/adoption_cases.html.twig', [
                    'adoption_cases' => $adoptionCaseRepository->findBy(['archived'=>true]),
                    'adoptionCaseToRestore' => $adoptionCase,
                    'clientNotAvailable' => $clientNotAvailable,
                    'allEmployeesArchived'=> $allEmployeesArchived,
                ]);
            }
            else
            {
                //przywracanie
                $dog->setarchived(false);
                $dog->setInAdoption(true);
                $adoptionCase->setarchived(false);
                $documents = $adoptionCase->getDocuments();
                foreach($documents as $document)
                {
                    $document->setarchived(false);
                }
                $adoptionCaseRepository->save($adoptionCase,true);
            }
        }
        else
        {
            return $this->render('archives/adoption_cases.html.twig', [
                'adoption_cases' => $adoptionCaseRepository->findBy(['archived'=>true]),
                'activeAdoptionCases' => $adoptionCases,
                'dogHasActiveAdoptionCase'=> true,
            ]);
        }
        return $this->redirectToRoute('app_adoption_case_index', [], Response::HTTP_SEE_OTHER);
    }
    
        
}
