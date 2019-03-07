<?php
namespace App\EventSubscriber;
use App\Entity\Movie;
use Twig\Environment as Twig;
use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Controller\MovieController as MovieController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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

    public function onLunarKernelController(FilterControllerEvent $event)
    {
        $controllerAndMethod = $event->getController();

        if (!is_array($controllerAndMethod)) {
            return;
        }

        $controllerName = $controllerAndMethod[0];
        $methodName = $controllerAndMethod[1];

        if ($controllerName instanceof MovieController && $methodName == 'index') {

            $movies = $this->movieRepository->findAll();
            $randomKey = array_rand($movies);

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
            KernelEvents::CONTROLLER => 'onLunarKernelController',
        );
    }
}