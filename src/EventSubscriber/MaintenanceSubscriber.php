<?php
namespace App\EventSubscriber;

use Twig\Environment as Twig;
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
        // dd($event->getResponse());

        // on stocke le content de la repsonse
        $response = $event->getResponse();
        $content =$response->getContent();
        /*
         L'objet $event du type FilterResponseEvent permet de récuperer un objet du type Response (utilisé a chaque return d'un controller notamment)

         Un objet du type Response permet de manipuler les différentes parties composant une requete HTTP :

         - Le code HTTP souhaité  (200, 404, 403 ...)
         - Le type de requête (text/html, application/json ...)
         - Le body / content à afficher (<html>...</html>)        
       */

       $banner = '<div class="alert alert-danger" role="alert">Maintenance prévue le 07 mars à 23h00</div>';

       // Je remplace ma balise html qui me sert de référence et je la remet après y avoir ajouté ma banière d'alerte
       $updatedContent = str_replace('</header>', '</header>' . $banner, $content);

       $response->setContent($updatedContent);
    }

    public static function getSubscribedEvents()
    {

        return array(
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
}