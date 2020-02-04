<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Internship;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $a1 = new Category();
        $a1->setName("1A");
        $manager->persist($a1);

        $a2 = new Category();
        $a2->setName("2A");
        $manager->persist($a2);

        $a3 = new Category();
        $a3->setName("3A");
        $manager->persist($a3);

        $info = new Category();
        $info->setName("Info")
            ->setParent($a1);
        $manager->persist($info);

        for ($i = 1; $i <= 10; $i++){
            $user = new User();
            $user->setUserName("user$i")
                ->setFirstName("Prénom$i")
                ->setLastName("Nom$i")
                ->setEmail("prénom$i.nom$i@centrale-marseille.fr")
                ->setPassword("password$i")
                ->setRoles(["user$i"]);

            $manager->persist($user);

            $internship = new Internship();
            $internship->setTitle("Stage n° $i")
                        ->setAuthor($user)
                        ->setDescription("Description du stage n° $i")
                        ->setCategory($info)
                        ->setCity("Marseille")
                        ->setPostalCode("13013")
                        ->setCountry("France")
                        ->setCompany("GInfo")
                        ->setContact("Romain Grondin")
                        ->setStartedOn(new \DateTime())
                        ->setFinishedOn(new \DateTime());

            $manager->persist($internship);
        }

        $manager->flush();
    }
}
