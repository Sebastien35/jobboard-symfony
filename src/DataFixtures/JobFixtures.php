<?php

namespace App\DataFixtures;

use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Document\job;

class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $villes = [
            'Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice',
            'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille'
        ];

        $langages = [
            'JAVASCRIPT', 'TYPESCRIPT', 'REACT', 'VUE.JS', 'ANGULAR', // Front-end
            'PHP', 'PYTHON', 'JAVA', 'RUBY', 'C#' // Back-end
        ];

        $LoremIpsum='Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, 
        eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. 
        Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, 
        sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. 
        Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, 
        adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. 
        Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? 
        Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?';

        $DecoupeLoremIpsum = preg_split('/,.?/',$LoremIpsum);

        for ($i = 0; $i < 1000; $i++) {
            $RandomDescription = $DecoupeLoremIpsum[array_rand($DecoupeLoremIpsum)];
            $job = new Job();
            $job->setName('Job ' . $i)
                ->setLocalisation($villes[$i % count($villes)])
                ->setDescription($RandomDescription)
                ->setLangage($langages[array_rand($langages)])
                ->setContact('contact' . $i . '@example.com')
                ->setCreatedAt($this->getRandomDate());

            $manager->persist($job);
            echo sprintf("Persisting job %d: %s\n", $i, $job->getName());
        }

        $manager->flush();
    }

    private function getRandomDate(): \DateTime
    {
        $start = new \DateTime('-1 month');
        $end = new \DateTime('-1 day');

        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new \DateTime();
        $randomDate->setTimestamp($randomTimestamp);

        return $randomDate;
    }
}
