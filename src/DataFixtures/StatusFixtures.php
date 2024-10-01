<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Status;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = ['To Do', 'Doing', 'Done'];

        foreach ($statuses as $statusLabel) {
            $status = new Status();
            $status->setLabel($statusLabel);
            $manager->persist($status);
        }
        $manager->flush();
    }
}
