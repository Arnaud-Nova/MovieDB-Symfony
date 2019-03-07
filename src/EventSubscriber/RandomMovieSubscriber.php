<?php
namespace App\EventSubscriber;
use App\Entity\Movie;
use Twig\Environment as Twig;
use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Controller\MovieController as MovieController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $twig;

    private $movieRepository;

    public function __construct(Twig $twig, MovieRepository $movieRepo)
    {
        $this->twig = $twig;
        $this->movieRepository = $movieRepo;
    }

    /*
     La nomclature de ma fonction doit avoir un nom parlant ayant pour suffix le nom de l'evenemment en question.

     Actuellement je me positionne sur mon controller donc OnSomethingController par ex.

     On peux aussi noter que au niveau de notre docuementation et de l'evenement appelé j'ai un objet dédié pour cet evenemment precis , ici FilterControllerEvent.

     doc : https://symfony.com/doc/current/reference/events.html#kernel-controller
    */
    public function onNovaKernelController(FilterControllerEvent $event)
    {
  
        $controllerAndMethod = $event->getController();

        //dans de rare cas il peux y avoir une closure au lieu d'un tableau donc je ne continue pas 
        if (!is_array($controllerAndMethod)) {
            return;
        }

        //je recupere le nom du controller + le nom de la methode du controller qui va etre appelée
        $controllerName = $controllerAndMethod[0];
        $methodName = $controllerAndMethod[1];

        //je verifie que je suis bien le controller sur lequel je veux effectuer mon action
        // note: si je ne precise pas de methode dans ma condition cette action sera effectuée pour chaque methode du controller concerné
        if ($controllerName instanceof MovieController) {

            //je recupere tout mes film
            $movies = $this->movieRepository->findAll();

            //je recupere une clef aleatoire de mon tableau d'objets
            $randomKey = array_rand($movies);

            //je rstocke mon objet directement en tant que constante twig affecté a la volée
            $this->twig->addGlobal('randomMovie', $movies[$randomKey]);
        }
    }

    /*
     liste des evenements : https://symfony.com/doc/current/reference/events.html
    */
    public static function getSubscribedEvents()
    {
        /*
     
            Doc : https://symfony.com/doc/current/reference/events.html
        */ 
        return array(
            KernelEvents::CONTROLLER => 'onNovaKernelController',
        );
    }
}