<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Job;
use App\Entity\Role;
use App\Entity\Team;

use App\Entity\User;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use App\DataFixtures\Faker\MovieAndGenreProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Utils\Slugger;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $slugger;

    /*
     Pour rappel , je ne peux passer un service exterieur dans mon code QUE via le constructeur (hors controller qui sont des services sepciaux)
    */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Slugger $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $em)
    {

        $roleAdmin = new Role();
        $roleAdmin->setCode('ROLE_NOVA_ADMIN');
        $roleAdmin->setName('Admin');

        $roleUser = new Role();
        $roleUser->setCode('ROLE_NOVA_USER');
        $roleUser->setName('Membre');

        $em->persist($roleAdmin);
        $em->persist($roleUser);

        $admin = new User();
        $admin->setEmail('admin@oclock.io');
        $admin->setUsername('admin');
        $admin->setRole($roleAdmin);
        $encodedPassword = $this->passwordEncoder->encodePassword($admin, 'admin');
        $admin->setPassword($encodedPassword);

        $user = new User();
        $user->setEmail('user@oclock.io');
        $user->setUsername('user');
        $user->setRole($roleUser);
        $encodedPassword = $this->passwordEncoder->encodePassword($user, 'user');
        $user->setPassword($encodedPassword);

        $em->persist($admin);
        $em->persist($user);

        $generator = Factory::create('fr_FR');

        //ajout provider custom MovieAndGenreProvider 
        //Note : $generator est attendu dans le constructeur de la classe Base de faker
        $generator->addProvider(new MovieAndGenreProvider($generator));

        $populator = new Faker\ORM\Doctrine\Populator($generator, $em);
        
        /*
         Faker n'apelle pas le constructeur d'origine donc genres n'est pas setté
         -> effet de bord sur adders qui utilise la methode contains sur du null
        */
        $populator->addEntity(
            'App\Entity\Movie', 
            10, 
            array(
                'title' => function() use ($generator) { return $generator->unique()->movieTitle(); },
                'score' => function() use ($generator) { return $generator->numberBetween(0, 5); },
                'summary' => function() use ($generator) { return $generator->paragraph(); },
                'poster' => '',
            ),
            [
                function($movie){ 
                    $slug = $this->slugger->sluggify($movie->getTitle());
                    $movie->setSlug($slug);
                }
            ]
        );
            
        $populator->addEntity('App\Entity\Genre', 20, array(
            'name' => function() use ($generator) { return $generator->unique()->movieGenre(); },
        ));

        $populator->addEntity('App\Entity\Person', 20, array(
            'name' => function() use ($generator) { return $generator->name(); },
        ));
        
        $populator->addEntity('App\Entity\Casting', 50, array(
            'orderCredit' => function() use ($generator) { return $generator->numberBetween(1, 10); },
            'role' => function() use ($generator) { return $generator->firstName(); },
        ));
        
        $populator->addEntity(Department::class, 50, array(
            'name' => function() use ($generator) { return $generator->company(); },
        ));

        $populator->addEntity(Job::class, 50, array(
            'name' => function() use ($generator) { return $generator->jobTitle(); },
        ));

        $populator->addEntity(Team::class, 150);

        $inserted = $populator->execute();

        //generated lists
        $movies = $inserted['App\Entity\Movie'];
        $genres = $inserted['App\Entity\Genre'];

        foreach ($movies as $movie) {

            shuffle($genres);

            // tableau rand en amont => recuperation des 3 premiers donne une valeur unique par rapport a mt rand
            $movie->addGenre($genres[0]);
            $movie->addGenre($genres[1]);
            $movie->addGenre($genres[2]);

            $em->persist($movie);
        }
        $em->flush();
    }
}
