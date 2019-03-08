<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MovieRepository;

class MovieCommand extends Command
{
    protected static $defaultName = 'app:nova:updateposter';
    private $em;
    private $repository;

    //recuperer le service repository me permettant de recuperer la liste des mes movies
    // recuperer aussi le service manager me permettant de mettre a jour mes films
    public function __construct(EntityManagerInterface $em, MovieRepository $repository)
    {

        // necessaire a la commande si constructeur custom
        parent::__construct();
        $this->em = $em;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
        ->setDescription('Update les posters de ma table Movie')
        ->setHelp('Cette commande est géniale');

        // permet de rajouter un ou plusieurs argument en entrée de ma console
        //->addArgument('nova', InputArgument::REQUIRED, 'The username of the user.');
    }

    /*
     cette fonction contient le code executé lors de l'appel de ma commande

     InputInterface permet de recuperer les parametre passé a ma commande via la console

     OutputInterface permet d'afficher du texte / donéne pour communiquer avec mon utilisateur
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Update Poster begin !',
            '============',
            '',
        ]);

        //recuperer toute la liste des films puis inserer l'url retournée en BDD
        //note : pour ne pas se telescoper avec la propriété poster actuelle , creer une nouvelle propriété 

         // Attention à remplacer les espaces presents dans nos titre grace à l'expression suivante        
        // $sanitizedTitle = str_replace(" ", "+", $movie->getTitle());
        $movies = $this->repository->findAll();

        foreach ($movies as $movie) {
            $title = str_replace(" ", "+", $movie->getTitle());
            $response = $this->getCurl($title);
            $movie->setPosterUrl($response->Poster);
            // $this->em->persist($movie); inutile en modification => création uniquement
            $output->writeln([
                $movie->getTitle(),
                $response->Poster,
                '************',
            ]);
        }
        $this->em->flush();

        $output->writeln([
            'Update Poster End !',
            '============',
            '',
        ]);
    }

    //modifier la fonction getcurl poru prendre dynamiquement le titre du film en parametre
    private function getCurl($title){

        $dbmovieApiUrl = 'http://www.omdbapi.com/?apikey=55429286&t=' . urlencode($title);

        //lorsque j'utilise cUrl , je dois d'abord initialiser la connexion
        $curl = curl_init();

        //je passe a minimam ces 2 option de connexion, a savoir l'url que je souhaite appeler (1) et que je souhaite que curl m'affiche le retour (2)
        curl_setopt($curl, CURLOPT_URL, $dbmovieApiUrl); //1
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //2
        
        //j'execute la configuration precedente initlisée sur curl pour obtenir une reponse
        $jsonResponse = curl_exec($curl);
        $response = json_decode($jsonResponse);
// dump($response);
        //je ferme la connexion à l'url (important)
        curl_close($curl);

        if(isset($response->Response) && $response->Response == "False"){
            $response = null;
        }
       
        return $response;
    }
}