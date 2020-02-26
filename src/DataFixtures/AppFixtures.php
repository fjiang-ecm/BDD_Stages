<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Internship;

use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $a1 = new Category();
        $a1->setName("1A");
        $manager->persist($a1);

        $a2 = new Category();
        $a2->setName("2A");
        $manager->persist($a2);

        $s8 = new Category();
        $s8->setName("S8")
            ->setParent($a2);
        $manager->persist($s8);

        $s8_l = new Category();
        $s8_l->setName("S8 Long")
            ->setParent($a2);
        $manager->persist($s8_l);

        $s8_e = new Category();
        $s8_e->setName("S8 Entreprise")
            ->setParent($a2);
        $manager->persist($s8_e);

        $cesure = new Category();
        $cesure->setName("Césure");
        $manager->persist($cesure);

        $a3 = new Category();
        $a3->setName("TFR");
        $manager->persist($a3);

        $user = new User();
        $hash = $this->encoder->encodePassword($user, 'iiiiiiii');

        $user->setUserName("MCF")
            ->setFirstName("MC")
            ->setLastName("F")
            ->setEmail("mcf@mcf.fr")
            ->setPassword($hash)
            ->setRoles(['ROLE_USER']);

        $manager->persist($user);
        
        for ($i = 1; $i <= 10; $i++){
            $user = new User();
            $hash = $this->encoder->encodePassword($user, $faker->password);

            $user->setUserName("user$i")
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash)
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);

            $internship = new Internship();
            $internship->setTitle($faker->jobTitle)
                ->setAuthor($user)
                ->setDescription($faker->text)
                ->setCategory($a1)
                ->setCity($faker->city)
                ->setPostalCode($faker->postcode)
                ->setCountry($faker->country)
                ->setCompany($faker->company)
                ->setContact($faker->name)
                ->setStartedOn($faker->dateTimeThisDecade($max = 'now', $timezone = null))
                ->setFinishedOn($faker->dateTimeBetween($startDate = $internship->getStartedOn(), $endDate = 'now', $timezone = null));

            $manager->persist($internship);

            $internship = new Internship();
            $internship->setTitle($faker->jobTitle)
                        ->setAuthor($user)
                        ->setDescription($faker->text)
                        ->setCategory($a2)
                        ->setCity($faker->city)
                        ->setPostalCode($faker->postcode)
                        ->setCountry($faker->country)
                        ->setCompany($faker->company)
                        ->setContact($faker->name)
                        ->setStartedOn($faker->dateTimeThisDecade($max = 'now', $timezone = null))
                        ->setFinishedOn($faker->dateTimeBetween($startDate = $internship->getStartedOn(), $endDate = 'now', $timezone = null));

            $manager->persist($internship);
        }

        $manager->flush();
    }
}
