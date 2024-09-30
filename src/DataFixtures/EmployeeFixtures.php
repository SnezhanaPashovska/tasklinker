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

        $employee1->setName('Jean')
                 ->setLastName('Dupont')
                 ->setEmail('jean.dupont@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee1, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 //->addProject($project1)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-15'));
        $manager->persist($employee1);

        $employee2 = new Employee();
        $employee2->setName('Jeanne')
                 ->setLastName('Smith')
                 ->setEmail('jeanne.smith@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee2, 'password123'))
                 ->setRole(0)
                 ->setContract('CDD')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-03-20'));
        $manager->persist($employee2);

        $employee3 = new Employee();
        $employee3->setName('Alice')
                 ->setLastName('Bardot')
                 ->setEmail('alice.bardot@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee3, 'password123'))
                 ->setRole(0)
                 ->setContract('Freelance')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2024-08-01'));
        $manager->persist($employee3);

        $employee4 = new Employee();
        $employee4->setName('Olivier')
                 ->setLastName('Dubois')
                 ->setEmail('olivier.dubois@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee4, 'password123'))
                 ->setRole(0)
                 ->setContract('Freelance')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2022-10-15'));
        $manager->persist($employee4);

        $employee5 = new Employee();
        $employee5->setName('Lucie')
                 ->setLastName('Bernard')
                 ->setEmail('lucie.bernard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee5, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-22'));
        $manager->persist($employee5);

        $employee6 = new Employee();
        $employee6->setName('Julien ')
                 ->setLastName('Martin')
                 ->setEmail('julien.martin@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee6, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-25'));
        $manager->persist($employee5);

        $employee7 = new Employee();
        $employee7->setName('Claire ')
                 ->setLastName('Dupont')
                 ->setEmail('claire.dupont@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee7, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-10-22'));
        $manager->persist($employee7);

        $employee8 = new Employee();
        $employee8->setName('Émilie ')
                 ->setLastName('Leroy')
                 ->setEmail('emilie.leroy@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee8, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2024-01-12'));
        $manager->persist($employee8);


        $employee9 = new Employee();
        $employee9->setName('Antoine ')
                 ->setLastName('Lefèvre')
                 ->setEmail('antoine.lefevre@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee9, 'password123'))
                 ->setRole(0)
                 ->setContract('CDD')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-07-29'));
        $manager->persist($employee9);

        $employee10 = new Employee();
        $employee10->setName('Sophie ')
                 ->setLastName('Moreau')
                 ->setEmail('sophie.moreau@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee10, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2022-01-02'));
        $manager->persist($employee10);

        $employee11 = new Employee();
        $employee11->setName('Lucas ')
                 ->setLastName('Dubois')
                 ->setEmail('lucas.dubois@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee11, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-11-14'));
        $manager->persist($employee11);

        $employee12 = new Employee();
        $employee12->setName('Chloé')
                 ->setLastName('Rousseau')
                 ->setEmail('chloe.rousseau@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee12, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(true)
                 ->setArrivalDate(new DateTimeImmutable('2023-12-22'));
        $manager->persist($employee12);

        $employee13 = new Employee();
        $employee13->setName('Thomas ')
                 ->setLastName('Gerard')
                 ->setEmail('thomas.gerard@example.com')
                 ->setPassword($this->passwordHasher->hashPassword($employee13, 'password123'))
                 ->setRole(0)
                 ->setContract('CDI')
                 ->setActive(false)
                 ->setArrivalDate(new DateTimeImmutable('2021-06-22'));
        $manager->persist($employee13);


        $manager->flush();
    }
}
