<?php

namespace App\Repository;

use App\Document\job;
use MongoDB\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;

class JobRepository
{
    private $dm;
    private $repository;

    public function __construct(DocumentManager $documentManager)
    {
        $this->dm = $documentManager;
        $this->repository = $documentManager->getRepository(Job::class);
    }

    public function findByLangage(string $langage): array
    {
        return $this->repository->findBy(['langage' => $langage]);
    }

    public function findByNewest(int $limit = 3): array
    {
        return $this->repository->findBy([], ['createdAt' => 'desc'], $limit);
    }

    public function find200Newest(): array
    {
        return $this->repository->findBy([], ['createdAt' => 'desc'], 200);
    }
    
    public function getPopularityRanking(): array
    { 
        $jobs = $this->find200Newest();
        
        if (empty($jobs)) {
            return [];
        }
        $CompteurLangage = [];
        foreach ($jobs as $job) {
            $langage = $job->getLangage();
            if (!isset($CompteurLangage[$langage])) {
                $CompteurLangage[$langage] = 0;
            }
            $CompteurLangage[$langage]++;
        }
        $totalJobs = count($jobs);
        $langagePercentages = [];
        foreach ($CompteurLangage as $langage => $count) {
            $langagePercentages[$langage] = ($count / $totalJobs) * 100;
        }
        arsort($langagePercentages);
        return $langagePercentages;
    }

    public function LangagesRecherches():array
    {
        $queryBuilder = $this->dm->createQueryBuilder(Job::class);
        $differentLangages = $queryBuilder->distinct('langage')->getQuery()->execute();
        return $differentLangages;
    }

    public function VillesDisponibles(): array
    {
        $queryBuilder = $this->dm->createQueryBuilder(Job::class);
        $differentVilles = $queryBuilder->distinct('localisation')->getQuery()->execute();
        return $differentVilles;
    }


    public function searchUsingLocalisationAndOrJob(string $langage, string $localisation): Iterable
    {   

        $query = [];
        if (!empty($langage)) {
            $query['langage'] = $langage;
        }
        if (!empty($localisation)) {
            $query['localisation'] = $localisation;
        }
        return $this->repository->findBy($query, ['createdAt' => 'desc']);
    }

    public function deleteJob(string $id): void
    {
        $job = $this->repository->find($id);
        if ($job) {
            $this->dm->remove($job);
            $this->dm->flush();
        }
    }

    public function saveJob(Job $job): void
    {
        try{
            $this->dm->persist($job);
            $this->dm->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la sauvegarde du job');
        }
        
    }

    public function getJob(string $id): ?Job
    {
        return $this->repository->find($id);
    }

    

}