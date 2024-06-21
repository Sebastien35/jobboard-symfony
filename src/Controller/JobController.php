<?php

namespace App\Controller;

use App\Repository\JobRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;


use function PHPUnit\Framework\throwException;

#[Route('/jobs', name: 'app_job_')]
class JobController extends AbstractController


{

    public function __construct(
        private SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    #[Route('/langage/{langage}', name:'parlangage', methods:'GET')]
    public function getJobsByLangage(
        string $langage,
        JobRepository $jobRepository): 
        Response {
            try {
                $jobs = $jobRepository->findByLangage($langage);
                return $this->render('job/jobs.html.twig', [
                    'jobs' => $jobs,
                    'langage' => $langage
                ]);
            } catch(\Exception $erreur) {
                throwException($erreur);
                return $this->render('error/500.html.twig', [
                    'error' => $erreur->getMessage()
                ]);
            }
    }
    
    
    #[Route('/popularity', name:'popularity', methods:'GET')]
    public function getPopularityRanking(
        JobRepository $jobRepository): 
        Response {
            try {
                $langagePercentages = $jobRepository->getPopularityRanking();
                return new Response(json_encode($langagePercentages));
            } catch(\Exception $erreur) {
                throwException($erreur);
                return $this->render('error/500.html.twig', [
                    'error' => $erreur->getMessage()
                ]);
            }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(
        Request $request,
        JobRepository $jobRepository,
        SerializerInterface $serializer
    ): Response {
        try {
            $langage = $request->query->get('langage', ''); 
            $localisation = $request->query->get('localisation', ''); 

            $jobs = $jobRepository->searchUsingLocalisationAndOrJob($langage, $localisation);
            $serializedJobs = $serializer->serialize($jobs, 'json', ['attributes' => [
                'id',
                'name',
                'langage',
                'description',
                'localisation',
                'contact',
                'createdAt'
            ]]);
            return new JsonResponse($serializedJobs, JsonResponse::HTTP_OK, [], true);
        } catch (\Exception $erreur) {
            return new JsonResponse([
                'success' => false,
                'error' => $erreur->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
}
