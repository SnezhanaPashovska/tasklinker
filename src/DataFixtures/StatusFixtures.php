<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
