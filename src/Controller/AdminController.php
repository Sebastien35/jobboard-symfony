<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Document\Job;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Repository\JobRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Monolog\LoggerInterface as Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{   
    public function __construct(
    ) {}


    #[Route('/', name: 'index')]
    public function index(
        JobRepository $jobRepository
    ): Response
    {
        $differentsLangages = $jobRepository->LangagesRecherches();
        $differentesVilles = $jobRepository->VillesDisponibles();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'differentsLangages' => $differentsLangages,
            "differentesVilles" => $differentesVilles
        ]);
    }


    #[Route('/jobs/delete/{id}', name: 'delete_job', methods: ['DELETE'])]
    public function deleteJob(
        JobRepository $jobRepository,
        string $id
    ): Response
    {   
        try{
        $jobRepository->deleteJob($id);
        return $this->redirectToRoute('app_admin_index');
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_admin_index');
        }
    }

    #[Route('/jobs/new', name: 'new_job', methods: ['POST'])]
    public function newJob(
        JobRepository $jobRepository,
        Request $request,
        DocumentManager $documentManager,
        LoggerInterface $logger
    ): Response
    {
        try{
        
        $JobToSave = new Job();
        $data=json_decode($request->getContent(), true);
        $JobToSave
            ->setName($data['name'])
            ->setLocalisation($data['localisation'])
            ->setDescription($data['description'])
            ->setLangage($data['langage'])
            ->setContact($data['contact'])
            ->setCreatedAt(new \DateTime());

        dd($JobToSave);
        $documentManager->persist($JobToSave);
        $documentManager->flush();
        $logger->info('Job persisted successfully: ' . json_encode($JobToSave));

        return new JsonResponse(['success' => true]);
        } catch (\Exception $erreur) {
            return $this->render('error/500.html.twig', [
                'controller_name' => 'AdminController',
                'error' => $erreur
            ]);

        }
    }
}
