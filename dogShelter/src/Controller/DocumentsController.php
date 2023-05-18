<?php

namespace App\Controller;

use App\Entity\Documents;
use App\Form\DocumentsType;
use App\Repository\DocumentsRepository;
use App\Repository\AdoptionCaseRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/documents')]
class DocumentsController extends AbstractController
{
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/', name: 'app_documents_index', methods: ['GET'])]
    public function index(DocumentsRepository $documentsRepository, AdoptionCaseRepository $adoptionCaseRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('documents/index.html.twig', [
                'documents' => $documentsRepository->findBy(['archived'=>false]),
            ]);
        } 
        elseif ($this->isGranted('ROLE_PRACOWNIK')) {
            $user = $this->getUser();
            /** @var User $user */
            $id = $user->getId();
            $adoptionCases = $adoptionCaseRepository->findEmployeeCases($id);
            
            return $this->render('documents/index.html.twig', [
                'documents' => $documentsRepository->findBy(['adoptionCase'=>$adoptionCases]),
            ]);
        }
        //

    }

    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/new', name: 'app_documents_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DocumentsRepository $documentsRepository, SluggerInterface $slugger): Response
    {
        $document = new Documents();
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('documentSource')->getData();
            if ($file) {
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeName = $slugger->slug($originalName);
                $newName = $safeName . "_" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('documents_directory'),
                        $newName
                    );
                } catch (FileException $e) {
                }
                $document->setDocumentSource($newName);
            }
            if($adoptionCase = $form->get('adoptionCase')->getData())
            {
                $adoptionCase->addDocument($document);   
            }
            //wstawic tutaj dodawanie dokumentu do sprawy adoptionCase addDocument 
            $documentsRepository->save($document, true);
            $this->addFlash('success', 'Pomyślnie dodano plik');
            return $this->redirectToRoute('app_documents_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('documents/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[IsGranted(Documents::VIEW,'document')]
    #[Route('/{id}', name: 'app_documents_show', methods: ['GET'])]
    public function show(Documents $document): Response
    {
        return $this->render('documents/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[IsGranted(Documents::EDIT,'document')]
    #[Route('/{id}/edit', name: 'app_documents_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Documents $document, DocumentsRepository $documentsRepository, SluggerInterface $slugger): Response
    {


        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('documentSource')->getData() != null) {
                if ($document->getDocumentSource() != null) {
                    $document->setDocumentSource(
                        new File($this->getParameter('documents_directory') . '/' . $document->getDocumentSource())
                    );
                    $filesystem = new Filesystem();
                    $filesystem->remove($document->getDocumentSource());
                }
                $file = $form->get('documentSource')->getData();
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeName = $slugger->slug($originalName);
                $newName = $safeName . "_" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('documents_directory'),
                        $newName
                    );
                } catch (FileException $e) {
                }
                $document->setDocumentSource($newName);
            }
            $documentsRepository->save($document, true);

            return $this->redirectToRoute('app_documents_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('documents/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }
   
    #[IsGranted(Documents::DELETE,'document')]
    #[Route('/{id}', name: 'app_documents_delete', methods: ['POST'])]
    public function delete(Request $request, Documents $document, DocumentsRepository $documentsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            if ($document) {
                $document->setDocumentSource(
                    new File($this->getParameter('documents_directory') . '/' . $document->getDocumentSource())
                );

                $filesystem = new Filesystem();
                $filesystem->remove($document->getDocumentSource());
                $documentsRepository->remove($document, true);
            }
        }

        return $this->redirectToRoute('app_documents_index', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/{id}/archive', name: 'app_documents_archive', methods: ['GET', 'POST'])]
    public function archive( Documents $document, DocumentsRepository $documentsRepository): Response
    {
        $document->setarchived(true);
        $documentsRepository->save($document,true);
        return $this->redirectToRoute('app_archives_documents', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted('ROLE_PRACOWNIK')]
    #[Route('/{id}/restore', name: 'app_documents_restore', methods: ['GET', 'POST'])]
    public function restore( Documents $document, DocumentsRepository $documentsRepository): Response
    {
        if($adoptionCase = $document->getAdoptionCase())
        {
            if($adoptionCase->isarchived() == true)
            {
                //blad
                return $this->render('archives/documents.html.twig', [
                    'documents' => $documentsRepository->findBy(['archived'=>true]),
                    'adoptionCase'=>$adoptionCase,
                    'documentToEdit' => $document,
                    'adoptionCaseArchived' => true,
                ]);
            }
            else
            {
                $document->setarchived(false);
                $documentsRepository->save($document,true);
                return $this->redirectToRoute('app_documents_index', [], Response::HTTP_SEE_OTHER);   
            }
        }
        else
        {
            $document->setarchived(false);
            $documentsRepository->save($document,true);
            return $this->redirectToRoute('app_documents_index', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[IsGranted('ROLE_CLIENT')]
    #[Route('/{id}/download', name: 'app_documents_download', methods: ['GET', 'POST'])]
    public function downloadDocument(Documents $document, DocumentsRepository $documentsRepository)
    {
        if($this->getParameter('documents_directory') != NULL && $document->getDocumentSource() != NULL )
        {
            $filesystem = new Filesystem();
            $dir = $this->getParameter('documents_directory') . '/' . $document->getDocumentSource();
            if($filesystem->exists($dir))
            {
                try
                {
                    //download// 
                    return $this->file($dir);
                    //preview
                    // return $this->file($dir, 'preview.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
                }
                catch (FileException $e)
                {
                    
                }
                
            }
            else
            {
                return $this->render('documents/index.html.twig', [
                    'documents' => $documentsRepository->findBy(['archived'=>false]),
                    'errorMessage' => "Plik nie istnieje",
                ]); 
            }  
        }  
    }
    #[IsGranted('ROLE_CLIENT')]
    #[Route('/{id}/preview', name: 'app_documents_preview', methods: ['GET', 'POST'])]
    public function previewDocument(Documents $document, DocumentsRepository $documentsRepository)
    {
        if($this->getParameter('documents_directory') != NULL && $document->getDocumentSource() != NULL )
        {
            $filesystem = new Filesystem();
            $dir = $this->getParameter('documents_directory') . '/' . $document->getDocumentSource();
            if($filesystem->exists($dir))
            {
                try
                {
                    //preview
                    return $this->file($dir, 'preview.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
                }
                catch (FileException $e)
                {
                    
                }    
            }
            else
            {
                return $this->render('documents/index.html.twig', [
                    'documents' => $documentsRepository->findBy(['archived'=>false]),
                    'errorMessage' => "Plik nie istnieje",
                ]); 
            }  
        }  
    }
}
