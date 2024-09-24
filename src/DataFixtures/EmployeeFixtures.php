<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeImmutable;

class EmployeeFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $employee1 = new Employee();

        $employee1->setName('John')
                 ->setLastName('Doe')
                 ->setEmail('john.doe@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee1, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 //->addProject($project1)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-15'));
        $manager->persist($employee1);

        $employee2 = new Employee();
        $employee2->setName('Jane')
                 ->setLastName('Smith')
                 ->setEmail('jane.smith@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee2, 'password123'))
                 ->setRole(0)
                 ->setContract('CDD')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-03-20'));
        $manager->persist($employee2);

        $employee3 = new Employee();
        $employee3->setName('Alice')
                 ->setLastName('Brown')
                 ->setEmail('alice.brown@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee3, 'password123'))
                 ->setRole(0)
                 ->setContract('Freelance')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2024-08-01'));
        $manager->persist($employee3);

        $employee4 = new Employee();
        $employee4->setName('Olivier')
                 ->setLastName('Dubois')
                 ->setEmail('olivier.dubois@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee4, 'password123'))
                 ->setRole(0)
                 ->setContract('Freelance')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2022-10-15'));
        $manager->persist($employee4);

        $employee5 = new Employee();
        $employee5->setName('Lucie')
                 ->setLastName('Bernard')
                 ->setEmail('lucie.bernard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee5, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-22'));
        $manager->persist($employee5);

        $employee5 = new Employee();
        $employee5->setName('Lucie')
                 ->setLastName('Bernard')
                 ->setEmail('lucie.bernard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee5, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-22'));
        $manager->persist($employee5);

        $employee5 = new Employee();
        $employee5->setName('Lucie')
                 ->setLastName('Bernard')
                 ->setEmail('lucie.bernard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee5, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-22'));
        $manager->persist($employee5);

        $employee5 = new Employee();
        $employee5->setName('Lucie')
                 ->setLastName('Bernard')
                 ->setEmail('lucie.bernard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee5, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-22'));
        $manager->persist($employee5);


        $manager->flush();
    }
}
