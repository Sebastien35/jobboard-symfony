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
        private DocumentManager $documentManager,
        private LoggerInterface $logger
    ) {
        $this->documentManager = $documentManager;
        $this->logger = $logger;
    }


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
        $job = $jobRepository->getJob($id);
        if (!$job) {
            return new Response('Job non trouvé', 404);
        }
        $this->$jobRepository->deleteJob($job);
        return new Response(204);
        } catch (\Exception $e) {
            return new Response('Erreur lors de la suppression', 500);
        }
    }

    #[Route('/jobs/new', name: 'new_job', methods: ['POST'])]
    public function newJob( Request $request): Response
    {
        try{
        $JobToSave = new Job();
        $data=json_decode($request->getContent(), true);
        if(empty($data['name']) || empty($data['localisation']) 
        || empty($data['description']) || empty($data['langage']) 
        || empty($data['contact']))
        {
            return new Response('Tous les champs sont obligatoires', 400);
        }
        $JobToSave
            ->setName(htmlspecialchars($data['name']))
            ->setLocalisation(htmlspecialchars($data['localisation']))
            ->setDescription(htmlspecialchars($data['description']))
            ->setLangage(htmlspecialchars($data['langage']))
            ->setContact(htmlspecialchars($data['contact']))
            ->setCreatedAt(new \DateTime());
        $this->documentManager->persist($JobToSave);
        $this->documentManager->flush();
        return new JsonResponse(['success' => true]);
        } catch (\Exception $erreur) {
            return $this->render('error/500.html.twig', [
                'controller_name' => 'AdminController',
                'error' => $erreur
            ]);

        }
    }

    #[Route('/jobs/edit/{id}', name: 'edit_job', methods: ['GET','PUT'])]
    public function editJob(
        JobRepository $jobRepository,
        Request $request,
        String $id
    ): Response
    {   
        if ($request->isMethod('GET')) {
            $job = $jobRepository->getJob($id);
            return $this->render('admin/editJob.html.twig', [
                'job' => $job
            ]);
        } 
        try{
        $job = $jobRepository->getJob($id);
        $data=json_decode($request->getContent(), true);
        if(empty($data['name']) || empty($data['localisation']) 
        || empty($data['description']) || empty($data['langage']) 
        || empty($data['contact']))
        {
            return new Response('Tous les champs sont obligatoires', 400);
        }
        $job
            ->setName(htmlspecialchars($data['name']))
            ->setLocalisation(htmlspecialchars($data['localisation']))
            ->setDescription(htmlspecialchars($data['description']))
            ->setLangage(htmlspecialchars($data['langage']))
            ->setContact(htmlspecialchars($data['contact']))
            ->setUpdatedAt(new \DateTime());
        
        $this->documentManager->flush();
        return new JsonResponse(['success' => true]);
        } catch (\Exception $erreur) {
            return $this->render('error/500.html.twig', [
                'controller_name' => 'AdminController',
                'error' => $erreur
            ]);
        }
    }
}
