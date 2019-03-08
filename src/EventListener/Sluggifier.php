<?php

namespace App\EventListener;

use App\Entity\Movie;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class Sluggifier
{
    private $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    /*
    Pour chaque event déclaré dans service.yaml pour cette classe, je dois avoir une fonction éponyme de l'évènement concerné
    */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->applySlug($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->applySlug($args);
    }

    private function  applySlug(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only act on some "Movie" entity
        if (!$entity instanceof Movie) {
            return;
        }

        $sluggifiedTitle = $this->slugger->sluggify($entity->getTitle());
        $entity->setSlug($sluggifiedTitle);
    }
}