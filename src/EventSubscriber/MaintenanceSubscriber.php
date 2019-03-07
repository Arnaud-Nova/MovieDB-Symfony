<?php
namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/*
 Le but de ce subscriber est de rajouter du code HTML au code généré présent dans la response retourné par le controller
*/

class MaintenanceSubscriber implements EventSubscriberInterface
{

    public function onKernelResponse(FilterResponseEvent $event)
    {
        /*
         L'objet $event du type FilterResponseEvent permet de récuperer un objet du type Response (utilisé a chaque return d'un controller notamment)

         Un objet du type Response permet de manipuler les différentes parties composant une requete HTTP :

         - Le code HTTP souhaité  (200, 404, 403 ...)
         - Le type de requête (text/html, application/json ...)
         - Le body / content à afficher (<html>...</html>)        
       */

    }
    public static function getSubscribedEvents()
    {
       /*
            Décommenter puis définir le type d'évenemment du Kernel (1) le plus approprié sur lequel va être déclenché la fonction onKernelResponse

            Doc : https://symfony.com/doc/current/reference/events.html
        */ 

        return array(
            //KernelEvents::/*1*/ => onKernelResponse,
        );
        
    }
}