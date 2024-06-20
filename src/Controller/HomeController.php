<?php

namespace App\Controller;

use App\Repository\JobRepository; // Import the JobRepository class
use Doctrine\DBAL\Query\Limit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        JobRepository $jobRepository

    ): Response
    {   
        $langages = [
            'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Angular', // Front-end
            'PHP', 'Python', 'Java', 'Ruby', 'C#' // Back-end
        ];

        $jobs = $jobRepository->findByNewest(10);
        $randomlangage = $langages[array_rand($langages)];



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'jobs' => $jobs,
            'randomlangage' => $randomlangage
        ]);
    }
}
